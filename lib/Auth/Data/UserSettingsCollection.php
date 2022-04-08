<?php declare(strict_types=1);

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
 * Class UserSettingsCollection 
 *
 */
class UserSettingsCollection extends DatabaseModel
{
    /** 
     * Get an array of settings for given userId
     * 
     * @access public
     * @static 
     * todo doc
     * 
     * @return array         
     */
    public static function getSettings($userId, string $settingName = null, int $limit = 0, int $offset = 0, string $orderBy = 'settingName')
    {
        // prepare query
        $query = self::database()->select('settingName', 'settingValue')
                                 ->from('user_setting')
                                 ->whereEqual('userId', (int) $userId);

        if ($limit > 0){
            $query->limit($limit);
            $query->offset($offset);
        }   

        // optional filter
        if (!empty($settingName)){
            $query->whereEqual('settingName', $settingName);
            $query->limit(1);
        }        

        // order
        if (in_array($orderBy, ['settingName'])){
            $query->orderAsc($orderBy);
        }        

        return $query->getAll('assoc');
    }

    /**
     * Update a value in user's settings 
     * 
     * @access public
     * @static
     * @param int               $userId             The user's id
     * @param string            $paramName          The setting parameter name
     * @param mixed             $value              The setting parameter value
     * 
     * @return bool             True if the setting parameter has been edited, otherwise false
     */
    public static function updateUserSettingsByName(int $userId, string $paramName, $value): bool
    {
        $query = self::database()->update('user_setting')
                                 ->setValue('settingValue', $value)
                                 ->whereEqual('settingName', $paramName)
                                 ->whereEqual('userId', (int) $userId);
 
        return $query->execute() && $query->rowCount() === 1;          
    }

    /**
     * Delete all settings for given user  
     * 
     * @access public
     * @static
     * @param mixed             $userId             The user's id
     * 
     * @return bool             True if the settings have been sucessfully deleted, otherwise false
     */
    public static function deleteUserSettings($userId): bool
    {
        $query = self::database()->delete('user_setting')
                                ->whereEqual('userId', $userId);

        return $query->execute();          
    }

   /** 
     * Create the table user_settings
     *
     * @access public
     * @static
     * @param Database      $database           The Database instance
     *
     * @return bool         True if the table has been created, otherwise False
     */
    public static function createTableSettings(Database $database)
    {
        return $database->table('user_setting')
                        ->create()
                        ->column('settingId',   ' int', 'NOT NULL', 'PK',  'AI')               
                        ->column('userId',       'int', 'NOT NULL')
                        ->column('settingName',  'varchar(64)', 'NULL')
                        ->column('settingValue', 'varchar(255)', 'NULL')
                        ->fk('fk_setting_user',  'userId', 'user', 'userId')
                        ->execute();
    }
}