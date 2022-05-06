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
use Kristuff\Patabase\Output;

/**
 * Class UserMetaCollection 
 *
 */
class UserMetaCollection extends DatabaseModel
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
    public static function getMeta($userId, string $settingName = null, int $limit = 0, int $offset = 0, string $orderBy = 'settingName')
    {
        // prepare query
        $query = self::database()->select('userMetaKey', 'userMetaValue')
                                 ->from('minikit_usermeta')
                                 ->whereEqual('userId', (int) $userId);

        if ($limit > 0){
            $query->limit($limit);
            $query->offset($offset);
        }   

        // optional filter
        if (!empty($settingName)){
            $query->whereEqual('userMetaKey', $settingName);
            $query->limit(1);
        }        

        // order
        if (in_array($orderBy, ['userMetaKey'])){
            $query->orderAsc($orderBy);
        }        

        return $query->getAll(Output::OBJ);
    }

    /** 
     * Get an array of settings for given userId
     * 
     * @access public
     * @static 
     * @param int               $userId             The user's id
     * @param string            $paramName          The setting parameter name
     * 
     * @return array         
     */
    public static function keyExists($userId, string $key)
    {
        $query = self::database()->select('userMetaKey')
                                 ->from('minikit_usermeta')
                                 ->whereEqual('userMetaKey', $key)
                                 ->whereEqual('userId', (int) $userId);


        return count($query->getAll()) > 0;
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
    public static function insertUserMeta(int $userId, string $paramName, $value): bool
    {
        $query = self::database()->insert('minikit_usermeta')
                                 ->setValue('userMetaKey', $paramName)
                                 ->setValue('userMetaValue', $value)
                                 ->setValue('userId', (int) $userId);
 
        return $query->execute() && $query->rowCount() === 1;          
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
    public static function updateUserMetaByKey(int $userId, string $paramName, $value): bool
    {
        $query = self::database()->update('minikit_usermeta')
                                 ->setValue('userMetaKey', $paramName)
                                 ->whereEqual('userMetaValue', $value)
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
    public static function deleteUserMeta($userId): bool
    {
        $query = self::database()->delete('minikit_usermeta')
                                ->whereEqual('userId', $userId);

        return $query->execute();          
    }

    /** 
     * Create the table minikit_usermeta
     *
     * @access public
     * @static
     * @param Database      $database           The Database instance
     *
     * @return bool         True if the table has been created, otherwise False
     */
    public static function createTable(Database $database)
    {
        $textColumn = self::getTextColumnType($database);

        return $database->table('minikit_usermeta')
                        ->create()
                        ->column('userMetaId',             'BIGINT',   'NOT NULL',   'PK',  'AI')               
                        ->column('userId',                 'BIGINT',   'NOT NULL')
                        ->column('userMetaKey',            'VARCHAR(255)',   'NULL')
                        ->column('userMetaValue',          $textColumn,     'NULL')
                        ->fk('fk_users_meta',  'userId',   'minikit_users', 'userId')    
                        ->execute();
    }
}