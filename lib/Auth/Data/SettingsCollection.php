<?php

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.19 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */

namespace Kristuff\Minikit\Auth\Data;

use Kristuff\Minikit\Data\Model\DatabaseModel;
use Kristuff\Patabase\Database;

/** 
 * SettingsCollection
 * 
 * Handle application setting collection in database 
 */
class SettingsCollection extends DatabaseModel
{
    /** 
     * Create the table app_setting
     *
     * @access public
     * @static
     * @param Database      $database           The Database instance
     *
     * @return bool         True if the table has been created, otherwise False
     */
    public static function createTableSettings(Database $database)
    {
        return $database->table('app_setting')
                        ->create()
                        ->column('settingId',    'int',          'NOT NULL', 'PK',  'AI')               
                        ->column('settingName',  'varchar(64)',  'NULL')
                        ->column('settingValue', 'varchar(255)', 'NULL')
                        ->execute();
    }
}