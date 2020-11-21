<?php

/** 
 *        _      _            _
 *  _ __ (_)_ _ (_)_ __ _____| |__
 * | '  \| | ' \| \ V  V / -_) '_ \
 * |_|_|_|_|_||_|_|\_/\_/\___|_.__/
 *
 * This file is part of Kristuff\MiniWeb.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @version    0.9.1
 * @copyright  2017-2020 Kristuff
 */

namespace Kristuff\Miniweb\Auth\Controller;

use Kristuff\Miniweb\Http\Request;
use Kristuff\Miniweb\Mvc\TaskResponse;
use Kristuff\Miniweb\Auth\Model\UserModel;
use Kristuff\Miniweb\Auth\Model\UserAdminModel;
use Kristuff\Miniweb\Auth\Model\UserLoginModel;
use Kristuff\Miniweb\Auth\Model\UserEditModel;
use Kristuff\Miniweb\Auth\Model\UserAvatarModel;
use Kristuff\Miniweb\Auth\Model\UserSettingsModel;
use Kristuff\Miniweb\Auth\Model\UserInvitationModel;

/** 
 * Class Api Controller
 * This controller contains methods to access/control application data
 * Try to respect the REST API standards
 *
 *  ----------------------------        ------      -------------------------------------   -------------       --------
 *  End points                          Method      Description                             parameters(s)       Response
 *  ----------------------------        ------      -------------------------------------   -------------       --------
 *  /api/users                          GET         Get user list                                               200 (OK), 403 (no admin)
 *  /api/users                          POST        Create or invite a user                                     201 (created)   
 *  /api/users/{userId}                 GET         Get a user by id
 *  /api/users/{userId}                 DELETE      Delete a user   
 *  /api/users/{userId}/suspend         PUT         Update user's supension status          supensionDays     
 *  /api/users/{userId}/delete          PUT         Soft dete a user     
 *  /api/users/{userId}/undelete        PUT         Update user's deletion  status           
 *  /api/users/{userId}/settings        GET         Get the given user's setting   
 *  /api/users/{userId}/settings        PUT         Upadte a setting value                  parameter/value
 *  /api/users/{userId}/settings        DELETE      Reset the user's settings to defaults  
 *  ----------------------------        ------      -------------------------------------   -------------       --------
 * 
 *  Possible response codes and outpout format by method:
 *  -------------                      ---     ----    ---     ------      ------
 *  Response code                      GET     POST    PUT     DELETE      format
 *  -------------                      ---     ----    ---     ------      ------
 *  200 (OK)                            X       -       X        -          JSON
 *  201 (Created)                       -       X       -        -          JSON
 *  204 (Empty)                         X       -       -        X          JSON 
 *  400 (bad requests)                  X       X       X        X          JSON
 *  401 (not allowed, require login)    X       X       X        X          JSON
 *  403 (not allowed, denied)           X       X       X        X          JSON
 *  405 (Method Not Allowed)            X       X       X        X          JSON
 *  500 (internal error)                X       X       X        X          JSON
 *  -------------                      ---     ----    ---     ------      ------
 * 
 */
class ApiController extends BaseController
{
    protected $token = '';            // The api token
    protected $tokenKey = 'api';      // The api token key 
    protected $response = null;       // The default api response


    /** 
     * Constructor
     * Check authentification and token
     */
    public function __construct(string $apiTokenKey = 'api')
    {
        parent::__construct();

        // api need auth
        if (!UserLoginModel::isUserLoggedIn() || !UserLoginModel::isSessionValid()) {
            $this->view->renderJson(TaskResponse::create(401, $this->text('ERROR_INVALID_AUTHENTFICATION'))->toArray(), 401);
            exit();
        } 

        // api need a token
        $token = $this->request()->arg('token') ? $this->request()->arg('token') : null;
        if (!$this->token()->isTokenValid($token, $apiTokenKey)) {
            $this->view->renderJson(TaskResponse::create(401, $this->text('ERROR_INVALID_TOKEN'))->toArray(), 401);
            exit();
        }

        // store the token
        $this->token =    $token;  
        $this->tokenKey = $apiTokenKey;  
        
        // the defaut response (invalid)
        $this->response = TaskResponse::create(400,  $this->text('ERROR_INVALID_REQUEST'));
    }
      
    /** 
     * Default controller returns an error 
     * 
     * @access private
     * @return void      
     */
    public function index()
    {
        $this->view->renderJson($this->response->toArray(), $this->response->code());
    }
    
    /** 
     * Users api end point
     * 
     *  End points                          Method      Description                             parameters(s)       Response
     *  ---------                           ------      -----------                             -------------       --------
     *  /api/users                          GET         Get user list                                               200 (OK), 403 (no admin)
     *  /api/users                          POST        Create or invite a user                                     201 (created)   
     *  /api/users                          PUT         N/A                                                         405 (not allowed) 
     *  /api/users/{userId}                 GET         Get a user by id
     *  /api/users/{userId}                 DELETE      Delete a user   
     *  /api/users/{userId}/suspend         PUT         Update user's supension status          supensionDays     
     *  /api/users/{userId}/delete          PUT         Soft dete a user     
     *  /api/users/{userId}/undelete        PUT         Update user's deletion  status           
     *  /api/users/{userId}/settings        GET         Get the given user's setting   
     *  /api/users/{userId}/settings        PUT         Upadte a setting value                  parameter/value
     *  /api/users/{userId}/settings        DELETE      Reset the user's settings to defaults  
     */
    public function users($userId = null, $action = null)
    {
        // In case userId has 'self' value, replace it with the real userId
        $userId = ($userId === 'self') ? $this->session()->get("userId") : $userId;
              
        switch ($this->request()->method()){
            
            // get users list
            case Request::METHOD_GET:
                $offset = $this->request()->arg('offset') ? (int) $this->request()->arg('offset')  : 0;  
                $limit  = $this->request()->arg('limit')  ? (int) $this->request()->arg('limit')   : 20; 
                $order  = $this->request()->arg('order')  ?? 'name';  

                $this->response = $action == 'settings' ?  UserSettingsModel::getUserSettings($userId) :  
                                                           UserModel::getProfiles($userId, $limit, $offset, $order);
                break;
            
            // create a user 
            case Request::METHOD_POST:
                
                $action    = $this->request()->arg('action')    ?? 'create';
                $userEmail = $this->request()->arg('userEmail') ?? null; 
                       
                switch ($action){

                    case 'invite':
                        // invite a new user
                        $this->response = UserInvitationModel::inviteNewUser($userEmail, $this->token, $this->tokenKey);
                        break;

                    case 'create':
                        // create a new user
                        //todo
                        $userEmailRepeat    = $this->request()->arg('userEmailRepeat')    ?? null; 
                        $userName           = $this->request()->arg('userName')           ?? null; 
                        $userPassword       = $this->request()->arg('userPassword')       ?? null; 
                        $userPasswordRepeat = $this->request()->arg('userPasswordRepeat') ?? null; 
                        $this->response = UserAdminModel::createNewAccount($userName, $userEmail, $userPassword, 
                                                                           $userPasswordRepeat, $this->token, $this->tokenKey);
                        break;
                }
                break;
            
            // delete a user 
            case Request::METHOD_DELETE:
                $this->response = $action == 'settings' ? UserSettingsModel::resetUserSettings($userId, $this->token, $this->tokenKey) : 
                                                          UserAdminModel::deleteUserAndSettings($userId, $this->token, $this->tokenKey);
                break;
            
            // update user                       
            case Request::METHOD_PUT:
                switch ($action){
                    case 'suspend':
                        $suspensionDays = $this->request()->arg('suspensionDays') ? (int) $this->request()->arg('suspensionDays') : null;
                        $this->response = UserAdminModel::updateSuspensionStatus($userId, $this->token, $this->tokenKey, $suspensionDays, false);
                        break;
                    
                    // undelete a user 
                    case 'undelete':
                        $this->response =  UserAdminModel::updateDeletionStatus($userId, $this->token, $this->tokenKey, false) ;
                        break;  
                    
                    // soft delete 
                    case 'delete':
                         $this->response =  UserAdminModel::updateDeletionStatus($userId, $this->token, $this->tokenKey, true) ;
                        break;

                    case 'settings':
                        $param = $this->request()->arg('parameter')  ?? null;  
                        $value = $this->request()->arg('value')      ?? null;
                        $this->response = UserSettingsModel::editUserSettings($userId, $param, $value, $this->token, $this->tokenKey); 
                        break;
                }
                break;                   
        }

        // render response
        $this->view->renderJson($this->response->toArray(), $this->response->code());
    }

}