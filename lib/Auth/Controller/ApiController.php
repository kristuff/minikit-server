<?php

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.23 
 * Copyright (c) 2017-2023 Christophe Buliard  
 */

namespace Kristuff\Minikit\Auth\Controller;

use Kristuff\Minikit\Mvc\Application;
use Kristuff\Minikit\Mvc\TaskResponse;
use Kristuff\Minikit\Http\Request;
use Kristuff\Minikit\Http\RequestMethod as HTTP;
use Kristuff\Minikit\Auth\Model\UserModel;
use Kristuff\Minikit\Auth\Model\UserAdminModel;
use Kristuff\Minikit\Auth\Model\UserLoginModel;
use Kristuff\Minikit\Auth\Model\UserEditModel;
use Kristuff\Minikit\Auth\Model\UserAvatarModel;
use Kristuff\Minikit\Auth\Model\UserSettingsModel;
use Kristuff\Minikit\Auth\Model\UserInvitationModel;
use Kristuff\Minikit\Auth\Model\UserMetaModel;

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
 *  /api/profile                        POST        Edit user name or email   
 *  /api/profile/name                   POST        Edit user name   
 *  /api/profile/email                  POST        Edit user email   
 *  /api/profile/avatar                 POST        Edit user avatar 
 *  /api/profile/avatar/delete          POST        Delete user avatar 
 *  /api/profile/password               POST        Edit user password
 *  ----------------------------        ------      -------------------------------------   -------------       --------
 * 
 *  Possible response codes and outpout format by method:
 *  -------------                      ---     ----    ---     ------      ------
 *  Response code                      GET     POST    PUT     DELETE      format
 *  -------------                      ---     ----    ---     ------      ------
 *  200 (OK)                            X       -       X        -          JSON
 *  201 (Created)                       -       X       -        -          JSON
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
     *
     * Check authentification and token
     * 
     * @access public
     * @param Application $application        The application instance
     */
    public function __construct(Application $application, string $apiTokenKey = 'api')
    {
        parent::__construct($application);

        // api need auth
        if (!UserLoginModel::isUserLoggedIn() || !UserLoginModel::isSessionValid()) {
            $this->response = TaskResponse::create(401, $this->text('ERROR_INVALID_AUTHENTFICATION'));
            $this->json();
            exit();
        } 

        // api need a token
        $token = $this->request()->arg('token') ? $this->request()->arg('token') : null;
        if (!$this->token()->isTokenValid($token, $apiTokenKey)) {
            $this->response = TaskResponse::create(401, $this->text('ERROR_INVALID_TOKEN'));
            $this->json();
            exit();
        }

        // store the token
        $this->token =    $token;  
        $this->tokenKey = $apiTokenKey;  
        
        // the defaut response (invalid)
        $this->response = TaskResponse::create(400,  $this->text('ERROR_INVALID_REQUEST'));
    }

    /** 
     * Render reponse as Json
     * 
     * @access private
     * @return void      
     */
    private function json()
    {
        $this->view->renderJson($this->response->toArray(), $this->response->code());
    }

    /** 
     * Default controller action returns an error 
     * 
     * @access private
     * @return void      
     */
    public function index()
    {
        $this->json();
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
        $route = $this->request()->method().'/users'.($userId ? '/{userId}' : '') . ($action ? '/'.$action : ''); 
        
        switch ($route){
            case HTTP::METHOD_GET.'/users':
                $offset         = $this->request()->arg('offset') ? (int) $this->request()->arg('offset')  : 0;  
                $limit          = $this->request()->arg('limit')  ? (int) $this->request()->arg('limit')   : 20; 
                $order          = $this->request()->arg('order')  ?? 'name';  
                $this->response = UserModel::getProfiles($limit, $offset, $order);
                break;

            case HTTP::METHOD_GET.'/users/{userId}/settings':
                $offset         = $this->request()->arg('offset') ? (int) $this->request()->arg('offset')  : 0;  
                $limit          = $this->request()->arg('limit')  ? (int) $this->request()->arg('limit')   : 20; 
                $order          = $this->request()->arg('order')  ?? 'name';  
                $this->response = UserMetaModel::getUserMeta($userId);
                break;
         
            case Request::METHOD_POST.'/users':
                $action    = $this->request()->arg('action')    ?? 'create';
                $userEmail = $this->request()->arg('user_email') ?? null; 
                       
                switch ($action){
                    case 'invite':
                        // invite a new user
                        $this->response = UserInvitationModel::inviteNewUser($userEmail, $this->token, $this->tokenKey);
                        break;

                    case 'create':
                        // create a new user
                        $userEmailRepeat    = $this->request()->arg('user_email_repeat')    ?? null;   //todo
                        $userName           = $this->request()->arg('user_name')            ?? null; 
                        $userPassword       = $this->request()->arg('user_password')        ?? null; 
                        $userPasswordRepeat = $this->request()->arg('user_password_repeat') ?? null; 
                        $this->response = UserAdminModel::createNewAccount($userName, $userEmail, $userPassword, 
                                                                           $userPasswordRepeat, $this->token, $this->tokenKey);
                        break;
                }
                break;
            
            case Request::METHOD_DELETE.'/users/{userId}/settings':
                $this->response =   UserMetaModel::resetUserSettings($userId, $this->token, $this->tokenKey);
                break;

            case Request::METHOD_DELETE.'/users/{userId}':
                $this->response = UserAdminModel::deleteUserAndSettings($userId, $this->token, $this->tokenKey);
                break;
            
            case Request::METHOD_PUT.'/users/{userId}/suspend':
                $suspensionDays = $this->request()->arg('suspension_days') ? (int) $this->request()->arg('suspension_days') : null;
                if (!empty($suspensionDays)) {
                    $this->response = UserAdminModel::updateSuspensionStatus($userId, $this->token, $this->tokenKey, $suspensionDays, false);
                } 
                break;
            
            case Request::METHOD_PUT.'/users/{userId}/undelete':
                $this->response =  UserAdminModel::updateDeletionStatus($userId, $this->token, $this->tokenKey, false) ;
                break;  
            
            // soft delete
            case Request::METHOD_PUT.'/users/{userId}/delete':
                $this->response =  UserAdminModel::updateDeletionStatus($userId, $this->token, $this->tokenKey, true) ;
                break;

            case Request::METHOD_PUT.'/users/{userId}/settings':
                $param = $this->request()->arg('parameter')  ?? null;  
                $value = $this->request()->arg('value')      ?? null;
                $this->response = UserMetaModel::editUserSettings($userId, $param, $value, $this->token, $this->tokenKey); 
                break;                   
        }

        $this->json();
    }

    /** 
     * Profile api end points
     *
     *  End points                              Method      Description                         parameters(s)       
     *  ----------------------------            ------      ------------------------------      ---------------------------------
     *  /api/profile                            POST        Edit user name or email             user_name, user_email, token 
     *  /api/profile/name                       POST        Edit user name                      user_name, token 
     *  /api/profile/nicename                   POST        Edit user nice name                 user_nice_name, token 
     *  /api/profile/email                      POST        Edit user email                     ser_email, token 
     *  /api/profile/avatar                     POST        Edit user avatar                    token, USER_AVATAR_file
     *  /api/profile/avatar/delete              POST        Delete user avatar                  token
     *  /api/profile/password                   POST        Edit user password                  user_password_current, .._new, .._repaet
     */
    public function profile($process = '', $parameter = '')
    {
        // accept only POST requests
        if ($this->request()->method() === Request::METHOD_POST) {

            switch ($process){
                case '':
                case 'all':
                    $this->response = UserEditModel::editCurrentUserNameOrEmail(
                        $this->request()->post('user_name', true),
                        $this->request()->post('user_nice_name', true),
                        $this->request()->post('user_email', true),
                        $this->request()->post('token'),
                        $this->tokenKey);
                    break;                     

                case 'email': 
                    $this->response = UserEditModel::editCurrentUserEmail(
                        $this->request()->post('user_email'),
                        $this->request()->post('token'),
                        $this->tokenKey);
                    break;

                case 'nicename': 
                    $this->response = UserEditModel::editCurrentUserNiceName(
                        $this->request()->post('user_nice_name'),
                        $this->request()->post('token'),
                        $this->tokenKey);
                    break;
                    
                case 'name': 
                    $this->response = UserEditModel::editCurrentUserName(
                        $this->request()->post('user_name'),
                        $this->request()->post('token'),
                        $this->tokenKey);
                    break;

                case 'password': 
                    $this->response =  UserEditModel::editCurrentUserPassword(
                        $this->request()->post('user_password_current'),
                        $this->request()->post('user_password_new'),
                        $this->request()->post('user_password_repeat'),
                        $this->request()->post('token'),
                        $this->tokenKey);
                    break;
            
                case 'avatar': 
                    $this->response = ($parameter === 'delete') ? 
                        UserAvatarModel::deleteCurrentUserAvatar($this->request()->post('token'), $this->tokenKey) : 
                        UserAvatarModel::createCurrentUserAvatar($this->request()->post('token'), $this->tokenKey);
                    break;
            }
        }

        $this->json();
   }
}