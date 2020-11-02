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
 * @version    0.9.1
 * @copyright  2017-2020 Kristuff
 */

namespace Kristuff\Miniweb\Auth\Model;

use Kristuff\Miniweb\Auth\Model\UserModel;
use Kristuff\Miniweb\Auth\Model\UserLoginModel;
use Kristuff\Miniweb\Mvc\TaskResponse;
use Kristuff\Miniweb\Core\Json;
use Kristuff\Miniweb\Core\Path;
use Kristuff\Patabase\Database;

/**
 * Class UserSettingsModel 
 *
 * The UserSettingsModel class contains method to get and edit user's settings
 * Actually just key/value data stored in the table user_setting. 
 */
class UserSettingsModel extends UserModel
{
    /**
     * Return an array with data for edit settings.
	 *
     * @access public
     * @static
     *
     * @return array        
     */
    public static function getSettingsDatas()
    {
        return [
            'settingsToken'     => self::token()->value('settings'),
        ];
    }

    /**
     * Get user settings 
     *
     * Get the settings for a given user. Returns a Response with users settings as data, 
     * or a key/value array if $returnArray is True.
	 *
     * @access public
     * @static
     * @param  int              $userId         The user's id
     * @param  bool             $returnArray    True to return an array instead of a complete Response. Default is False.
     *
     * @return mixed|array        
     */
    public static function getUserSettings(int $userId, bool $returnArray = false)
    {
        // the return response
        $response = TaskResponse::create();
        $data = [];

        // validate userId (self or admin permissions)
        if (self::validateUserId($response, $userId)){
            
            // get users settings data
            foreach(self::getSettings($userId) as $item) {
                $data[$item['settingName']] = \Kristuff\Miniweb\Core\Filter::XssFilter($item['settingValue']);
            }
        }

        // return array
        if ($returnArray) {
            return $data;
        }

        // return response with array
        $response->setData($data);
        return $response;
    }

    /** 
     * Reset user settings
     *
     * Deletes and recreates all default settings for a given user. This action expects the 
     * token given by UserSettingsModel::getSettingsDatas() to be passed as argument.
     *
     * @access public
     * @static
     * @param mixed             $userId             The user's id
     * @param string            $tokenValue         The token value
     * @param string            $tokenKey           The token key
     *
     * @return TaskResponse
     */
    public static function resetUserSettings($userId, string $tokenValue, string $tokenKey)
    {
        // the return response
        $response = TaskResponse::create();

        // validate token and  userid
        if (self::validateToken($response, $tokenValue, $tokenKey) &&
            self::validateUserId($response, $userId)){

            // try to delete and reload
            $result = self::deleteUserSettings($userId) && 
                      self::loadDefaultSettings(self::database(), $userId);
            
            // if true reload settings in session
            if ($response->assertTrue($result, 500, 'TODO')){

                // get and reset user settings data into session
                $settingsData = UserSettingsModel::getUserSettings(self::getCurrentUserId(), true);
                self::session()->set('userSettings', $settingsData);

                // feedback //TODO text
                $response->setMessage('User settings reseted sucessfully TODO');             
            }
        }
        // return response 
        return $response;
    }

    /** 
     * Edit user settings
     *
     * Update the value of a setting parameter for the given user. This action expects the 
     * api token given by UserLoginModel::getPostLoginData() to be passed as argument.
     *
     * @access public
     * @static
     * @param mixed             $userId             The user's id
     * @param string            $paramName          The setting parameter name
     * @param mixed             $value              The setting parameter value
     * @param string            $tokenValue         The token value
     * @param string            $tokenKey           The token key
     *
     * @return TaskResponse
     */
    public static function editUserSettings($userId, string $paramName, $value, string $tokenValue, string $tokenKey)
    {
        // the return response
        $response = TaskResponse::create();

        // validate token, userid, param name and value
        if (self::validateToken($response, $tokenValue, $tokenKey) &&
            self::validateUserId($response, $userId) && 
            self::validatePermissions($response, $userId) && 
            self::validateSettingNameAndValue($response, $paramName, $value)){

            // try to update
            $query = self::updateUserSettingsByName((int) $userId, $paramName, $value);
            if ($response->assertTrue($query, 500, 'todo')){

                // get and reset user settings data into session
                $settingsData = UserSettingsModel::getUserSettings(self::getCurrentUserId(), true);
                self::session()->set('userSettings', $settingsData);
            }
        }

        // return response        
        return $response;
    }
    
    /**
	 * Validate the param/value given.
     *     
     * Gets whether the param name is valid or not (check for null values) 
     * and in case it not, register error(s) in response. 
	 *
     * @access protected
     * @static
	 * @param TaskResponse      $response
	 * @param mixed             $paramName 
	 * @param mixed             $value
	 *
	 * @return bool                
	 */
    protected static function validateSettingNameAndValue(TaskResponse $response, $paramName, $value)
    {
        // param name and value must be set
        if ($response->assertFalse(empty($paramName), 405, self::text('USER_SETTING_NAME_ERROR_EMPTY')) &&
            $response->assertFalse(empty($value),     405, self::text('USER_SETTING_VALUE_ERROR_EMPTY'))){
            return true;
        }
         
        return false;
    }
    
    /**
	 * Validate persmissions according to given user's id. 
     *     
     * Gets whether the id is valid or not (check for null, id of current user, or admin permissions) and
     * if not, registers error(s) in response. 
	 *
     * @access protected
     * @static
	 * @param TaskResponse      $response               The TaskResponse instance.
	 * @param mixed             $UserId                 The user's id
	 *
	 * @return bool             True if the given id is valid, otherwise false.   
	 */
    protected static function validatePermissions(TaskResponse $response, $userId)
    {
       return $response->assertTrue(UserLoginModel::isCurrentUserId($userId) || 
                                    UserLoginModel::isUserLoggedInAndAdmin(), 
                                    403, self::text('ERROR_INVALID_PERMISSIONS'));
    }

    /**
     * Delete all settings for given user  
     * 
     * @static
     * @param mixed             $userId             The user's id
     * 
     * @return bool             True if the settings have been sucessfully deleted, otherwise false
     */
    private static function deleteUserSettings($userId)
    {
        $query = self::database()->delete('user_setting')
                                ->whereEqual('userId', $userId);

        return $query->execute();          
    }

    /**
     * Update a value in user's settings 
     * 
     * @param int               $userId             The user's id
     * @param string            $paramName          The setting parameter name
     * @param mixed             $value              The setting parameter value
     * 
     * @return bool             True if the setting parameter has been edited, otherwise false
     */
    private static function updateUserSettingsByName(int $userId, string $paramName, $value)
    {
        $query = self::database()->update('user_setting')
                                 ->setValue('settingValue', $value)
                                 ->whereEqual('settingName', $paramName)
                                 ->whereEqual('userId', (int) $userId);
 
        return $query->execute() && $query->rowCount() === 1;          
    }

    /** 
     * Get an associtive array of settings for gibe userId
     * 
     * @access public
     * @static 
     * todo doc
     * 
     * @return array         
     */
    private static function getSettings($userId, string $settingName = null, int $limit = 0, int $offset = 0, string $orderBy = 'settingName')
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
     * Load the default setting 
     * Will check for a json file named 'user.settings.default.json' in app config path
     * 
     * @access public
     * @static
     * @param Database      $database           The Database instance
     *
     * @return bool         True if the table has been created, otherwise False
     */
    public static function loadDefaultSettings(Database $database, int $userId = null)
    {
        $confileJsonFile = self::config('CONFIG_PATH') . 'user.settings.default.json';
         
        if (!Path::fileExists($confileJsonFile)) {
            return false;
        }
        
        $query = $database->insert('user_setting')
                          ->prepare('settingName', 'settingValue', 'userId');

        foreach (Json::fromFile($confileJsonFile) as $item){

            $query->values([
                'settingName'   => $item['name'], 
                'settingValue'  => $item['value'], 
                'userId'        => $userId
            ]);

            if (!$query->execute()){
                return false;
            }            
        }
        return true;   
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