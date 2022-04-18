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
use Kristuff\Patabase\Output;
use Kristuff\Patabase\Query\Select;

/** 
 * UserHostsCollection
 * 
 * Handle user hosts meta collection in database 
 * 
 */
class UserHostsCollection extends DatabaseModel
{
    /** 
     * Create the table minikit_userhosts
     * 
     * @access public
     * @static
     * @param Database  $database           The Database instance
     *
     * @return bool     True if the table has been created, otherwise False
     */
    public static function createTable(Database $database): bool
    {
        $timeColumn = self::getTimeColumnType($database);

        return $database->table('minikit_userhosts')
                        ->create()
                        ->column('userHostId',               'BIGINT',        'NOT NULL',   'PK',  'AI')               
                        ->column('userId',                   'BIGINT',        'NOT NULL')
                        ->column('userHostRememberMeToken',  'VARCHAR(255)',  'NULL')
                        ->column('userLastLoginTimestamp',   $timeColumn,     'NOT NULL')
                        ->column('userHostIP',               'VARCHAR(255)',  'NOT NULL')            
                        ->column('userHostAgent',            'VARCHAR(255)',  'NOT NULL')            
                        ->fk('fk_users_hosts',  'userId',    'minikit_users', 'userId')    
                        ->execute(); 
    }
}