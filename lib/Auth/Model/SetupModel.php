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

namespace Kristuff\Minikit\Auth\Model; 

use Kristuff\Minikit\Auth;
use Kristuff\Minikit\Data\Auth\SettingsCollection;
use Kristuff\Minikit\Auth\Data\UsersCollection;
use Kristuff\Minikit\Auth\Data\UserSettingsCollection;
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
               UserSettingsCollection::createTableSettings($database) &&
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
    
    /**
     * Perform install
     *  
     * @access public
     * @static
     * @param string    $adminName 
     * @param string    $adminPassword
     * @param string    $adminEmail
     * @param string    $databaseName    
     * 
     * @return TaskResponse
     */
    public static function install(string $adminName, string $adminPassword, string $adminEmail, string $databaseName)
    {
        // the return response
        $response = TaskResponse::create();
 
// TODO SQLITE
        $databaseFilePath = realpath(self::config('DATA_DB_PATH')). '/'. $databaseName .'.db';
    
        // validate input
        if (Auth\Model\UserModel::validateUserNamePattern($response, $adminName) && 
            Auth\Model\UserModel::validateUserEmailPattern($response, $adminEmail, $adminEmail) &&
            Auth\Model\UserModel::validateUserPassword($response, $adminPassword, $adminPassword)){
                   
                // create datatabase, 
                // create tables
                // insert admin user
                // load default settings
                // save config file

// TODO SQLITE

                $database   = self::createSqliteDatabase($databaseFilePath);
            
              
            if ($response->assertTrue($database !== false, 500, 'Internal error : unable to create database') &&
                $response->assertTrue(self::createTables($database), 500, 'Internal error : unable to create tables'))  {
                
                self::logSuccessMessage('Database successfully created.');
    
                // create admin user and get its id
                $adminId = UsersCollection::insertAdminUser($adminEmail, $adminName, $adminPassword, $database);
                                
                if ($response->assertFalse($adminId === false, 500, 'Internal error : unable to insert admin user')) {

                    self::logSuccessMessage('Admin user successfully created.');
                
// TODO SQLITE
                    // load admin user settings and create config file
                    if ($response->assertTrue(Auth\Model\UserSettingsModel::loadDefaultSettings($database, (int) $adminId), 500, 'Internal error : unable to insert settings data') &&
                        $response->assertTrue(self::createDatabaseConfigFile('sqlite', 'localhost', $databaseFilePath, '', ''), 500, 'Internal error : unable to create config file')) {
                        
                        self::logSuccessMessage('Defaults settings successfully initialized.');

                        $response->setMessage('Congratulation! <br>Install was successful. You can now login.');
                    }
                }
            }
        }

        return $response;
    }

}