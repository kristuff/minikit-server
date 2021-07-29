<?php declare(strict_types=1);

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
 * @version    0.9.10
 * @copyright  2017-2021 Kristuff
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