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
 * @version    0.9.3
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Auth\Model;

use Kristuff\Miniweb\Auth\Model\UserModel;
use Kristuff\Miniweb\Auth\Model\UserLoginModel;
use Kristuff\Miniweb\Mvc\TaskResponse;
use Kristuff\Miniweb\Core\Json;
use Kristuff\Miniweb\Core\Path;
use Kristuff\Patabase\Database;

/**
 * Class AppSettingsModel 
 *
 */
class AppSettingsModel extends UserModel
{
   

    /**
     * Get app settings 
     *
     * Get the settings for a given user. Returns a Response with users settings as data, 
     * or a key/value array if $returnArray is True.
	 *
     * @access public
     * @static
     * @param  bool             $returnArray    True to return an array instead of a complete Response. Default is False.
     *
     * @return mixed|array        
     */
    public static function getAppSettings($returnArray = false)
    {
        // the return response
        $response = TaskResponse::create();
        $data = [];

        // validate userId (self or admin permissions)
        if (self::validateAdminPermissions($response)){
            
            // get users settings data
            foreach(self::getList() as $item) {
                $data[$item['settingName']] = \Kristuff\Miniweb\Core\Filter::XssFilter($item['settingValue']);
            }
        }

        // return array
        if ($returnArray) {
            return $data;
        }

        // return complete response with data
        $response->setData($data);
        return $response;
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
    public static function editAppSettings(string $paramName, $value, string $tokenValue, string $tokenKey)
    {
        // the return response
        $response = TaskResponse::create();

        // token valid and is admin?
        if (self::validateAdminPermissions($response) &&
            self::validateToken($response, $tokenValue, $tokenKey)){
            
            // try to update    
            $query = self::updateAppSettingsByName($paramName, $value);
            if ($response->assertTrue($query, 500, 'TODO')){
                
                // feedback
                $response->setMessage('Application settings updated sucessfully TODO');             
            }
        }

        // return response        
        return $response;
    }
    
    /**
	 * Validate the param/value given. TODO §§§§§§
     *     
     * Gets whether the param name is valid or not (check XXXXXXXXXXX) and in case it not, register error(s) in response. 
	 *
     * @access protected
     * @static
	 * @param  TaskResponse    $response
	 * @param  mixed            $UserIdXXXXXXXXXXXXXXX TODO
	 * @param  mixed            $UserIdXXXXXXXXXXXXXXX TODO
	 *
	 * @return bool             True if the given XXXXXXXXX is valid, otherwise false.   
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
     * @return bool TODO
     */
    private static function updateAppSettingsByName(string $settingName, $value)
    {
        $query = self::database()->update('app_setting')
                                 ->setValue('settingValue', $value)
                                 ->whereEqual('settingName', $settingName);

        return $query->execute() && $query->rowCount() === 1;          
    }

    /** 
     * 
     * todo
     * 
     * @access public
     * @static 
     *
     */
    private static function getList($settingName = null, $limit = 0, $offset = 0, $orderBy = 'settingName')
    {
        // prepare query
        $query = self::database()->select('settingName', 'settingValue')
                                 ->from('app_setting');

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
     * Will check for a json file named 'app.settings.default.json' in app config path
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
        
        // prepar query
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

    /** 
     * Create the table app_setting
     *
     * @access public
     * @static
     * @param Database      $database           The Database instance
     *
     * @return bool         True if the table has been created, otherwise False
     */
    public static function createTableSettings(Database $database)
    {
        return $database->table('app_setting')
                        ->create()
                        ->column('settingId',   ' int', 'NOT NULL', 'PK',  'AI')               
                        ->column('settingName',  'varchar(64)', 'NULL')
                        ->column('settingValue', 'varchar(255)', 'NULL')
                        ->execute();
    }
}