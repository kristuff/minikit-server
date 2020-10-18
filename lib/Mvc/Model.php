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

use Kristuff\Miniweb\Mvc\Factory;
use Kristuff\Miniweb\Mvc\Application;
use Kristuff\Miniweb\Mvc\TaskResponse;

/** 
 * class Model 
 *
 * Abstract base class for models. 
 */
abstract class Model
{
    /**
     * Adds a positive or negative message in feedback collection and/or 
     * returns the global Feedback instance.
     *
     * When the function is used without parameters or null message, it just returns the Feedback instance.
     *
     * @access public
     * @static
	 * @param string       [$message]         The message to add. Default is null (no message added).
	 * @param bool         [$isPositive]      True or false to add positive/negative messages. Default is true.
     *
     * @return \Kristuff\Miniweb\Mvc\Feedback
     */
    public static function feedback(?string $message = null, bool $isPositive = true): \Kristuff\Miniweb\Mvc\Feedback
    {
        // get the Feedback instance
        $feed = Factory::getFactory()->getFeedback();

        // add postive message ?
        if (isset($message) && $isPositive === true) {
            $feed->addPositive($message);

        // add negative message ?
        } elseif (isset($message) && $isPositive === false) {
            $feed->addNegative($message);
        }

        // return the Feedback instance
        return $feed;
    } 

    /**
     * Creates and returns a new TaskResponse 
     *
     * @access public
     * @static
	 * @param int          [$code]          The response code (must matchs a valid http response code)
	 * @param string       [$message]       The response main message
	 * @param array        [$data]          The response data (if any)
	 * @param array        [$errors]        The response data (if any)
     *
     * @return \Kristuff\Miniweb\Mvc\TaskResponse    
     */
    public static function createResponse(int $code = 200, string $message = '', array $data = [], array $errors = []): \Kristuff\Miniweb\Mvc\TaskResponse
    {
        return new TaskResponse($code, $message, $data, $errors); 
    }

    /**
     * Gets (or creates) and returns a Session instance
     *
     * @access public
     * @return \Kristuff\Miniweb\Http\Session
     */
    public static function session(): \Kristuff\Miniweb\Http\Session
    {
        return Factory::getFactory()->getSession();
    }

    /**
     * Gets the global instance of Http Request
     *
     * @access public
     * @return \Kristuff\Miniweb\Http\Request
     */
    public static function request(): \Kristuff\Miniweb\Http\Request
    {
        return Factory::getFactory()->getRequest();
    }

    /**
     * Gets the global instance of Cookie 
     *
     * @access public 
     * @return \Kristuff\Miniweb\Http\Cookie 
     */
    public static function cookie(): \Kristuff\Miniweb\Http\Cookie
    {
        return Factory::getFactory()->getCookie();
    }

    /**
     * Gets/Returns the global Token (anti CSRF) instance
     *
     * The Token is build on demand (created the first time function is called)
     *
     * @access public 
     * @static
     *
     * @return \Kristuff\Miniweb\Security\Token
     */
    public static function token(): \Kristuff\Miniweb\Security\Token
    {
         return Factory::getFactory()->getToken(self::session(), self::config('TOKEN_VALIDITY'));
    }
             
    /**
     * Gets/returns the configuration value for the given key
     *
     * @access public
     * @static   
     * @param string    $key                The key
     * @param string   [$configName]        The name of the configuration (optional)
     *
     * @return string|null    The key value is the key exists, otherwise null.
     */
    public static function config(string $key, ?string $configName = null): ?string
    {
        return Application::config($key, $configName);
    }   

    /**
     * Gets/returns the locale value for the given key (localized apps)
     *
     * @access public
     * @static   
     * @param string     $key               The key
     * @param string    [$locale]           The locale to use (the default is used if null). (optional)
     *
     * @return string|null    The key value is the key exists, otherwise null.
     */
    public static function text(string $key, ?string $locale = null): ?string
    {
        return Application::text($key, $locale);
    }         

    /**
     * Gets/returns the locale value for the given key (localized apps)
     *
     * @access public
     * @static
     * @param string     $key               The key
     * @param string     $section           The application section
     * @param string    [$locale]           The locale to use (the default is used if null). (optional)
     *
     * @return string|null    The key value is the key exists, otherwise null.
     */
    public static function textSection(string $key, string $section, string $locale = null): ?string
    {
        return Application::textSection($key, $section, $locale);
    }         
}