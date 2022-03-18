<?php declare(strict_types=1);

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.17 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */


namespace Kristuff\Minikit\Mvc;

use Kristuff\Minikit\Mvc\View;
use Kristuff\Minikit\Mvc\Factory;
use Kristuff\Minikit\Http\Redirect;

/** 
 * class Controller 
 *
 * Abstract class for controllers. 
 */
abstract class Controller 
{
    /** 
     * @access protected
     * @var \Kristuff\Minikit\Mvc\View            $view       The View instance
     */
    protected $view = null;

    /** 
     * @access protected
     * @var Mvc\Application $application        The application instance
     */
    protected $application;

    /**
     * Constructor
     *
     * @access public
     * @param Mvc\Application $application        The application instance
     */
    public function __construct(Application $application)
    {
        $this->view = new View();
        $this->application = $application;
    }

    /**
     * Gets/Returns the global Session instance
     *
     * @access protected
     * @return \Kristuff\Minikit\Http\Session
     */
    protected function session(): \Kristuff\Minikit\Http\Session
    {
        return Factory::getFactory()->getSession();
    }

    /**
     * Gets/Returns the global Cookie instance
     *
     * @access protected 
     * @return \Kristuff\Minikit\Http\Cookie 
     */
    protected function cookie(): \Kristuff\Minikit\Http\Cookie
    {
        return Factory::getFactory()->getCookie();
    }

    /**
     * Gets the global instance of Http Request
     *
     * @access public
     * @return \Kristuff\Minikit\Http\Request
     */
    public function request(): \Kristuff\Minikit\Http\Request
    {
        return Factory::getFactory()->getRequest();
    }

    /**
     * Gets/Returns global the Token (AntiCsrf) instance
     *
     * @access protected
     * @return \Kristuff\Minikit\Security\Token
     */
    protected function token(): \Kristuff\Minikit\Security\Token
    {
         return Factory::getFactory()->getToken($this->session());
    }  

    /**
	 * Redirects to the defined url
     *
	 * @access protected
	 * @param string  $url         The url.
	 * @param int     $code        The http response code. 
	 * @param bool    $exit        true to stop application via exit(). Default is true.
     *
     * @return void
	 */
    protected function redirect(string $url, int $code = 302, bool $exit = true): void
    {
        Redirect::url($url, $code, $exit);  
    }
    
}