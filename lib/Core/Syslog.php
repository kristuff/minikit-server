<?php declare(strict_types=1);

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.22 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */

namespace Kristuff\Minikit\Core;

use Kristuff\Minikit\Http\Server;

/**
 * Class Syslog
 *
 */
class Syslog
{
    /**
     * Log a message using the LOG_USER facility
     * 
     * @param int       $priority
     * @param string    $message
     *
     * @return bool
     */
    protected static function log(int $priority, string $message): bool
    {
        return openlog(Server::serverName(), LOG_PERROR | LOG_CONS | LOG_PID, LOG_USER) &&
               syslog($priority, $message) && 
               closelog();
    }

    /**
     * Log an info message using the LOG_USER facility
     * 
     * @param string    $message
     *
     * @access public
     * @static
     * @return bool
     */
    public static function info(string $message): bool
    {
        return self::log(LOG_INFO, $message);
    }

    /**
     * Log a warning message using the LOG_USER facility
     * 
     * @param string    $message
     *
     * @access public
     * @static
     * @return bool
     */
    public static function warning(string $message): bool
    {
        return self::log(LOG_WARNING, $message);
    }
}
