<?php

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.22 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */

namespace Kristuff\Minikit\Auth\Model; 

use Kristuff\Minikit\Auth;
use Kristuff\Minikit\Auth\Data\SettingsCollection;
use Kristuff\Minikit\Auth\Data\UsersCollection;
use Kristuff\Minikit\Auth\Data\UserMetaCollection;
use Kristuff\Minikit\Auth\Data\UserHostsCollection;
use Kristuff\Minikit\Mvc\TaskResponse;
use Kristuff\Patabase\Driver\Sqlite\SqliteDatabase;
use Kristuff\Patabase\Database;

/** 
 * SetupModel
 */
class SetupModel extends \Kristuff\Minikit\Data\Model\SetupModel
{
    /** 
     * Create db tables
     * 
     * @access protected
     * @static
     * @param Database    $database
     * 
     * @return bool
     */
    protected static function createTables(&$database)
    {
        return UsersCollection::createTable($database) &&
               UserMetaCollection::createTable($database) &&
               UserHostsCollection::createTable($database) &&
               SettingsCollection::createTableSettings($database);
    }

    //TODO
    protected static function validatesAdminUserInputs(TaskResponse &$response, string $adminName, string $adminPassword, string $adminEmail)
    {
        Auth\Model\UserModel::validateUserNamePattern($response, $adminName);
        Auth\Model\UserModel::validateUserEmailPattern($response, $adminEmail, $adminEmail);
        Auth\Model\UserModel::validateUserPassword($response, $adminPassword, $adminPassword);
        return $response;
    }

    /** 
     * Perform some checks 
     * 
     * @access public
     * @static
     * 
     * @return TaskResponse
     */
    public static function checkForInstall()
    {
        $response = parent::checkForInstall();
        $response->assertTrue(file_exists(Auth\Model\UserAvatarModel::getPath()), 500, self::text('USER_ERROR_AVATAR_PATH_MISSING')) &&
        $response->assertTrue(is_writable(Auth\Model\UserAvatarModel::getPath()), 500, self::text('USER_ERROR_AVATAR_PATH_PERMISSIONS'));
        return $response;
    } 
    
   

}