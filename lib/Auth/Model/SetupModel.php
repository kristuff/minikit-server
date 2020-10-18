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
 * @version    0.9.0
 * @copyright  2017-2020 Kristuff
 */

namespace Kristuff\Miniweb\Auth\Model; 

use \Kristuff\Miniweb\Auth;
use \Kristuff\Miniweb\Mvc\TaskResponse;
use \Kristuff\Patabase\Driver\Sqlite\SqliteDatabase;
use \Kristuff\Patabase\Database;
use \Kristuff\Mishell\Console;

/** 
 * SetupModel
 */
class SetupModel extends \Kristuff\Miniweb\Data\Model\SetupModel
{

    //todo
    protected static function logSuccessMessage(string $message)
    {
        if (self::isCommandLineInterface()){
            Console::log('  '.Console::text('[✓] ', 'green') . Console::text($message, 'white'));
        }
    }

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
        return Auth\Model\UserModel::createTable($database) &&
               Auth\Model\UserSettingsModel::createTableSettings($database);
    }

    protected static function validatesInput(TaskResponse &$response, string $adminName, string $adminPassword, string $adminEmail)
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
        // the return response
        $response = parent::checkForInstall();

        // perform checks
        $response->assertTrue(file_exists(Auth\Model\UserAvatarModel::getPath()), 500, self::text('USER_ERROR_AVATAR_PATH_MISSING')) &&
        $response->assertTrue(is_writable(Auth\Model\UserAvatarModel::getPath()), 500, self::text('USER_ERROR_AVATAR_PATH_PERMISSIONS'));

        if ( $response->success() ){
            $response->setMessage('All checks was successful');        
        }

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
                $adminId = Auth\Model\UserAdminModel::insertAdminUser($adminEmail, $adminName, $adminPassword, $database);
                                
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