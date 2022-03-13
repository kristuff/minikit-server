<?php

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.16 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */


namespace Kristuff\Minikit\Auth\Controller;

use Kristuff\Minikit\Mvc\Application;
use Kristuff\Minikit\Auth\Model\UserLoginModel;
use Kristuff\Minikit\Auth\Model\UserRecoveryModel;
use Kristuff\Minikit\Auth\Model\UserRegistrationModel;
use Kristuff\Minikit\Auth\Model\UserInvitationModel;

/**
 * Class AuthController
 *
 */
abstract class AuthController extends \Kristuff\Minikit\Auth\Controller\BaseController
{
    /** 
     * Handle the user login process
     * 
     * End points                                      Method   Description                         
     * ----------                                      ------   -----------                       
     * /auth/signin                                     GET     Gets the signin page 
     * /auth/signin/perform                             POST    Receives and check the signin infos. Redirect to
     *                                                          login page if it fails. Redirect to previous page 
     *                                                          (if set in param) or home page 
     *
     * @access public
     * @static   
     * @param string            $action                 The action (handle 'perform' or null) 
     *
     * @return mixed            
     */
    public function signin($action = null)
    {
        // prevent access to this method when user is logged in
        $this->redirectAlreadyLoggedUserToHome();

        switch ($action){
            case '':
                $data = UserLoginModel::getPreLoginData();
                $this->view->renderHtml(Application::config('AUTH_LOGIN_VIEW_FILE'), $data, 
                                        Application::config('AUTH_LOGIN_VIEW_TEMPLATE'));
                break;

            case 'perform': 
                // perform the login method, put result (true or false) into $login_successful
                $loginResult = UserLoginModel::login(
                    $this->request()->post('user_name_or_email', true),
                    $this->request()->post('user_password', true),
                    $this->request()->postCheckbox('set_remember_me_cookie'),
                    $this->request()->post('token'));

                // check login status: if true, then redirect user home or previous page, 
                // if false, then to login form again
                if ($loginResult->success()) {
                    $redirectTo =  $this->request()->post('redirect') ? $this->request()->post('redirect')  : ''; 
                    $this->redirect(rtrim(Application::getUrl(), '/') . $redirectTo, 303);
                    break;
                }

                // set feedback 
                $loginResult->toFeedback();
                $this->redirectLogin();
                break;
                
            default :
                $this->redirectHome();
        }
    }
    
    /** 
     * Logout
     * 
     * clear all data in session, cookie, update database then 
     * redirect home and exit.

     * End points                                       Method  Description                         
     * ----------                                       -----   -----------                       
     * /auth/logout                                     GET     logout 
   
     * @access public
     * @return void           
     */
    public function logout()
    {
        UserLoginModel::logout();
        $this->redirectHome();
    }
        
    /**
     * Handle user password recovery
     * 
     * End points                                       Method  Description                         
     * ----------                                       -----   -----------                       
     * /auth/recovery                                   GET     Gets the recovery page 
     * /auth/recovery/captcha                           GET     output the captcha image
     * /auth/recovery/request                           POST    Receives recovery infos (name/email + captcha)   
     * /auth/recovery/verify/{userName}/{actionHash}    GET     Receives verify userName / Hash from email link. 
     *                                                          if verified, renders the reset form view
     * /auth/recovery/reset                             POST    Receives and sets the new password
     * 
     * @access public
     * @static   
     * @param string            $action                 The action (handle 'request', 'verify', 'reset' or null) 
     * @param string            $userName               The user's name.
     * @param string            $actionhash             The password reset hash
     *
     * @return mixed            
     */
    public function recovery($action = null, $userName = null, $actionHash = null)
    {
        // returns false (=>404) if the recovery process is not enabled
        if (! UserRecoveryModel::isRecoveryEnabled()){
            return false;
        }
        
        // prevent access to this method when user is logged in
        $this->redirectAlreadyLoggedUserToHome();
        
        switch ($action){

            case 'request':
                // handle the password recovery request
                $result = UserRecoveryModel::verifyPasswordRecoveryRequest(
                     $this->request()->post('user_name_or_email', true), 
                     $this->request()->post('captcha_value'));
                
                // set feedback and redirect to login page
                $result->toFeedback();
                $this->redirectLogin();
                break;

            case 'verify':
                // check if this the provided verification code fits the user's verification code
                // and set result in feedback 
                $result = UserRecoveryModel::verifyPasswordResetLink($userName, $actionHash);
                $result->toFeedback();
                
                // if the validation passed render reset password view
                if ($result->success()) {
                    $this->view->setData('userName', $userName);
                    $this->view->setData('userPasswordResetHash', $actionHash);
                    $this->view->renderHtml(Application::config('AUTH_PASSWORD_RESET_VERFIFED_VIEW_FILE'), [], 
                                            Application::config('AUTH_PASSWORD_RESET_VERFIFED_VIEW_TEMPLATE'));
                    break;
                }
                 
                // otherwise, redirect login
                $this->redirectLogin();
                break;

            case 'reset':
                // Set the new password
                $result = UserRecoveryModel::handlePasswordReset(
                        $this->request()->post('user_name', true), 
                        $this->request()->post('user_password_reset_hash', true),
                        $this->request()->post('user_password_new', true), 
                        $this->request()->post('user_password_repeat', true));
                $result->toFeedback();
                $this->redirectLogin();
                break;
            
            case 'captcha':
                UserRecoveryModel::outputCaptcha();
                break;
    
            default:
                // get the recovery request view
                $this->view->renderHtml(Application::config('AUTH_PASSWORD_RESET_REQUEST_VIEW_FILE'), [], 
                                        Application::config('AUTH_PASSWORD_RESET_REQUEST_VIEW_TEMPLATE'));
        }
    }

    /**
     * Handles user invitation process
     * 
     * End points                                       Method  Description                         
     * ----------                                       -----   -----------                       
     * /auth/invite/verify/{userId}/{actionHash}        GET     Verifies userName / Hash from email link. 
     *                                                          if verified, renders the reset form view
     * /auth/invite/register/{userId}/{actionHash}      POST    Receives user info to register
     * 
     * @access public
     * @static   
     * @param string            $action                 The action (handle 'verify' or 'register') 
     * @param string            $userId                 The user's id
     * @param string            $actionhash             The invite process verification hash
     *
     * @return mixed            
     */
    public function invite($action = null, $userId = null, $actionHash = null)
    {
        // returns false (->404) if the invitation process is not enabled
        if (! UserInvitationModel::isInvitationEnabled()){
            return false;
        }

        // prevent access to this method when user is logged in
        $this->redirectAlreadyLoggedUserToHome();
        

        switch ($action){

            // complete user after activation mail link opened
            case 'verify':
                $verifyUrlprocess = UserInvitationModel::verifyInvitedUser($userId, $actionHash);
                 
                // render complete.view if successfull
                if ($verifyUrlprocess->success()){
                    $data = ['userId' => (int) $userId, 'actionHash' => $actionHash];
                    $this->view->renderHtml(Application::config('AUTH_INVITATION_COMPLETE_VIEW_FILE'), $data, 
                                            Application::config('AUTH_INVITATION_COMPLETE_VIEW_TEMPLATE'));
                    break;
                }
                
                // Otherwise, redirect to home
                $verifyUrlprocess->toFeedback();
                $this->redirectHome();
                break;

            // register 
            case 'register':
               
               $verifyUrlprocess = UserInvitationModel::verifyInvitedUser($userId, $actionHash);
               if ($verifyUrlprocess->success()){

                    $result = UserInvitationModel::completeRegistration($userId, 
                                $this->request()->post('user_name', true),
                                $this->request()->post('user_password', true),                    
                                $this->request()->post('user_password_repeat', true),
                                $actionHash);
                
                    $result->toFeedback();
                
                    // redirect login if successfull
                    if ($result->success()){
                        $this->redirectLogin();
                        break;
                    }
               }
                // else redirect home page
                $this->redirectHome();
                break;
            
            default:
                $this->redirectHome();

        }
    }

    /**
     * Handles user invitation process
     * 
     * End points                                       Method  Description                         
     * ----------                                       -----   -----------                       
     * /auth/signup                                     GET     Get the signup view
     * /auth/signup/captcha                             GET     Output the captacha
     * /auth/signup/request                             POST    Receives user info to register
     * /auth/signup/verify/{userId}/{actionHash}        GET     Receives verify userName / Hash from email link. 
     *                                                          if verified, redirect to home page (otherwise return to login)
     * 
     * @access public
     * @static   
     * @param string            $action                 The action (handle 'request' or 'verify' or 'captcha) 
     * @param string            $userId                 The user's id
     * @param string            $actionhash             The signup process verification hash
     *
     * @return mixed            
     */
    public function signup($action = null, $userId = null, $actionHash = null)
    {
        // returns false (->404) if the signup process is not enabled
        if (!UserRegistrationModel::isRegistrationEnabled()){
            return false;
        }

        // prevent access to this method when user is logged in
        $this->redirectAlreadyLoggedUserToHome();

        switch ($action){

            case 'request':
                $result = UserRegistrationModel::handleRegistrationRequest(
                                $this->request()->post('user_name', true ),
                                $this->request()->post('user_email', true),
                                $this->request()->post('user_email_repeat', true),
                                $this->request()->post('user_password', true),                    
                                $this->request()->post('user_password_repeat', true),
                                $this->request()->post('captcha_value', true));
                
                $result->toFeedback();
                $this->redirectLogin();
                break;

            case 'verify':
                // verify user after activation mail link opened
                $result = UserRegistrationModel::verifyRegisteredUser($userId, $actionHash);
                $result->toFeedback();
                
                // redirect login if successful
                if ($result->success()){
                    $this->redirectLogin();
                    break;
                }

                // else redirect home page
                $this->redirectHome();
                break;

            case 'captcha':
                UserRegistrationModel::outputCaptcha();
                break;
    
            default:
                // request registration page
                $this->view->renderHtml(Application::config('AUTH_SIGNUP_VIEW_FILE'), [], 
                                        Application::config('AUTH_SIGNUP_VIEW_TEMPLATE'));
        }
    }
}