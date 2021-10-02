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
 * @version    0.9.14
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Http;

use Kristuff\Miniweb\Core\Filter;
use Kristuff\Miniweb\Mvc\Application;

/**
 * class Session 
 *
 * Handles the session stuff.
 */
class Session
{
    /**
     * Make sure the session is started 
     *
     * @access public
     * @return void
     */
    public function init(): void
    {
        // if no session exist, start the session
        if (session_status() == PHP_SESSION_NONE || session_status() == 1) {
        //if (session_id() === '') {
            
            // honor app config
            session_set_cookie_params([
                'secure'    => Application::config('COOKIE_SECURE'), 
                'httponly'  => Application::config('COOKIE_HTTP'),
                'path'      => Application::config('COOKIE_PATH'), 
                'domain'    => Application::config('COOKIE_DOMAIN'),
                'samesite'  => Application::config('COOKIE_SAMESITE'),
                'lifetime'  => Application::config('COOKIE_RUNTIME'),
            ]);

            session_start();
        }
    }

    /**
     * Gets/returns the session id. Returns the session id for the current session or an empty 
     * string ("") if there is no current session (no current session id exists). 
     * @link https://php.net/manual/en/function.session-id.php
     * 
     * @access public
     * @return string
     */
    public function sessionId(): string
    {
        return session_id() ;
    }

    /**
     * Adds a value as a new array element to the key.
     *
     * @param string    $key        The key
     * @param mixed     $value      The key's value
     */
    public static function add(string $key, $value): void
    {
        $_SESSION[$key][] = $value;
    }

    /**
     * Sets a specific value to a specific key of the session
     *
     * @access public
     * @param string    $key        The key
     * @param mixed     $value      The key's value
     *
     * @return void
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Gets/returns the value of a specific key of the session
     *
     * @access public
     * @param string    $key        The key
     *
     * @return mixed|null           The key's value if the key exists, othewise Null.
     */
    public function get(string $key)
    {
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];

        	// filter the value for XSS vulnerabilities
        	Filter::XssFilter($value);

            return $value;
        }
        return null;
    }

    /**
     * Destroys all data registered to a session, delete the session cookie,
     * and, if needed, delete the $_COOKIE value
     *
     * @access public
     * @return void
     */
    public function destroy(): void
    {
        // Unset all of the session variables.
        $this->clearSessionArray();

        /**
         * In order to kill the session altogether, the session ID must also be unset. If a cookie 
         * is used to propagate the session ID (default behavior), then the session cookie must be deleted. 
         * setcookie() may be used for that. 
         * @sess http://php.net/manual/en/function.session-destroy.php
         */
        if ($this->isUsingCookie()) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );

            // may unsupport this, unsecure 
            if (!$this->isUsingCookieOnly()) {
                if (isset($_COOKIE[session_name()])){
                    unset($_COOKIE[session_name()]);
                }
            }
        }

        // destroy the session
        session_destroy();
    }

    /**
     * Reset the session. Remove old and regenerate session ID, 
     * and clear $_SESSION array
     *
     * @access public
     * @return void
     */
    public function reset(): void
    {
        // make sure session is init when 
        // calling this action
        $this->init();

        // remove old and regenerate session ID.
        // It's important to regenerate session on sensitive actions,
        // and to avoid fixated session.
        // e.g. when a user logs in
        session_regenerate_id(true);

        // Unset all of the session variables.
        $this->clearSessionArray();
    }

    /**
     * Get whether the module will use cookies to store the session id on the client side. 
     * Defaults to true (enabled).
     * @see http://php.net/manual/en/session.configuration.php#ini.session.use-cookies
     *
     * @access public
     * @return bool     True if the module will use cookies to store the session id on the 
     *                  client side, otherwise false.
     */
    public function isUsingCookie(): bool
    {
        return (bool) ini_get('session.use_cookies');
    }
 
    /**
     * Get whether the module will only use cookies to store the session id on the client side. 
     * Enabling this setting prevents attacks involved passing session ids in URLs. Defaults is 
     * true (enabled) since PHP 5.3.0. 
     * @see http://php.net/manual/en/session.configuration.php#ini.session.use-only-cookies
     *
     * @access public
     * @return bool     True if the module will only use cookies to store the session id on 
     *                  the client side, otherwise false.
     */
    public function isUsingCookieOnly(): bool
    {
        return (bool) ini_get('session.use_only_cookies');
    }

    /**
     * Clear the $_SESSION array
     *
     * @access private
     * @return void
     */
    private function clearSessionArray(): void
    {
        // reset session 
        $_SESSION = [];
    }
}