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
 * @version    0.9.7
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Auth\Controller;

use Kristuff\Miniweb\Http\Server;
use Kristuff\Miniweb\Mvc\Application;
use Kristuff\Miniweb\Auth\Model\UserLoginModel;
use Kristuff\Miniweb\Auth\Controller\BaseController;

/**
 * Class PrivateController
 *
 * Extends BaseController for logged in users only.
 * A no logged user will be redirect to login page
 */
abstract class PrivateController extends BaseController
{
    /**
     * Constructor
     *
     * @access public
     * @param Application $application        The application instance
     */
    public function __construct(Application $application)
    {
        parent::__construct($application);

        // redirect no logged in users to login page
        if (!UserLoginModel::isUserLoggedIn()){
            $this->session()->destroy();
            $this->redirect(Application::getUrl() . 
                            Application::config('AUTH_LOGIN_URL').'?redirect=' . 
                            urlencode(Server::requestUri()));
        }

        // check for concurrency session / suspended or deleted account
        if (UserLoginModel::isUserLoggedIn() && !UserLoginModel::isSessionValid()) {
            
            // perform logout then redirect home
            UserLoginModel::logout();
            $this->redirectHome();
        }

        // load minimal user data. include api token
        foreach (UserLoginModel::getPostLoginData() as $key => $value){
            $this->view->setData($key, $value);
        }

        // load user settings
        foreach ($this->session()->get('userSettings') as $key => $value){
            $this->view->setData($key, $value);
        }
    }
}