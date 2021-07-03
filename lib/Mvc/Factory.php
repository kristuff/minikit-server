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
 * @version    0.9.9
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Mvc;

use Kristuff\Miniweb\Http;
use Kristuff\Miniweb\Security;
use Kristuff\Miniweb\Mvc\Application;
use Kristuff\Miniweb\Mvc\Feedback;

/** 
 * class Factory
 */
class Factory
{
    /**
     * @access private
     * @static var
     * @var Factory             $factory        The Factory instance 
     */
    private static $factory;

    /**
     * @access private
     * @var Security\Token      $token          The Token instance
     */
    private $token;

    /**
     * @access private
     * @var Http\Request            $request        The Request instance
     */
    private $request;

    /**
     * @access private
     * @var Http\Session            $Session        The Session instance
     */
    private $session;

    /**
     * @access private
     * @var Mvc\Feedback            $feedback       The Feedback instance
     */
    private $feedback;

    /**
     * @access private
     * @var Http\Cookie             $cookie         The Cookie instance
     */
    private $cookie;

    /**
     * Gets or creates a global static Factory instance
     *
     * @access public
     * @static method
     *
     * @return Factory
     */
    public static function getFactory(): Factory
    {
        if (!self::$factory) {
            self::$factory = new Factory();
        }
        return self::$factory;
    }

    /**
     * Register the Request object for the whole application
     *
     * @access public
     * @param  Http\Request     $request
     * 
     * @return void
     */
    public function setRequest(Http\Request $request): void
    {
        $this->request = $request;
    }

    /**
     * Register the Session object for the whole application
     *
     * @access public
     * @param  Http\Session     $session        The Session instance
     * 
     * @return void
     */
    public function setSession(Http\Session $session): void
    {
        $this->session = $session;
    }

    /**
     * Register the Request object for the whole application
     *
     * @access public
     * @return Http\Request
     */
    public function getRequest(): Http\Request
    {
        return $this->request;
    }

    /**
     * Get or create a Session instance
     *
     * @access public
     * @return Http\Session
     */
    public function getSession(): Http\Session
    {
        return $this->session;
    }

    /**
     * Get or create a Token instance
     *
     * @access public
     * @param  Http\Session     $session        The Session instance
     * 
     * @return Security\Token
     */
    public function getToken(Http\Session $session): Security\Token
    {
        if (!$this->token) {
            $this->token = new Security\Token($session);
        }
        return $this->token;
    }

    /**
     * Get or create a Feedback instance
     *
     * @access public
     * @return Feedback
     */
    public function getFeedback(): Feedback
    {
        if (!$this->feedback) {
            $session = new Http\Session();
            $this->feedback = new Feedback($session);
        }
        return $this->feedback;
    }

    /**
     * Get or create a Cookie instance
     *
     * @access public
     * @return Http\Cookie
     */
    public function getCookie(): Http\Cookie
    {
        if (!$this->cookie) {
            $this->cookie = new Http\Cookie(
                Application::config('COOKIE_SECURE'), 
                Application::config('COOKIE_HTTP'),
                Application::config('COOKIE_PATH'), 
                Application::config('COOKIE_DOMAIN'),
                Application::config('COOKIE_SAMESITE'),
                Application::config('COOKIE_RUNTIME')
            );
        }
        return $this->cookie;
    }
}