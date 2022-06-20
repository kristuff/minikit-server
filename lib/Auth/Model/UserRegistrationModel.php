<?php

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.22 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */

namespace Kristuff\Minikit\Auth\Model;

use Kristuff\Minikit\Auth\Model\UserModel;
use Kristuff\Minikit\Mail\Mailer;
use Kristuff\Minikit\Mail\EmailBuilder;
use Kristuff\Minikit\Mvc\TaskResponse;
use Kristuff\Minikit\Mvc\Application;
use Kristuff\Minikit\Security\CaptchaModel;
use Kristuff\Minikit\Auth\Data\UsersCollection;

/** 
 * Class RegistrationModel
 *
 * Handles registration process.
 */
class UserRegistrationModel extends UserModel
{
    /** 
     * Gets whether the registration process is enabled or not
     * 
     * @access public
     * @static
     *
     * @return bool         True if the the registration process is enabled, otherwise false.
     */
    public static function isRegistrationEnabled()
    {
        return self::config('AUTH_SIGNUP_ENABLED') === true; 
    }

    /**
     * Create and output the registration captcha 
     *
     * @access public
     * @static
     *
     * @return mixed 
     */
    public static function outputCaptcha()
    {
        CaptchaModel::captcha()->createAndOutput('AUTH_SIGNUP_captcha');
    }
        
    /**
     * Handle registraion request
     * 
     * Handles the registration process for DEFAULT users: creates a new user in the database and send 
     * an email to user to confirm its account.
     *
     * @access public
     * @static
     * @param string    $userName               The user's name.
     * @param string    $userEmail              The user's email address.
     * @param string    $userEmailRepeat        The repeated user's email address.
     * @param string    $userPassword           The user's password.
     * @param string    $userPasswordRepeat     The repeated user's password.
     * @param string    $captcha                The captacha value.
     *
     * @return TaskResponse
     */
    public static function handleRegistrationRequest($userName, $userEmail, $userEmailRepeat, $userPassword, $userPasswordRepeat, $captcha)
    {
        // the return response
        $response = TaskResponse::create();
        
        // clean the inputs
        $userName           = strip_tags($userName);
        $userEmail          = strip_tags($userEmail);
        $userEmailRepeat    = strip_tags($userEmailRepeat) ;

        // validate the inputs
        self::validateRegistrationCaptcha($response, $captcha);
        self::validateUserNamePattern($response, $userName);
        self::validateUserNameNoConflict($response, $userName);
        self::validateUserEmailPattern($response, $userEmail, $userEmailRepeat);
        self::validateUserEmailNoConflict($response, $userEmail);
        self::validateUserPassword($response, $userPassword, $userPasswordRepeat);

        // stop registration flow if anything breaks the input check rules
        // else continue...
        if ($response->success()) {

            // crypt the password with the PHP 5.5's password_hash() function, results in a 60 character hash string.
            // @see php.net/manual/en/function.password-hash.php for more, especially for potential options
            // and generate random hash for email verification (40 char bytes)
            $userPasswordHash = password_hash($userPassword, PASSWORD_DEFAULT);
            $userActivationHash = bin2hex(random_bytes(40));

            // write user data to database
            if ($response->assertTrue(UsersCollection::insertUnregisteredUser($userEmail, $userName, $userPasswordHash, $userActivationHash), 500, 
                                       self::text('USER_NEW_ACCOUNT_ERROR_CREATION_FAILED'))){

                // get user_id of the user that has been created, to keep things clean we DON'T use lastInsertId() here
                $userId = UsersCollection::getUserIdByUsername($userName);
                if ($response->assertTrue($userId !== false, 500, self::text('UNKNOWN_ERROR'))){

                    // send verification email
                    $mailSent = self::sendVerificationEmail($userId, $userEmail, $userActivationHash);
                    if (!$response->assertTrue($mailSent, 500, self::text('USER_NEW_ACCOUNT_MAIL_SENDING_ERROR'))){

                        // if verification email sending failed: instantly delete the user
                        UsersCollection::deleteUserById($userId);
                        return $response;
                    }
                    
                    // load settings
                    $response->assertTrue(UserMetaModel::loadDefaultSettings(self::database(), (int) $userId), 500, self::text('USER_NEW_ACCOUNT_ERROR_DEFAULT_SETTINGS'));

                    // set success message and return response
                    $response->setMessage(self::text('USER_NEW_ACCOUNT_SUCCESSFULLY_CREATED'));
                }
            }
        }

        // return response
        return $response;
    }
    
    /**
     * Verify registered user
     * 
     * Checks the id/verification code combination and set the user's activation status to true in the database
     *
     * @access public
     * @static
     * @param  int       $userId                 The user's id
     * @param  string    $userActivationHash     The user's mail verification hash string
     *
     * @return TaskResponse
     */
    public static function verifyRegisteredUser($userId, $userActivationHash)
    {
        $response = TaskResponse::create();
        $response->assertTrue(UsersCollection::validateRegistrationHash($userId, $userActivationHash), 500, self::text('USER_NEW_ACCOUNT_ACTIVATION_FAILED'))
            && $response->setMessage(self::text('USER_NEW_ACCOUNT_ACTIVATION_SUCCESSFUL'));

        return $response;
    }
    
    /**
     * Validates the registration captcha
     *  
     * Return true if the captcha is valid
     *
     * @access protected
     * @static
     * @param  TaskResponse    $response               The response instance.
     * @param  string           $captcha                The captcha value
     *
     * @return bool
     */
    protected static function validateRegistrationCaptcha(TaskResponse $response, $captcha)
    {
        $isvalid = \Kristuff\Minikit\Security\CaptchaModel::captcha()->validate($captcha, 'AUTH_SIGNUP_captcha');
        return $response->assertTrue($isvalid, 400, self::text('ERROR_INVALID_CAPTCHA'));
    }

    /**
     * Sends the verification email to confirm the account.
     *
     * @access protected
     * @static
     * @param int       $userId                 The user's id
     * @param string    $userEmail              The user's email
     * @param string    $userActivationHash     The user's mail verification hash string
     *
     * @return bool     
     */
    protected static function sendVerificationEmail($userId, $userEmail, $userActivationHash)
    {
        $useHtml          = self::isHtmlEmailEnabled();
        $appName          = self::config('APP_NAME') ;
        $mailSubject      = sprintf(self::text('USER_SIGNUP_EMAIL_VERIFICATION_SUBJECT'), $appName, Application::getUrl());
        $intro            = self::text('USER_SIGNUP_EMAIL_VERIFICATION_INTRO');
        $message          = self::text('USER_SIGNUP_EMAIL_VERIFICATION_LINK_MESSAGE');
        $linkTitle        = self::text('USER_SIGNUP_EMAIL_VERIFICATION_LINK_TITLE');
        $linkUrl          = Application::getUrl().self::config('AUTH_SIGNUP_EMAIL_VERIFICATION_URL') . '/' . urlencode($userId) . '/' . urlencode($userActivationHash);
        $politePhrase     = self::text('AUTH_EMAIL_POLITE_PHRASE');
        $mailSignature    = sprintf(self::text('AUTH_EMAIL_SIGNATURE'), $appName);
        $mailCopyright    = "Copyright ". (date("Y"))." ".self::config('APP_COPYRIGHT');

        if ($useHtml){
            $builder =  EmailBuilder::getEmailBuilder();
            EmailBuilder::createHeader($builder, $mailSubject, '');
            EmailBuilder::createContent($builder, [$intro, $message]);
            EmailBuilder::createButton($builder, $linkTitle, $linkUrl);
            EmailBuilder::createContent($builder, [$politePhrase, $mailSignature]);
            EmailBuilder::createFooter($builder, $appName, $mailCopyright);
            $content = $builder->getHtml();

        } else {
            $content  = $intro ;
            $content .= PHP_EOL ;
            $content .= PHP_EOL ;
            $content .= $message ;
            $content .= PHP_EOL ;
            $content .= PHP_EOL ;
            $content .= $linkTitle . PHP_EOL . $linkUrl ;
            $content .= PHP_EOL ;
            $content .= PHP_EOL ;
            $content .= $politePhrase ;
            $content .= PHP_EOL ;
            $content .= $mailSignature;
            $content .= PHP_EOL ;
            $content .= PHP_EOL ;
            $content .= $appName . ' | ' . $mailCopyright ;
        }

        $mail = new Mailer();
        $mailSent = $mail->sendMail($userEmail, 
            self::config('AUTH_EMAIL_FROM_EMAIL'),
            self::config('AUTH_EMAIL_FROM_NAME'), 
            $mailSubject, 
            $content, 
            $useHtml
        );

        return $mailSent ? true : false;
    }
}