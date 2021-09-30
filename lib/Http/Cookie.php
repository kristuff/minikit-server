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
 * @version    0.9.14
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Http;

use Kristuff\Miniweb\Http\Server;

/**
 * Class Cookie
 *
 * Class for create and retreive cookies. 
 * @see http://php.net/manual/en/features.cookies.php
 * @see http://php.net/manual/en/function.setcookie.php
 */
class Cookie
{
    /**
     * The SameSite attribute of the Set-Cookie HTTP response header allows you to declare if your cookie 
     * should be restricted to a first-party or same-site context. 
     * The SameSite attribute accepts three values:
     *  - Lax:     Cookies are allowed to be sent with top-level navigations and will be sent along with GET 
     *             request initiated by third party website. This is the default value in modern browsers.
     *  - Strict:  Cookies will only be sent in a first-party context and not be sent along with requests 
     *             initiated by third party websites.
     *  - None:    Cookies will be sent in all contexts, i.e sending cross-origin is allowed
     * 
     * @access protected
     * @var string
     */
    protected $sameSite = 'Lax';

    /**
     * Indicates that cookies should only be transmitted over a secure HTTPS connection 
     * from the client. When set to true, the cookie will only be set if a secure connection exists
     *
     * @access protected
     * @var bool
     */
    protected $secure = false;

    /**
     * Indicates that cookies should only be accessible only through the HTTP protocol. This means that 
     * the cookie won't be accessible by scripting languages, such as JavaScript. It has been suggested 
     * that this setting can effectively help to reduce identity theft through XSS attacks (although it 
     * is not supported by all browsers), but that claim is often disputed
     * http://php.net/manual/en/function.setcookie.php
     *
     * @access protected
     * @var bool
     */
    protected $httpOnly = false;

    /**
     * The default path on the server in which the cookie will be available on. 
     * If set to '/' (its default value), the cookie will be available within the entire domain. If set 
     * to '/foo/', the cookie will only be available within the /foo/ directory and all sub-directories 
     * such as /foo/bar/ of domain. 
     *
     * @access protected
     * @var string
     */
    protected $path = false;

    /**
     * The (sub)domain that the cookie is available to. Setting this to a subdomain (such as 
     * 'www.example.com') will make the cookie available to that subdomain and all other sub-domains of 
     * it (i.e. w2.www.example.com). To make the cookie available to the whole domain (including all 
     * subdomains of it), simply set the value to the domain name ('example.com', in this case).
     * Older browsers still implementing the deprecated » RFC 2109 may require a leading . to match all 
     * subdomains. 
     *
     * @access protected
     * @var string|bool $domain             The domain
     */
    protected $domain = false;
    
    /**
     * @access protected
     * @var int         $runtime            How long should a session cookie be valid by seconds, 604800 = 1 week.
     */
    protected $runtime = 604800;
    
    /**
     * Constructor
     *
     * @access public
     * @param bool      $secure             true if cookies should only be transmitted over a secure HTTPS connection. 
     * @param bool      $httpOnly           true if cookies should only be accessible only through the HTTP protocol.
     * @param string    $path               The default path on the server in which the cookie will be available on. 
     * @param string    $domain             The default (sub)domain that the cookie is available to.
     * @param string    $samesite           SameSite attribut declares if your cookie should be restricted to a first-party 
     *                                      or same-site context.
     * @param int       $runtime            The duration of the cookie (in seconds). Default is -1, that
     *                                      means using the default duration is used.  
     * 
     * @return void
     */
    public function __construct(bool $secure = false, bool $httpOnly = false, string $path = '/', string $domain = '', string $sameSite = 'Lax', int $runtime = -1)
    {
        $this->secure   = $secure;
        $this->httpOnly = $httpOnly;
        $this->path     = $path;
        $this->sameSite = $sameSite;

        // use the given domain if defined or get current
        $this->domain   = !empty($domain) ? $domain : $this->getCurrentHost();

        // define default runtime
        if ((int) $runtime > 0) {
            $this->runtime = (int) $runtime;
        }
    }

    /**
     * gets/returns the value of a specific key of the COOKIE super-global
     *
     * @access public
     * @static 
     * @param string    $key            The key
     * 
     * @return string|null       Returns the key's value if exists, otherwise null
     */
    public function get(string $key): ?string
    {
        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }
        return null;
    }

    /**
     * Sets the value of a cookie 
     * 
     * The cookie value is stored on the clients computer. Do not store sensitive information. 
     * If output exists prior to calling this function, setcookie() will fail and return false. 
     * If setcookie() successfully runs, it will return true. This does not indicate whether the 
     * user accepted the cookie. 
     *
     * @access public
     * @param string    $name           The name of the cookie.
     * @param string    $value          The value of the cookie.
     * @param string    $path           The path on the server in which the cookie will be available on.
     *                                  Default is '/' so the cookie will be available within the entire 
     *                                  domain. If set to '/foo/', the cookie will only be available within 
     *                                  the /foo/ directory and all sub-directories such as /foo/bar/ of domain.
     * @param int       $runtime        The duration of the cookie (in seconds). Default is -1, that
     *                                  means using the default duration is used.  
     * 
     * @return bool
     */
    public function set(string $name, string $value, string $path = null, int $runtime = -1): bool
    {
        // define when cookie will expire
        $expire =  ($runtime > -1) ? time() + (int) $runtime : time() + $this->runtime;

        return setcookie($name, $value, [
            'expires'   => $expire,
            'path'      => $this->getCookiePath($path),
            'domain'    => $this->domain,
            'samesite'  => $this->sameSite,
            'secure'    => $this->secure,
            'httponly'  => $this->httpOnly,
        ]);
    }
    
    /**
     * sets the value of a cookie that will expire at the end of the 
     * session (ie. when the browser closes).
     * 
     * The cookie value is stored on the clients computer. Do not store sensitive information. 
     * If output exists prior to calling this function, setcookie() will fail and return false. 
     * If setcookie() successfully runs, it will return true. This does not indicate whether the 
     * user accepted the cookie. 
     *
     * @access public
     * @static 
     * @param string    $name           The cookie name
     * @param string    $value          The value of the cookie.
     * @param string    $path           The path for the cookie (keep null to use default path).
     *                                  Default is '/' so the cookie will be available within the entire 
     *                                  domain. If set to '/foo/', the cookie will only be available within 
     *                                  the /foo/ directory and all sub-directories such as /foo/bar/ of domain.
     * 
     * @return bool
     */
    public function setForSession(string $name, string $value, string $path = null): bool
    {
        return setcookie($name, $value, [
            'expires'   => 0,
            'path'      => $this->getCookiePath($path),
            'domain'    => $this->domain,
            'samesite'  => $this->sameSite,
            'secure'    => $this->secure,
            'httponly'  => $this->httpOnly,
        ]);
    }

    /**
     * Deletes the cookie
     *
     * Cookies must be deleted with the same parameters as they were set with.  
     * Sets value to empty string and expire to passed date (1)
     * @see http://php.net/manual/en/function.setcookie.php
     *
     * @access public
     * @param string    $name           The cookie name
     * @param string    $path           The path for the cookie (keep null to use default path).
     * 
     * @return void
     */
    public function delete(string $name, string $path = null): void
    {
        if (isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
        }

        setcookie($name, '', [
            'expires'   => 1,
            'path'      => $this->getCookiePath($path),
            'domain'    => $this->domain,
            'samesite'  => $this->sameSite,
            'secure'    => $this->secure,
            'httponly'  => $this->httpOnly,
        ]);
    }

    /**
     * Gets/returns the current hostname to use 
     *
     * Usually define cookie domain does not work with "localhost", 
     * If so, leave it as false .
     * @link http://php.net/manual/en/function.setcookie.php#73107
     * 
     * @access protected
     * @param string    $name           The cookie name
     * @param string    $path           The path for the cookie (keep null to use default path).
     * 
     * @return string|bool  The current host if not localhost, otherwise false 
     */
    protected function getCurrentHost()
    {
        $host = Server::httpHost();
        return $host !== 'localhost' ? $host : false;
    }

    /**
     * Gets/returns the path for the cookie 
     *
     * Returns the given path if defined, otherwise the default defined in constructor.
     *
     * @access protected
     * @param string    $path           The path for the cookie (keep null to use default path).
     * 
     * @return string
     */
    protected function getCookiePath(string $path = null): string
    {
        return isset($path) ? strval($path) : $this->path;
    }
}