<?php declare(strict_types=1);

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.23 
 * Copyright (c) 2017-2023 Christophe Buliard  
 */

namespace Kristuff\Minikit\Security;

use Kristuff\Minikit\Http\Session;

/**
 * Class AntiCsrf utils.
 * 
 */
class Token
{
    /**
     * @access private
     * @var    int              $validity       The token validity in seconds. Default is 86400 (1 day)
     */
    private $validity = 86400;

    /**
     * @access private
     * @var    Http\Session     $session        The Session instance
     */
    private $session = null;
    
    /** 
     * Constructor
     *
     * @access public
     * @param Http\Session  $session        The Session instance
     * @param int           $validity       (optional) The token validity in seconds. Default is 86400 (1 day)
     */
    public function __construct(Session $session, int $validity = 86400)
    {
        $this->session = $session;
        $this->validity = $validity;
    }

    /**
     * Gets/returns the CSRF token
     *
     * @access public
     * @param string        $identifier     (optional) Identifier. Default is 'global'.
     *
     * @return string       
     */
    public function value(string $identifier = 'global'): string
    {
        return $this->getToken($identifier);
    }

    /**
     * Checks if the the given CSRF token is valid according to 
     * the value stored in session.
     * 
     * @access public
     * @param string        $value          The value to compare
     * @param string        $identifier     (optional) Identifier. Default is 'global'.
     *
     * @return bool         true if the given value matchs the value registered in session, otherwise false 
     */
    public function isTokenValid(string $value = null, string $identifier = 'global'): bool
    {
        // get token from session and compare
        $token = $this->session->get('token_'.$identifier);    
        return !empty($value) && !empty($token) && ($token == $value);
    }
   
    /**
     * Generates and returns a new token 
     * 
     * @access public
     * @static
     * @param int           $lenght         The lenght of the base bytes. The string returned 
     *                                      by this function will the double of this value 
     *
     * @return string       The token   
     */
    public static function getNewToken(int $lenght): string
    {
        // random_bytes() PHP >=7
        return bin2hex(random_bytes($lenght));
    }       

    /**
     * get or generates a token
     *
     * @access private
     * @param string        $identifier      (optional) Identifier. Default is 'global'.
     *
     * @return string  
     */
    private function getToken(?string $identifier = 'global'): string
    {
        // get token from session
        $token      = $this->session->get('token_'.$identifier);    
        $tokenTime  = $this->session->get('token_'.$identifier.'_time');

        // check if really exists and is still valid
        if (!empty($token) && !empty($tokenTime) && time() <= ($tokenTime + $this->validity)){
            return $token;    
        }

        // generate a new one and register it in session
        $token = self::getNewToken(32);
        $this->session->set('token_'.$identifier, $token);
        $this->session->set('token_'.$identifier.'_time', time());
        return $token;
    }
}