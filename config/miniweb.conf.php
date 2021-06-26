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
 * @version    0.9.8
 * @copyright  2017-2021 Kristuff
 */

/** 
 * Returns the default configuration.
 */
return array(

     /**
     * ------------------------------
     * Configuration for: application
     * ------------------------------
     */

    /* TODO  */
    'APP_NAMESPACE'             => '',  
    'CONFIG_PATH'               => __DIR__ . '/',
    'CONFIG_DEFAULT_PATH'       => __DIR__ . '/default/',

    /**
     * -----------------------------
     * Configuration for: MVC SYSTEM
     * -----------------------------
     * 
     * CONTROLLER_NAMESPACE:        Your controller namespace 
     * CONTROLLER_PATH:             The full path to controllers. Must be defined in real application
     * CONTROLLER_EXTENSION:        The 'relative' name of controller (without .php extension). Default is 'Controller'
     *                              (Controller for 'index' will be named 'IndexController' in 'IndexController.php').
     * CONTROLLER_UCWORDS:          True if the first letter of controller must be in uppercase. Default is True
     *                              (Controller for index we be 'Index')
     * CONTROLLER_DEFAULT:          The default controller to use.
     * CONTROLLER_DEFAULT_ACTION:   The default action to call inside the default controller. Default is 'index' for name 
     *                              and action, that means a controller index() method inside Index controler.
     * ERROR_CONTROLLER:            Your error controller
     * VIEW_PATH:                   The full path to views. Must be defined in real application
     */

    'CONTROLLER_NAMESPACE'          => '',  
    'CONTROLLER_PATH'               => '',
    'CONTROLLER_EXTENSION'          => 'Controller',
    'CONTROLLER_UCWORDS'            => true,
    'CONTROLLER_DEFAULT'            => 'index',
    'CONTROLLER_ACTION_DEFAULT'     => 'index',
    'ERROR_CONTROLLER'              => '', 
    'VIEW_PATH'                     => '',

    /** 
     * --------------------------
     * Configuration for: DATA
     * --------------------------
     *
     */
    'DATA_PATH'                     => __DIR__ . '/../data/',
    'DATA_CONFIG_PATH'              => __DIR__ . '/../data/config/',
    'DATA_DB_PATH'                  => __DIR__ . '/../data/db/',
    'DATA_LOG_PATH'                 => __DIR__ . '/../data/logs/',    
 
    /**
     * ------------------------
     * Configuration for: TOKEN
     * ------------------------
     * 
     * TOKEN_VALIDITY:          The token validity in seconds. Default is 86400 (1 day).
     */
    'TOKEN_VALIDITY'                => 86400,

    /**
     * --------------------------
     * Configuration for: COOKIES
     * --------------------------
     * 
     * COOKIE_RUNTIME:          The cookie validity in second. 1209600 seconds = 2 weeks
     * COOKIE_PATH:             The path the cookie is valid on, usually '/' to make it valid on the whole domain.
     *                          @see http://stackoverflow.com/q/9618217/1114320
     *                          @see php.net/manual/en/function.setcookie.php
     * COOKIE_DOMAIN:           The (sub)domain where the cookie is valid for. Usually this does not work with 'localhost',
     *                          '.localhost', '127.0.0.1', or '.127.0.0.1'. If so, leave it as empty string, false or null.
     *                          When using real domains make sure you have a dot (!) in front of the domain if you want to, like ".mydomain.com". 
     *                          Older browsers still implementing the deprecated » RFC 2109 may require a leading . to match all subdomains. 
     *                          @see http://php.net/manual/en/function.setcookie.php#73107
     *                          @see http://stackoverflow.com/questions/2285010/php-setcookie-domain
     *                          @see http://stackoverflow.com/questions/1134290/cookies-on-localhost-with-explicit-domain
     * COOKIE_SECURE:           If the cookie will be transferred through secured connection(SSL). It's highly recommended 
     *                          to set it to true if you have secured connection.
     * COOKIE_HTTP:             If set to true, Cookies that can't be accessed by JS - Highly recommended!
     * SESSION_RUNTIME:         How long should a session cookie be valid by seconds, 604800 = 1 week.
     */
    'COOKIE_RUNTIME'            => 1209600,
    'COOKIE_PATH'               => '/',
    'COOKIE_DOMAIN'             => '',
    'COOKIE_SECURE'             => false,
    'COOKIE_HTTP'               => true,
    'SESSION_RUNTIME'           => 604800,
    'COOKIE_SAMESITE'           => 'Strict',

    /**
     * -----------------------------
     * Configuration for: ENCRYPTION
     * -----------------------------
     * 
     * ENCRYPTION_KEY, HMAC_SALT:   Currently used to encrypt and decrypt publicly visible values, like the user id in
     *                              the cookie. Change these values for increased security, but don't touch if you have 
     *                              no idea what this means.
     */
    'ENCRYPTION_KEY'            => '6#x0gÊìf^25cL1f$08&',
    'HMAC_SALT'                 => '8qk9c^4L6d#15tM8z7n0%',

    /** 
     * -----------------------------
     * Configuration for: AUTH EMAIL
     * -----------------------------
     *
     * AUTH_EMAIL_HTML:              True to use HTML email for auth process (registration, recovery, ...)
     * AUTH_EMAIL_FROM_EMAIL:        The email address of the sender
     * AUTH_EMAIL_FROM_NAME:         The name of the email sender
     */
    'AUTH_EMAIL_HTML'               => false,
    'AUTH_EMAIL_FROM_EMAIL'         => 'no-reply@EXAMPLE.COM',
    'AUTH_EMAIL_FROM_NAME'          => 'The EXAMPLE.COM team',
   
    /** 
     * --------------------------
     * Configuration for: LOGIN
     * --------------------------
     *
     * AUTH_LOGIN_COOKIE_ENABLED:   true to allow login with cookie (see also configuration for cookie)
     */
    'AUTH_LOGIN_URL'                => 'auth/signin',
    'AUTH_LOGIN_VIEW_FILE'          => 'auth/login.view.php',
    'AUTH_LOGIN_VIEW_TEMPLATE'      => 'auth',
    'AUTH_LOGIN_COOKIE_ENABLED'     => false,
   
    /**
     * -------------------------------------
     * Configuration for: PASSWORD RECOVERY
     * -------------------------------------
     */
    'AUTH_PASSWORD_RESET_ENABLED'                => false,
    'AUTH_PASSWORD_RESET_REQUEST_VIEW_FILE'      => 'auth/recovery.view.php',
    'AUTH_PASSWORD_RESET_REQUEST_VIEW_TEMPLATE'  => 'auth',
    'AUTH_PASSWORD_RESET_VERIFY_URL'             => 'auth/recovery/verify',
    'AUTH_PASSWORD_RESET_VERFIFED_VIEW_FILE'     => 'auth/reset.view.php',
    'AUTH_PASSWORD_RESET_VERFIFED_VIEW_TEMPLATE' => 'auth',
    
    /**
     * -------------------------------------
     * Configuration for: SIGNUP 
     * -------------------------------------
     */
    'AUTH_SIGNUP_ENABLED'                        => false,
    'AUTH_SIGNUP_URL'                            => 'auth/signup',
    'AUTH_SIGNUP_EMAIL_VERIFICATION_URL'         => 'auth/signup/verify',
    'AUTH_SIGNUP_VIEW_FILE'                      => 'auth/register.view.php',
    'AUTH_SIGNUP_VIEW_TEMPLATE'                  => 'auth',

    /**
     * -------------------------------------
     * Configuration for: EMAIL INVITATION
     * -------------------------------------
     */
    'AUTH_INVITATION_ENABLED'                   => false,
    'AUTH_INVITATION_EMAIL_VERIFICATION_URL'    => 'auth/invite/verify',
    'AUTH_INVITATION_COMPLETE_VIEW_FILE'        => 'auth/complete.view.php',
    'AUTH_INVITATION_COMPLETE_VIEW_TEMPLATE'    => 'auth',
   
    /** 
     * -----------------------------------
     * Configuration for: AVATARS/GRAVATAR
     * -----------------------------------
     *
     * USER_AVATAR_PATH:             The path to store the avatars files (must be writible)
     * USER_AVATAR_JPEG_QUALITY:     Image quality
     * USER_AVATAR_SIZE:             set the pixel size of avatars/gravatars (will be 90x90 by default). 
     *                               Avatars are always squares.
	 * USER_AVATAR_UPLOAD_MAX_SIZE:  the max size of upload image (in bytes) default is 1000000 (1MB)
     * USER_AVATAR_USE_GRAVATAR:     Set to true if you want to use "Gravatar(s)", a service that automatically
     *                               gets avatar pictures via using email addresses of users by requesting images 
     *                               from the gravatar.com API. Set to false to use own locally saved avatars.
     */
	'USER_AVATAR_PATH'               => '',
	'USER_AVATAR_SIZE'               => 90,
	'USER_AVATAR_JPEG_QUALITY'       => 85,
	'USER_AVATAR_UPLOAD_MAX_SIZE'    => 1000000,
    'USER_AVATAR_USE_GRAVATAR'       => false,
	'GRAVATAR_DEFAULT_IMAGESET'      => 'mm',
	'GRAVATAR_RATING'                => 'pg',
    
    /**
     * --------------------------
     * Configuration for: CAPTCHA
     * --------------------------
     *
     * CAPTCHA_WIDTH:       The width of the captcha image in pixels. Defaults is 260.
     * CAPTCHA_HEIGHT:      The height of the captcha image in pixels. Defaults is 80.
     */
	'CAPTCHA_WIDTH'         => 260,
    'CAPTCHA_HEIGHT'        => 80,
    
    /**
     * -------------------------------------------
     * Configuration for: EMAIL SERVER CREDENTIALS
     * -------------------------------------------
     *
     * Here you can define how you want to send emails.
     * If you have successfully set up a mail server on your linux server and you know
     * what you do, then you can skip this section. Otherwise please set EMAIL_USE_SMTP to true
     * and fill in your SMTP provider account data.
     *
     * EMAIL_MAILER: 'phpmailer' or 'native'
     * EMAIL_USE_SMTP: Use SMTP or not
     * EMAIL_SMTP_AUTH: leave this true unless your SMTP service does not need authentication
     */
    'EMAIL_MAILER'          => 'phpmailer',
    'EMAIL_USE_SMTP'        => false,
    'EMAIL_SMTP_HOST'       => 'yourhost',
    'EMAIL_SMTP_AUTH'       => true,
    'EMAIL_SMTP_USERNAME'   => 'yourusername',
    'EMAIL_SMTP_PASSWORD'   => 'yourpassword',
    'EMAIL_SMTP_PORT'       => 465,
    'EMAIL_SMTP_ENCRYPTION' => 'ssl',


 );