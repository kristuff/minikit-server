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


namespace Kristuff\Minikit\Http;

/**
 * Class Redirect
 *
 * Simple abstraction for redirecting the user to a certain page
 */
class Redirect
{
	/**
	 * Redirects to the defined url
     *
     * Send Location header and a redirect response code: 302 (default) or 301 for permanent redirect.
     * Make sure the 201 or a 3xx status code has not already been set before to use this function. 
     * Most contemporary clients accept relative URIs as argument to "Location", but some older clients 
     * require an absolute URI including the scheme, hostname and absolute path.
     *
	 * @access public
     * @static
	 * @param string  $url         The url.
	 * @param int     $code        The http response code. 
	 * @param bool    $exit        true to stop application via exit(). Default is false.
     * 
     * @return void
	 */
	public static function url(string $uri, int $code = 302, bool $exit = false): void
	{
        // set redirect header with according status code
		header("Location: " . $uri, true, $code);

        /**
         * As there is no guarantee the client respects the Location header (curl and some crawlers will
         * ignore header above) we kill application to prevent fetching views or undesired actions. 
         * @see http://thedailywtf.com/articles/WellIntentioned-Destruction
         * looking for aonther way...
         */
         if ($exit) {
            // \Kristuff\Minikit\Core\SafeExit::exit();
            exit();
         }
    }
    
    /**
	 * Redirects to the defined url with 301 http code (permanent redirect)
     *
     * Send Location header and a redirect response code: 301 (Moved Permanently)  
     * Make sure the 201 or a 3xx status code has not already been set before to use this function. 
     * Most contemporary clients accept relative URIs as argument to "Location", but some older clients 
     * require an absolute URI including the scheme, hostname and absolute path.
     *
	 * @access public
     * @static
	 * @param string  $url         The url.
	 * @param bool    $exit        true to stop application via exit(). Default is false.
     * 
     * @return void
	 */
	public static function permanent(string $uri, bool $exit = false): void
	{
        self::url($uri, 301, $exit);
	}

    /**
	 * Redirects to the defined url with 302 http code (temporary redirect)
     *
     * Send Location header and a redirect response code: 302 (Found)  
     * Make sure the 201 or a 3xx status code has not already been set before to use this function. 
     * Most contemporary clients accept relative URIs as argument to "Location", but some older clients 
     * require an absolute URI including the scheme, hostname and absolute path.
     *
	 * @access public
     * @static
	 * @param string  $url         The url.
	 * @param bool    $exit        true to stop application via exit(). Default is false.
     * 
     * @return void
	 */
	public static function temporary(string $uri, bool $exit = false): void
	{
        self::url($uri, 302, $exit);
	}

    /**
	 * Redirects to the defined url with 303 http code (see other)
     *
     * Send Location header and a redirect response code: 303 (see other) 
     * Make sure the 201 or a 3xx status code has not already been set before to use this function. 
     * Most contemporary clients accept relative URIs as argument to "Location", but some older clients 
     * require an absolute URI including the scheme, hostname and absolute path.
     *
	 * @access public
     * @static
	 * @param string  $url         The url.
	 * @param bool    $exit        true to stop application via exit(). Default is false.
     * 
     * @return void
	 */
	public static function seeOther(string $uri, bool $exit = false): void
	{
        self::url($uri, 303, $exit);
	}

}