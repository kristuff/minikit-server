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

use Kristuff\Minikit\Auth\Data\SettingsCollection;
use Kristuff\Minikit\Mvc\TaskResponse;
use Kristuff\Minikit\Core\Json;
use Kristuff\Minikit\Core\Path;
use Kristuff\Patabase\Database;
use Kristuff\Minikit\Core\Filter;

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
        foreach(SettingsCollection::getList() as &$item) {
            $item->settingValue = Filter::XssFilter($item->settingValue);
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
            self::validatesettingKey($response, $paramName)){
            
            // try to update    
            $query = SettingsCollection::updateAppSettingsByName($paramName, $value);
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
    protected static function validatesettingKey(TaskResponse $response, ?string $paramName = null)
    {
        return $response->assertFalse(empty($paramName), 405, sprintf(self::text('ERROR_PARAM_NULL_OR_EMPTY'), 'name'));
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
        $confileJsonFile = self::config('CONFIG_DEFAULT_PATH') . 'settings.default.json';

        if (!Path::fileExists($confileJsonFile)){
            return false;
        }
        
        // prepare query
        $query = $database->insert('minikit_settings')
                          ->prepare('settingKey', 'settingValue');

        foreach (Json::fromFile($confileJsonFile) as $item){
            $query->values([
                'settingKey'   => $item['name'], 
                'settingValue'  => $item['value'] 
            ]);

            if (!$query->execute()){
                return false;
            }            
        }
        return true;   
    }

}