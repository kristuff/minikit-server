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
 * @version    0.9.9
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Data\Model;

use Kristuff\Miniweb\Mvc\Model;
use Kristuff\Miniweb\Data\Core\DatabaseFactory;

/**
 * Class DatabaseModel
 *
 */
abstract class DatabaseModel extends Model
{
    /**
     * Gets and returns the global Database instance
     *
     * @access public
     * @static method
     *
     * @return \kristuff\Patabase\Database
     * @throw  ?
     */
    public static function database()
    {
        return DatabaseFactory::getFactory()->getConnection([
            'driver'    => self::config('DB_DRIVER'),
            'hostname'  => self::config('DB_HOST'), 
            'database'  => self::config('DB_NAME'), 
            'port'      => self::config('DB_PORT'), 
            'charset'   => self::config('DB_CHARSET'),
            'username'  => self::config('DB_USER'), 
            'password'  => self::config('DB_PASSWORD')
        ]);
    }
}