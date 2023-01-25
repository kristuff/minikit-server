<?php

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.23 
 * Copyright (c) 2017-2023 Christophe Buliard  
 */

namespace Kristuff\Minikit\Auth\Data;

use Kristuff\Minikit\Data\Model\DatabaseModel;
use Kristuff\Patabase\Database;
use Kristuff\Patabase\Output;

/** 
 * SettingsCollection
 * 
 * Handle application setting collection in database 
 */
class SettingsCollection extends DatabaseModel
{
    /** 
     * Get the settings for whole application stored in database. Returns an indexed array
     *
     * @access public
     * @static
     * @param string    $orderBy
     *
     * @return array        
     */
    public static function getList(?string $orderBy = 'settingKey'): array
    {
        $query = self::database()->select('settingKey', 'settingValue')
                                 ->from('minikit_settings');
       
        // order
        if (in_array($orderBy, ['settingKey', ])){
            $query->orderAsc($orderBy);
        }        

        return $query->getAll(Output::OBJ);
    }

    /**
     * 
     * @access public
     * @static 
	 * @param string        $settingKey
     * @param mixed         $value
     * 
     * @return bool
     */
    public static function updateAppSettingsByName(string $settingKey, $value): bool
    {
        $query = self::database()->update('minikit_settings')
                                 ->setValue('settingValue', $value)
                                 ->whereEqual('settingKey', $settingKey);
        
        if ($query->execute() && $query->rowCount() === 1){
            return true;
        } else {
            // try to insert missing parameter
            $query = self::database()->insert('minikit_settings')
                                     ->setValue('settingValue', $value)
                                     ->setValue('settingKey', $settingKey);

            return $query->execute() && $query->rowCount() === 1;
        }          
    }

    /** 
     * Create the table minikit_settings
     *
     * @access public
     * @static
     * @param Database      $database           The Database instance
     *
     * @return bool         True if the table has been created, otherwise False
     */
    public static function createTableSettings(Database $database)
    {
        return $database->table('minikit_settings')
                        ->create()
                        ->column('settingId',    'INT',          'NOT NULL', 'PK',  'AI')               
                        ->column('settingKey',   'VARCHAR(64)',  'NULL')
                        ->column('settingValue', 'VARCHAR(255)', 'NULL')
                        ->execute();
    }
}