<?php

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.16 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */


namespace Kristuff\Minikit\Data\Model; 

use Kristuff\Minikit\Mvc\TaskResponse;
use Kristuff\Patabase\Driver\Sqlite\SqliteDatabase;
use Kristuff\Mishell\Console;

/** 
 * SetupModel
 */
class SetupModel extends \Kristuff\Minikit\Mvc\Model
{
    /** 
     * Gets whether program is run as Command Line Interface 
     * 
     * @access protected
     * @static
     * 
     * @return bool
     */
    protected static function isCommandLineInterface(): bool
    {
        return (php_sapi_name() === 'cli');
    }

    /** 
     * Log a message if run as cli 
     * 
     * @access protected
     * @static
     * @param string    $dbname     The database full path 
     * 
     * @return void
     */
    protected static function logSuccessMessage(string $message): void
    {
        if (self::isCommandLineInterface()){
            Console::log('  '.Console::text('[✓] ', 'green') . Console::text($message, 'white'));
        }
    }

    /**
     * Check if application is installed or not (check for db.config file)
     * 
     * @access public
     * @static
     * 
     * @return bool
     */
    public static function isInstalled(): bool
    {
        $fileName = self::config('DATA_CONFIG_PATH') . 'db.config.php';
        return file_exists($fileName);
    }

    /** 
     * Create sqlite db 
     * 
     * @access protected
     * @static
     * @param string    $dbname     The database full path 
     * 
     * @return SqliteDatabase|bool
     */
    protected static function createSqliteDatabase(string $dbname)
    {
        try{
            $database = SqliteDatabase::createInstance($dbname);
            return $database;
        } catch (\Exception $e){
            return false;
        }
    }

    /**
     * Delete sqlite database
     * 
     * @access public
     * @static
     * 
     * @return void
     */
    public static function deleteSqliteDatabase()
    {
       $configFileName = self::config('DATA_CONFIG_PATH') . 'db.config.php';
       $dbFileName = self::config('DB_NAME');

       if (file_exists($configFileName)){
           unlink($configFileName);
       }
    
       if (file_exists($dbFileName)){
           unlink($dbFileName);
       }
    }

    /**
     * Gets the db config 
     * 
     * @access public
     * @static
     * 
     * @return array
     */
    public static function getConfig(): array
    {
       $fileName = self::config('DATA_CONFIG_PATH') . 'db.config.php';
       $config = require $fileName;
       return $config;
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
        $response = TaskResponse::create();

        // perform checks
        $response->assertTrue(file_exists(self::config('DATA_PATH')), 500, self::text('DATA_PATH_ERROR_MISSING')) && 
            $response->assertTrue(is_writable(self::config('DATA_PATH')), 500, self::text('DATA_PATH_ERROR_PERMISSIONS')) ;

        $response->assertTrue(file_exists(self::config('DATA_CONFIG_PATH')), 500, self::text('DATA_CONFIG_PATH_ERROR_MISSING')) &&
            $response->assertTrue(is_writable(self::config('DATA_CONFIG_PATH')), 500, self::text('DATA_CONFIG_PATH_ERROR_PERMISSIONS'));
            
        $response->assertTrue(file_exists(self::config('DATA_DB_PATH')), 500, self::text('ERROR_DATA_DB_PATH_MISSING')) &&
            $response->assertTrue(is_writable(self::config('DATA_DB_PATH')), 500, self::text('ERROR_DATA_DB_PATH_PERMISSIONS'));
        
        $response->assertTrue(file_exists(self::config('DATA_LOG_PATH')), 500, self::text('ERROR_DATA_LOG_PATH_MISSING')) &&
            $response->assertTrue(is_writable(self::config('DATA_LOG_PATH')), 500, self::text('ERROR_DATA_LOG_PATH_PERMISSIONS'));
        
        return $response;
    } 
 
    /**
     * Create the db config file and write content file with given parameters
     * 
     * @access protected
     * @static
     * @param string    $dbDriver           The db driver. Must be a valid Patabase driver. 
     * @param string    $dbHost             The db host. 
     * @param string    $dbName             The db name. For sqlite diver, db name is the full path to db file. 
     * @param string    $dbPassword         The db password. Unuse by sqlite.
     * @param string    $dbPort             The db port. Default is 3306.
     * @param string    $dbCharset          The db charset. Default is utf8.
     * 
     * @return bool     
     */
    protected static function createDatabaseConfigFile(string $dbDriver, string $dbHost, string $dbName, string $dbUser, string $dbPassword, string $dbPort = "3306", string $dbCharset = 'utf8')
    {
        try {
            $fileName = self::config('DATA_CONFIG_PATH') . 'db.config.php';
            $date = (new \DateTime())->format('Y-m-d H:i:s');
            $content = '<?php
/** 
 * ----------------------------------------
 * Minikit database configuration file. 
 * Generated on '. $date .'
 * DO NOT MODIFY UNTIL YOU KNOW WHAT YOU DO
 * ----------------------------------------
 */
return array(
    "DB_DRIVER"     => "'. $dbDriver .'",
    "DB_HOST"       => "'. $dbHost .'",
    "DB_NAME"       => "'. $dbName .'",
    "DB_USER"       => "'. $dbUser .'",
    "DB_PASSWORD"   => "'. $dbPassword .'",
    "DB_PORT"       => "'. $dbPort .'",
    "DB_CHARSET"    => "'. $dbCharset .'"
);';

             file_put_contents($fileName, $content);
             return true;
        } catch(\Exception $e) {
             return false;
        }
    }
   
  
}