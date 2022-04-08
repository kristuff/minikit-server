<?php

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.19 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */

namespace Kristuff\Minikit\Auth\Controller;

use Kristuff\Minikit\Http\Server;
use Kristuff\Minikit\Mvc\Application;
use Kristuff\Minikit\Auth\Model\UserLoginModel;
use Kristuff\Minikit\Auth\Controller\BaseController;

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