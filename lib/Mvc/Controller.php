<?php

declare(strict_types=1);

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
 * @version    0.9.0
 * @copyright  2017-2020 Kristuff
 */

namespace Kristuff\Miniweb\Mvc;

use Kristuff\Miniweb\Mvc\View;
use Kristuff\Miniweb\Mvc\Factory;
use Kristuff\Miniweb\Http\Redirect;

/** 
 * class Controller 
 *
 * Abstract class for controllers. 
 */
abstract class Controller 
{
    /** 
     * @access protected
     * @var \Kristuff\Miniweb\Mvc\View            $view       The View instance
     */
    protected $view = null;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        // create a View instance
        $this->view = new View();
    }

    /**
     * Gets/Returns the global Session instance
     *
     * @access protected
     * @return \Kristuff\Miniweb\Http\Session
     */
    protected function session(): \Kristuff\Miniweb\Http\Session
    {
        return Factory::getFactory()->getSession();
    }

    /**
     * Gets/Returns the global Cookie instance
     *
     * @access protected 
     * @return \Kristuff\Miniweb\Http\Cookie 
     */
    protected function cookie(): \Kristuff\Miniweb\Http\Cookie
    {
        return Factory::getFactory()->getCookie();
    }

    /**
     * Gets the global instance of Http Request
     *
     * @access public
     * @return \Kristuff\Miniweb\Http\Request
     */
    public function request(): \Kristuff\Miniweb\Http\Request
    {
        return Factory::getFactory()->getRequest();
    }

    /**
     * Gets/Returns global the Token (AntiCsrf) instance
     *
     * @access protected
     * @return \Kristuff\Miniweb\Security\Token
     */
    protected function token(): \Kristuff\Miniweb\Security\Token
    {
         return Factory::getFactory()->getToken($this->session());
    }  

    /**
	 * Redirects to the defined url
     *
     * Send Location header and a redirect response code: 302 (default) or 301 for permanent redirect.
     * Make sure the 201 or a 3xx status code has not already been set before to use this function. 
     * Most contemporary clients accept relative URIs as argument to "Location", but some older clients 
     * require an absolute URI including the scheme, hostname and absolute path.
     *
	 * @access protected
	 * @param  string  $url         The url.
	 * @param  bool    $permanent   true for permanent redirect. Default is false. 
	 * @param  bool    $exit        true to stop application via exit(). Default is false.
     *
     * @return void
	 */
    protected function redirect(string $url, bool $permanent = false, bool $exit = false): void
    {
        Redirect::url($url, $permanent, $exit);  
    }
    
}