<?php

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.17 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */


namespace Kristuff\Minikit\Data\Model;

use Kristuff\Minikit\Mvc\Model;
use Kristuff\Minikit\Data\Core\DatabaseFactory;

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
     * @static
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