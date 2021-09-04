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
 * @version    0.9.12
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Http;

use Kristuff\Miniweb\Http\RequestMethod;

/**
 * Class Request
 *
 * Abstracts the access to $_GET, $_POST and $_COOKIE and some $_SERVER properties, preventing 
 * direct access to these super-globals. This makes PHP code quality analyzer tools very happy.
 * 
 * @see http://php.net/manual/en/reserved.variables.request.php
 * @see https://phpmd.org/rules/controversial.html#superglobals
 */
class Request extends RequestMethod
{
    use ServerTrait;

    /**
     * request method
     *
     * @access protected
     * @var string
     */
    protected $method = null;

    /**
     * request uri
     *
     * @access protected
     * @var string
     */
    protected $uri = null;

    /**
     * controller name
     *
     * @access protected
     * @var string
     */
    protected $controllerName = null;

    /**
     * controller name
     *
     * @access protected
     * @var string
     */
    protected $actionName = null;

    /** 
     * Constructor
     *
     * @access public
     * @param   //TODO
     */
    public function __construct(string $uri, string $method = self::METHOD_GET, ?string $controllerName = null, ?string $actionName = null)
    {
        $this->uri      = $uri;
        //TODO check method passed . make case insensitive
        $this->method   = $method;
        $this->controllerName = $controllerName;
        $this->actionName = $actionName;
    }

    /** 
     * Gets/returns the request method used
     *
     * @access public
     *
     * @return string
     */
    public function method(): string
    {
        return $this->method;
    }

    /** 
     * Gets/returns the request uri
     *
     * @access public
     *
     * @return string
     */
    public function uri(): string
    {
        return $this->uri;
    }

    /** 
     * Gets/returns the controller name
     *
     * @access public
     *
     * @return string
     */
    public function controllerName(): string
    {
        return $this->controllerName;
    }

    /** 
     * Gets/returns the action name
     *
     * @access public
     *
     * @return string
     */
    public function actionName(): string
    {
        return $this->actionName;
    }

    /** 
     * Gets/returns the value of a specific key of the POST or GET super-global 
     * depending the used method. 
     *
     * @access public
     * @static
     * @param string    $key            The key
     * @param mixed     $default        The default returned value is case of null
     * @param bool      $cleanPost      Marker for optional cleaning of the var
     *
     * @return mixed    the key's value or the default value 
     */
    public function arg(string $key, $default = null, bool $cleanPost = false)
    {
        // POST method?
        if ($this->method() ===  self::METHOD_POST) {
            return self::post($key) ? self::post($key, $cleanPost) : $default;            
        } 

        // for other methods, parameters are  GET
        return self::get($key) ? self::get($key) : $default;            
    }

    /** 
     * Gets/returns the value of a specific key of the POST super-global.
     *
     * When using just Request::post('x') it will return the raw and untouched $_POST['x'], when using it like
     * Request::post('x', true) then it will return a trimmed and stripped $_POST['x']
     *
     * @access public
     * @static
     * @param string    $key            The key
     * @param bool      $clean          true to remove html and php tags. Default is false
     *
     * @return mixed the key's value or null
     */
    public static function post(string $key, bool $clean = false)
    {
        if (isset($_POST[$key])) {
            return $clean ? trim(strip_tags($_POST[$key])) : $_POST[$key];
        }

        return null;
    }

    /**
     * Returns the state of a checkbox.
     *
     * @access public
     * @static
     * @param string    $key            The key
     *
     * @return mixed    state of the checkbox
     */
    public static function postCheckbox(string $key)
    {
        return isset($_POST[$key]) ? 1 : null;
    }

    /**
     * gets/returns the value of a specific key of the GET super-global
     *
     * @access public
     * @static
     * @param string    $key            The key
     *
     * @return mixed    the key's value or null
     */
    public static function get(string $key)
    {
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }

    /**
     * Gets/returns the referer. 
     * 
     * The address of the page (if any) which referred the user agent to the current page. This is set 
     * by the user agent. Not all user agents will set this, and some provide the ability to modify 
     * HTTP_REFERER as a feature. In short, it cannot really be trusted. 
     * 
     * @access public
     * @static
     *
     * @return string       The value of HTTP_REFERER in $_SERVER.
     */
    public static function referer()
    {
        return self::getServerValue('HTTP_REFERER');
    }

    /**
     * Gets/returns the user agent. 
     *
     * @access public
     * @static
     *
     * @return string       The value of HTTP_USER_AGENT in $_SERVER.
     */
    public static function userAgent()
    {
        return self::getServerValue('HTTP_USER_AGENT');
    }

    /**
     * Gets/returns the remote port being used on the user's machine to communicate with the web server. 
     * 
     * @access public
     * @static
     *
     * @return string       The value of REMOTE_PORT in $_SERVER.
     */
    public static function remotePort()
    {
        return self::getServerValue('REMOTE_PORT');
    }

    /**
     * Gets/returns the remote user (authenticated user) . 
     *
     * @access public
     * @static
     *
     * @return string       The value of REMOTE_USER in $_SERVER.
     */
    public static function remoteUser()
    {
        return self::getServerValue('REMOTE_USER');
    }

    /**
     * Gets/returns the authenticated user if the request is internally redirected. 
     * 
     * @access public
     * @static
     *
     * @return string       The value of REDIRECT_REMOTE_USER in $_SERVER.
     */
    public static function redirectRemoteUser()
    {
        return self::getServerValue('REDIRECT_REMOTE_USER');
    }

    /**
     * Gets/returns the remote Host. 
     *
     * @access public
     * @static
     *
     * @return string       The value of REMOTE_HOST in $_SERVER.
     */
    public static function remoteHost()
    {
        return self::getServerValue('REMOTE_HOST');
    }

    /**
     * Gets/returns the remote IP. 
     *
     * @access public
     * @static
     *
     * @return string       The value of REMOTE_ADDR in $_SERVER.
     */
    public static function remoteIp()
    {
        return self::getServerValue('REMOTE_ADDR');
    }

    /**
     * Gets/returns the contents of the Accept: header from the current request, if there is one. 
     *
     * @access public
     * @static
     *
     * @return string
     */
    public static function accept(): ?string
    {
        return self::getServerValue('HTTP_ACCEPT');
    }

    /**
     * Gets/returns the contents of the Accept-Charset: header from the current request, if there is one. 
     * Example: 'iso-8859-1,*,utf-8'. 
     * 
     * @access public
     * @static
     *
     * @return string
     */
    public static function acceptCharset(): ?string
    {
        return self::getServerValue('HTTP_ACCEPT_CHARSET');
    }

    /**
     * Gets/returns the contents of the Accept-Language: header from the current request, if there is one. 
     * Example: 'en'. 
     * 
     * @access public
     * @static
     *
     * @return string
     */
    public static function acceptLanguage(): ?string
    {
        return self::getServerValue('HTTP_ACCEPT_LANGUAGE');
    }

    /**
     * Gets/returns the contents of the Accept-Encoding: header from the current request, if there is one. 
     * Example: 'gzip'. 
     *
     * @access public
     * @static
     *
     * @return string
     */
    public static function acceptEncoding(): ?string
    {
        return self::getServerValue('HTTP_ACCEPT_ENCODING');
    }

    /**
     * Gets/returns the request time
     *
     * @access public
     * @static
     *
     * @return float    
     */
    public static function time(): float
    {
        // As of PHP 5.4.0, REQUEST_TIME_FLOAT is available in the $_SERVER superglobal array.
        // It contains the timestamp of the start of the request with microsecond precision.
        $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        return $time;
    }      
}
