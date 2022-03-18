<?php

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.17 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */


namespace Kristuff\Minikit\Auth\Controller;

use Kristuff\Minikit\Mvc\Application;
use Kristuff\Minikit\Auth\Model\UserLoginModel;
use Kristuff\Minikit\Auth\TextHelper;

/**
 * Class Controller
 *
 * Extends Minikit\Mvc\Controller with auth functionality 
 */
abstract class BaseController extends \Kristuff\Minikit\Mvc\Controller
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


        // if user is not logged and the login with cookie is enabled and cookie is set, 
        $loginCookie = $this->cookie()->get('remember_me');
        if (!UserLoginModel::isUserLoggedIn() && 
            Application::config('AUTH_LOGIN_COOKIE_ENABLED') === true && 
            isset($loginCookie)) {
            
            // try to login with cookie
            $loginProccess = UserLoginModel::loginWithCookie($loginCookie);
       
            if ($loginProccess->success()){

                // load minimal user data. include api token
                foreach (UserLoginModel::getPostLoginData() as $key => $value){
                    $this->view->setData($key, $value);
                }

                // load user settings
                foreach ($this->session()->get('userSettings') as $key => $value){
                    $this->view->setData($key, $value);
                }
            }

            // set the login result in feedback
            $loginProccess->toFeedback();
        }

        // set basic data
        $this->view->setData('userIsLoggedIn', UserLoginModel::isUserLoggedIn());
        $this->view->setData('userIsAdmin',    UserLoginModel::isUserLoggedInAndAdmin());
    }
 
    /** 
	 * Redirect to home page and exit
     *
     * @access protected
     * @return void
	 */
    protected function redirectHome()
    {
        $this->redirect(Application::getUrl(), 302, true);
    }

    /** 
	 * Redirect to login page and exit
     *
     * @access protected
     * @return void
	 */
    protected function redirectLogin()
    {
        $this->redirect(Application::getUrl() . Application::config('AUTH_LOGIN_URL'), 302, true);
    }

    /** 
	 * Redirect no admin user to home page
     *
     * @access protected
     * @return void
	 */
    protected function redirectNoAdminToHome()
    {
        if ( !UserLoginModel::isUserLoggedInAndAdmin() ){
            $this->redirectHome();
        }
    }

    /** 
	 * Redirect no admin user to home page
     *
     * @access protected
     * @return void
	 */
    protected function redirectAlreadyLoggedUserToHome()
    {
        if (UserLoginModel::isUserLoggedIn()){
            $this->redirectHome();
        }
    }

    /**
     * Gets/returns the locale value for the given key (localized apps)
     * Looking for an overide text in the main locale file 
     * then look in the default texts dist with this library  
     * 
     * @access public
     * @param  string   $key        The key
     * @param  string   $locale     The locale to use (the default is used if null). (optional)
     *
     * @return mixed    The key value is the key exists, otherwise null.
     */
    protected function text(string $key, $locale = null)
    {
        return TextHelper::text($key, $locale);
    }      
}