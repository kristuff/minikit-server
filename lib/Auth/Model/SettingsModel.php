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

use Kristuff\Minikit\Auth\Model\UserModel;
use Kristuff\Minikit\Mvc\TaskResponse;
use Kristuff\Minikit\Core\Json;
use Kristuff\Minikit\Core\Path;
use Kristuff\Patabase\Database;

/**
 * Class SettingsModel 
 * Handle application settings
 */
class SettingsModel extends UserModel
{
    /**
     * Get app settings 
     * 
     * Get the settings for whole application. Returns an indexed array
     * Value is filtered by XssFilter
     *
     * @access public
     * @static
     *
     * @return array        
     */
    public static function getAppSettings(): array
    {
        $data = [];
        foreach(self::getList() as $item) {
            $data[$item['settingName']] = \Kristuff\Minikit\Core\Filter::XssFilter($item['settingValue']);
        }

        return $data;
    }

    /** 
     * Edit application settings
     *
     * Update the value of a global (application) setting parameter. This action expects a 
     * token and requires ADMIN permissions.
     *
     * @access public
     * @static
     * @param string            $paramName      The setting parameter name
     * @param mixed             $value          The setting parameter value
     * @param string            $tokenValue     The token value
     * @param string            $tokenKey       The token key
     *
     * @return TaskResponse
     */
    public static function editAppSettings(string $paramName, $value, string $tokenValue, string $tokenKey): TaskResponse
    {
        $response = TaskResponse::create();

        // token valid / is admin / name ok?
        if (self::validateAdminPermissions($response) &&
            self::validateToken($response, $tokenValue, $tokenKey) &&
            self::validateSettingName($response, $paramName)){
            
            // try to update    
            $query = self::updateAppSettingsByName($paramName, $value);
            if ($response->assertTrue($query, 500, self::text('UNKNOWN_ERROR'))){
                $response->setData([
                    'parameter' => $paramName,
                    'newValue'  => $value,
                ]);
            }
        }

        return $response;
    }
    
    /**
	 * Validate the param/value given.
     *     
     * Gets whether the param name is valid or not (check for empty) and in case it not, register error(s) in response. 
     * TODO length
	 *
     * @access protected
     * @static
	 * @param  TaskResponse    $response
	 * @param  string          $paramName
     * 
	 * @return bool                
	 */
    protected static function validateSettingName(TaskResponse $response, ?string $paramName = null)
    {
        return $response->assertFalse(empty($paramName), 405, sprintf(self::text('ERROR_PARAM_NULL_OR_EMPTY'), 'name'));
    }

    /**
     * 
     * @access private
     * @static 
	 * @param string        $settingName
     * @param mixed         $value
     * 
     * @return bool
     */
    private static function updateAppSettingsByName(string $settingName, $value): bool
    {
        $query = self::database()->update('app_setting')
                                 ->setValue('settingValue', $value)
                                 ->whereEqual('settingName', $settingName);
        
        if ($query->execute() && $query->rowCount() === 1){
            return true;
        } else {
            // try to insert missing parameter
            $query = self::database()->insert('app_setting')
                ->setValue('settingValue', $value)
                ->setValue('settingName', $settingName);

            return $query->execute() && $query->rowCount() === 1;
        }          
    }

    /** 
     * Get the settings for whole application stored in database. Returns an indexed array
     *
     * @access private
     * @static
     * @param string    $orderBy
     *
     * @return array        
     */
    private static function getList(?string $orderBy = 'settingName')
    {
        $query = self::database()->select('settingName', 'settingValue')
                                 ->from('app_setting');
       
        // order
        if (in_array($orderBy, ['settingName', ])){
            $query->orderAsc($orderBy);
        }        

        return $query->getAll('assoc');
    }

    /** 
     * Load the default setting 
     * Will check for a json file named 'app.settings.default.json' in app config default path
     * 
     * @access public
     * @static
     * @param Database      $database           The Database instance
     *
     * @return bool         True if the table has been created, otherwise False
     */
    public static function loadDefaultAppSettings(Database $database)
    {
        $confileJsonFile = self::config('CONFIG_DEFAULT_PATH') . 'app.settings.default.json';

        if (!Path::fileExists($confileJsonFile)){
            return false;
        }
        
        // prepare query
        $query = $database->insert('app_setting')
                          ->prepare('settingName', 'settingValue');

        foreach (Json::fromFile($confileJsonFile) as $item){
            $query->values([
                'settingName'   => $item['name'], 
                'settingValue'  => $item['value'] 
            ]);

            if (!$query->execute()){
                return false;
            }            
        }
        return true;   
    }

}