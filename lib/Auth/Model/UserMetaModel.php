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
use Kristuff\Minikit\Auth\Model\UserLoginModel;
use Kristuff\Minikit\Auth\Data\UserMetaCollection;
use Kristuff\Minikit\Mvc\TaskResponse;
use Kristuff\Minikit\Core\Json;
use Kristuff\Minikit\Core\Path;
use Kristuff\Minikit\Core\Filter;
use Kristuff\Patabase\Database;

/**
 * Class UserMetaModel 
 *
 * The UserMetaModel class contains method to get and edit user's settings
 * Actually just key/value data stored in the table minikit_usermeta. 
 */
class UserMetaModel extends UserModel
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
    public static function getUserMeta(int $userId, bool $returnArray = false)
    {
        // the return response
        $response = TaskResponse::create();

        // validate userId (self or admin permissions)
        if (self::validateUserId($response, $userId)){
            
            // get users settings data
            $meta = (UserMetaCollection::getMeta($userId));
            foreach ($meta as &$item) {
               $item->userMetaValue = Filter::XssFilter($item->userMetaValue);
            }
        }

        // return array
        if ($returnArray) {
            return $meta;
        }

        // return response with array
        $response->setData($meta);
        return $response;
    }

    /** 
     * Reset user settings
     *
     * Deletes and recreates all default settings for a given user. This action expects the 
     * token given by UserMetaModel::getSettingsDatas() to be passed as argument.
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
        $response = TaskResponse::create();

        // validate token and  userid
        if (self::validateToken($response, $tokenValue, $tokenKey) &&
            self::validateUserId($response, $userId)){

            // try to delete and reload
            $result = UserMetaCollection::deleteUserMeta($userId) && 
                      self::loadDefaultSettings(self::database(), $userId);
            
            if ($response->assertTrue($result, 500, 'TODO')){

                // if true, reload settings in session
                // get and reset user settings data into session
                $settingsData = UserMetaModel::getUserMeta(self::getCurrentUserId(), true);
                self::session()->set('userSettings', $settingsData);
                $response->setMessage('User settings reseted sucessfully TODO'); //TODO text locale             
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
     * @param string            $key                The setting parameter name
     * @param mixed             $value              The setting parameter value
     * @param string            $tokenValue         The token value
     * @param string            $tokenKey           The token key
     *
     * @return TaskResponse
     */
    public static function editUserSettings($userId, string $key, $value, string $tokenValue, string $tokenKey)
    {
        // the return response
        $response = TaskResponse::create();

        // validate token, userid, param name and value
        if (self::validateToken($response, $tokenValue, $tokenKey) &&
            self::validateUserId($response, $userId) && 
            self::validatePermissions($response, $userId) && 
            self::validateuserMetaKeyAndValue($response, $key, $value)){

            // try to update
            $query = UserMetaCollection::updateUserMetaByKey((int) $userId, $key, $value); 

            // insert new key if doesn't exist
            if ($query === false && UserMetaCollection::keyExists((int) $userId, $key) === false ) {
                $query = UserMetaCollection::insertUserMeta((int) $userId, $key, $value); 
            }           

            if ($response->assertTrue($query, 500, 'todo' . $query)){

                // get and reset user settings data into session
                $settingsData = UserMetaModel::getUserMeta(self::getCurrentUserId(), true);
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
	 * @param mixed             $key 
	 * @param mixed             $value
	 *
	 * @return bool                
	 */
    protected static function validateuserMetaKeyAndValue(TaskResponse $response, $key, $value)
    {
        // param name and value must be set
        if ($response->assertFalse(empty($key), 405, self::text('USER_SETTING_NAME_ERROR_EMPTY')) &&
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
     * Load the default setting 
     * Will check for a json file named 'user.settings.default.json' in app config default path
     * 
     * @access public
     * @static
     * @param Database      $database           The Database instance
     *
     * @return bool         True if the table has been created, otherwise False
     */
    public static function loadDefaultSettings(Database $database, int $userId = null)
    {
        $confileJsonFile = self::config('CONFIG_DEFAULT_PATH') . 'usermeta.default.json';
         
        if (!Path::fileExists($confileJsonFile)) {
            return false;
        }
        
        $query = $database->insert('minikit_usermeta')
                          ->prepare('userMetaKey', 'userMetaValue', 'userId');

        foreach (Json::fromFile($confileJsonFile) as $item){

            $query->values([
                'userMetaKey'   => $item['name'], 
                'userMetaValue' => $item['value'], 
                'userId'        => $userId
            ]);

            if (!$query->execute()){
                return false;
            }            
        }
        return true;   
    }
}