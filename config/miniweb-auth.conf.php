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
 * @version    0.9.6
 * @copyright  2017-2021 Kristuff
 */

/** 
 * Returns the default configuration.
 */
return array(
 
    /** 
     * --------------------
     * global configuration
     * --------------------
     *
     * AUTH_EMAIL_HTML:              True to use HTML email for auth process (registration, recovery, ...)
     * AUTH_EMAIL_FROM_EMAIL:        The email address of the sender
     * AUTH_EMAIL_FROM_NAME:         The name of the email sender
     */
    'AUTH_EMAIL_HTML'          => false,
    'AUTH_EMAIL_FROM_EMAIL'    => 'no-reply@EXAMPLE.COM',
    'AUTH_EMAIL_FROM_NAME'     => 'The EXAMPLE.COM team',
   
    /** 
     * --------------------------
     * Configuration for: LOGIN
     * --------------------------
     *
     * AUTH_LOGIN_COOKIE_ENABLED: true to allow login with cookie (see also configuration for cookie)
     */
    'AUTH_LOGIN_URL'                             => 'auth/signin',
    'AUTH_LOGIN_VIEW_FILE'                       => 'auth/login.view.php',
    'AUTH_LOGIN_VIEW_TEMPLATE'                   => 'auth',
    'AUTH_LOGIN_COOKIE_ENABLED'                  => false,
   
    /**
     * -------------------------------------
     * Configuration for: PASSWORD RECOVERY
     * -------------------------------------
     */
    'AUTH_PASSWORD_RESET_ENABLED'                => false,
    'AUTH_PASSWORD_RESET_MAIL_FROM_EMAIL'        => 'no-reply@EXAMPLE.COM',
    'AUTH_PASSWORD_RESET_MAIL_FROM_NAME'         => 'The EXAMPLE.COM team',
    'AUTH_PASSWORD_RESET_MAIL_SUBJECT'           => 'Password reset for EXAMPLE.COM application',
    'AUTH_PASSWORD_RESET_MAIL_CONTENT'           => 'Please click on this link to reset your password: ',
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
    
    'AUTH_SIGNUP_EMAIL_VERIFICATION_FROM_EMAIL'  => 'no-reply@EXAMPLE.COM',
    'AUTH_SIGNUP_EMAIL_VERIFICATION_FROM_NAME'   => 'The YOUR_APP_NAME team',
    'AUTH_SIGNUP_EMAIL_VERIFICATION_SUBJECT'     => 'Account activation for EXAMPLE.COM application',
    'AUTH_SIGNUP_EMAIL_VERIFICATION_CONTENT'     => 'Please click on this link to activate your account: ',

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
    
    
 );