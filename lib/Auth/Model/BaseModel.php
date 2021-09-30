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

namespace Kristuff\Miniweb\Auth\Model;

use Kristuff\Miniweb\Auth\TextHelper;
use Kristuff\Miniweb\Data\Model\DatabaseModel;
use Kristuff\Miniweb\Http\Server;

/**
 * Class BaseModel 
 * Base class for all models of this library
 * Extends the DatabaseModel class with custom text function
 * and utils for Auth process
 */
abstract class BaseModel extends DatabaseModel
{
    /**
     * Gets/returns the locale value for the given key (localized apps)
     * Overides default method by looking for an overide text in the main locale file 
     * then look in the default texts dist with this library  
     * 
     * @access public
     * @static
     * @param string    $key        The key
     * @param string    $locale     The locale to use (the default locale is used if null). 
     *
     * @return string|null    The key value is the key exists, otherwise null.
     */
    public static function text(string $key, ?string $locale = null): ?string
    {
        return TextHelper::text($key, $locale);
    }         

    /** 
     * Gets whether the auth process use HTML email
     * 
     * @access public
     * @static
     *
     * @return bool         True if the auth process use HTML email, otherwise false.
     */
    public static function isHtmlEmailEnabled()
    {
        return self::config('AUTH_EMAIL_HTML') === true; 
    }

    /**
     * Log a message using the LOG_USER facility
     * 
     * @param int       $facility
     * @param string    $message
     *
     * @return void
     */
    protected static function log(int $facility, string $message): void
    {
        openlog(Server::serverName(), LOG_PERROR | LOG_CONS, LOG_USER);
        syslog($facility, $message);
        closelog();
    }
}