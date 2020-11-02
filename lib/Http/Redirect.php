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
 * @version    0.9.1
 * @copyright  2017-2020 Kristuff
 */

namespace Kristuff\Miniweb\Http;

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
	 * @param bool    $permanent   true for permanent redirect. Default is false. 
	 * @param bool    $exit        true to stop application via exit(). Default is false.
     * 
     * @return void
	 */
	public static function url(string $uri, bool $permanent = false, bool $exit = false): void
	{
        // set redirect header with according status code
		header("Location: " . $uri, true, $permanent ? 301 : 302);

        /**
         * As there is no guarantee the client respects the Location header (curl and some crawlers will
         * ignore header above) we kill application to prevent fetching views or undesired actions. 
         * @see http://thedailywtf.com/articles/WellIntentioned-Destruction
         * looking for aonther way...
         */
         if ($exit) {
            // \Kristuff\Miniweb\Core\SafeExit::exit();
            exit();
         }
	}
}