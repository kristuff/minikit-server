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
 * @version    0.9.2
 * @copyright  2017-2020 Kristuff
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
    'APP_NAMESPACE'        => '',  
    'CONFIG_PATH'               => __DIR__ . '/',
    'CONFIG_DEFAULT_PATH'       => __DIR__ . '/default/',

    /**
     * ------------------------------
     * Configuration for: controllers
     * ------------------------------
     */

    //TODO
    'CONTROLLER_NAMESPACE' => '',  

    /**
     * CONTROLLER_PATH: The full path to controllers. Must be defined in real application
     */
    'CONTROLLER_PATH'           => '',

    /**
     * CONTROLLER_EXTENSION: The 'relative' name of controller (without .php extension)
     * Default is 'Controller' (Controller for 'index' will be named 'IndexController' in 'IndexController.php').
     */
    'CONTROLLER_EXTENSION'      => 'Controller',

    /**
     * CONTROLLER_UCWORDS: True if the first letter of controller must be in uppercase.
     * Default is True (Controller for index we be 'Index')
     */
    'CONTROLLER_UCWORDS'       => true,

    /**
     * CONTROLLER_DEFAULT: The default controller to use.
     * CONTROLLER_DEFAULT_ACTION: The default action to call inside the default controller.
     * Default is 'index' for name and action, that means a controller index() method 
     * inside Index controler.
     */
    'CONTROLLER_DEFAULT'        => 'index',
    'CONTROLLER_ACTION_DEFAULT' => 'index',

    // Todo doc 
    'ERROR_CONTROLLER'          => '', 

   /**
     * ------------------------
     * Configuration for: Views
     * ------------------------
     */

    /**
     * VIEW_PATH: The full path to views. Must be defined in real application
     */
    'VIEW_PATH'           => '',

    /**
     * ------------------------
     * Configuration for: Token
     * ------------------------
     */

    /**
     * TOKEN_VALIDITY: The token validity in seconds. Default is 86400 (1 day).
     */
    'TOKEN_VALIDITY'       => 86400,


    /**
     * --------------------------
     * Configuration for: Cookies
     * --------------------------
     * 
     * COOKIE_RUNTIME: The cookie validity in second. 1209600 seconds = 2 weeks
     */
    'COOKIE_RUNTIME' => 1209600,

    /**
     * COOKIE_PATH is the path the cookie is valid on, usually '/' to make it valid on the whole domain.
     * @see http://stackoverflow.com/q/9618217/1114320
     * @see php.net/manual/en/function.setcookie.php
     */
    'COOKIE_PATH' => '/',

    /**
     * COOKIE_DOMAIN:   The (sub)domain where the cookie is valid for. Usually this does not work with 'localhost',
     *                  '.localhost', '127.0.0.1', or '.127.0.0.1'. If so, leave it as empty string, false or null.
     *                  When using real domains make sure you have a dot (!) in front of the domain if you want to, like 
     *                  ".mydomain.com". 
     * Older browsers still implementing the deprecated » RFC 2109 may require a leading . to match all subdomains. 
     * @see http://php.net/manual/en/function.setcookie.php#73107
     * @see http://stackoverflow.com/questions/2285010/php-setcookie-domain
     * @see http://stackoverflow.com/questions/1134290/cookies-on-localhost-with-explicit-domain
     */
    'COOKIE_DOMAIN' => '',

    /**
     * COOKIE_SECURE:   If the cookie will be transferred through secured connection(SSL). It's highly recommended 
     *                  to set it to true if you have secured connection.
     */
     'COOKIE_SECURE' => false,

    /**
     * COOKIE_HTTP:     If set to true, Cookies that can't be accessed by JS - Highly recommended!
     */
    'COOKIE_HTTP' => true,

    /**
     * SESSION_RUNTIME: How long should a session cookie be valid by seconds, 604800 = 1 week.
     */
    'SESSION_RUNTIME' => 604800,

    /**
     * 
     */
    'COOKIE_SAMESITE' => 'Strict',


    /**
     * -----------------------------
     * Configuration for: Encryption
     * -----------------------------
     */
    
    /**
     * TODO
     *
     * Configuration for: Encryption Keys
     * ENCRYPTION_KEY, HMAC_SALT: Currently used to encrypt and decrypt publicly visible values, like the user id in
     * the cookie. Change these values for increased security, but don't touch if you have no idea what this means.
     */
    'ENCRYPTION_KEY' => '6#x0gÊìf^25cL1f$08&',
    'HMAC_SALT'      => '8qk9c^4L6d#15tM8z7n0%',
 
  );