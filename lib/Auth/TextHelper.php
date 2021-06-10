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
 * @version    0.9.6
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Auth;

use Kristuff\Miniweb\Mvc\Application;

class TextHelper
{
    /**
     * Gets/returns the locale value for the given key (localized apps)
     * Overides default method by looking for an overide text in the main locale file 
     * then look in the default texts dist with this library  
     * 
     * @access public
     * @static
     * @param string        $key            The key
     * @param string        $locale         The locale to use (the default is used if null). (optional)
     *
     * @return string|null    The key value is the key exists, otherwise null.
     */
    public static function text(string $key, ?string $locale = null): ?string
    {
        $possibleOverideText = Application::text($key, $locale);
        
        return isset($possibleOverideText) ? $possibleOverideText :  
               Application::textSection($key, 'miniweb', $locale);
    }         
}