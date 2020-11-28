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
 * @version    0.9.2
 * @copyright  2017-2020 Kristuff
 */

namespace Kristuff\Miniweb\Auth\Model;

use Kristuff\Miniweb\Mvc\TaskResponse;
use Kristuff\Miniweb\Mvc\Application;
use Kristuff\Patabase\Database;

/** 
 * UserModel
 */
class UserModel extends UserBaseModel
{
   
    /** 
     * Checks if a username is already taken
     *
     * @access public
     * @static
     * @param string        $userName           The user's name
     *
     * @return bool         True if username alreay exists in database, otherwise False.
     */
    public static function isUserNameExists(string $userName)
    {
         return self::database()->select()
                                ->count('num')
                                ->from('user')
                                ->whereEqual('userName', $userName)
                                ->getColumn() > 0;
    }

    /** 
     * Checks if a email is already used
     *
     * @access public
     * @static   
     * @param  string       $userEmail          The user's email address.
     *
     * @return bool         True if given email already exists in database, otherwise false
     */
    public static function isUserEmailExists(string $userEmail)
    {
        return self::database()->select()
                               ->count('num')
                               ->from('user')
                               ->whereEqual('userEmail', $userEmail)
                               ->limit(1)
                               ->getColumn() > 0;
    }

    /** 
     * Gets a user's profile data, according to the given user's name or user's email
     *
     * @access protected
     * @static
     * @param string            $userNameOrEmail        The user's name or email address.
     *
     * @return mixed|bool       Returns the user as object if found, otherwise returns false.
     */
    protected static function getUserByUserNameOrEmail(string $userNameOrEmail)
    {
        $users = self::database()->select('userId','userName', 'userEmail', 'userPasswordHash', 'userAccountType', 
                                            'userDataDirectory', 'userHasAvatar', 'userDeleted', 'userActivated',
                                            'userCreationTimestamp',
                                            'userSuspensionTimestamp', 'userFailedLoginCount', 'userLastFailedLoginTimestamp')
                                ->from('user')
                                ->where()
                                    ->beginOr()
                                        ->equal('userName', $userNameOrEmail)
                                        ->equal('userEmail', $userNameOrEmail)
                                    ->closeOr()
                               ->where()->equal('userProvider','DEFAULT')
                               ->getOne('obj');

        return count($users) > 0 ? $users[0] : false;
    }   

    /** 
     * Get the user id user for given userName
     *
     * @access public
     * @static   
     * @param string            $userName               The user's name.
     *
     * @return mixed|bool       Returns the user'id if found, otherwise returns false.
     */
    public static function getUserIdByUsername(string $userName)
    {
        $userId = self::database()->select('userId')
                               ->from('user')
                               ->whereEqual('userName', $userName)
                               ->getColumn();
        return !empty($userId) ?  $userId : false;
    }
    /**
     * Validates that the username is not already taken
     * 
     * @access public
     * @static
	 * @param TaskResponse      $response               The TaskResponse instance.
     * @param string            $userName               The user's name.
     *
	 * @return bool             True if the given username is valid, otherwise false.   
     */
    public static function validateUserNameNoConflict(TaskResponse $response, string $userName)
    {
        //check if new username already exists (conflict)
        return $response->assertFalse(self::isUserNameExists($userName), 409, self::text('USER_NAME_ERROR_ALREADY_TAKEN'));
    }
    
    /**
     * Validates that email is not already token
     *
     * @access public
     * @static
     * @param TaskResponse      $response               The TaskResponse instance.
     * @param string            $userEmail              The user's email address.
     * 
     * @return bool             True if the given username is valid, otherwise false. 
     */
    public static function validateUserEmailNoConflict(TaskResponse $response, string $userEmail)
    {
        // check if new email already exists (conflict)
        return $response->assertFalse(self::isUserEmailExists($userEmail), 409, self::text('USER_EMAIL_ERROR_ALREADY_TAKEN'));
    }

    /** 
     * Gets the number of users in database
     * 
     * @access public
     * @static
     * 
     * @return int    
     */
    public static function countProfiles()
    {
        return (int) self::database()->select()
                                     ->count('total')
                                     ->from('user')
                                     ->getColumn();
    }

    /** 
     * Gets user profiles
     *
     * //TODO PARAMS
     * 
     * @access public
     * @static
     * 
     * @return TaskResponse    
     */
    public static function getProfiles(int $userId = null, int $limit = 0,int $offset = 0, string $orderBy = 'name')
    {
        // the return response
        $response = TaskResponse::create();
        
        // need admin auth
        if (self::validateAdminPermissions($response)){
            
            //prepare query
            $query = self::database()->select('userId','userName', 'userEmail', 'userAccountType', 
                                                'userDataDirectory', 'userHasAvatar', 'userDeleted', 'userActivated', 
                                                'userLastLoginTimestamp', 'userCreationTimestamp', 'userDeletionTimestamp',
                                                'userSuspensionTimestamp', 'userFailedLoginCount', 'userLastFailedLoginTimestamp')
                                     ->from('user');
            // userId?
            if (!empty($userId)){ $query->whereEqual('UserId', $userId); }    

            // order?
            switch($orderBy) {
                case 'name': $query->orderAsc('name');  break;
                case 'id':   $query->orderAsc('id');    break;
            }
        
            // paging?
            if ( $offset > 0 ) { $query->offset($limit); }
            if ( $limit  > 0 ) { $query->limit($limit);  }
        
            // get user(s)
            $users = $query->getAll('assoc');
        
            // all elements of array passed to Filter::XSSFilter for XSS sanitation.
            // Removes (possibly bad) JavaScript etc from the user's values
            \Kristuff\Miniweb\Core\Filter::XssFilter($users);
       
            // set/fix additional infos avatar url and ...
            foreach($users as $key => &$val) {
            
                $users[$key]['userAvatarUrl'] =  UserAvatarModel::getAvatarUrl(
                    $users[$key]['userHasAvatar'], 
                    $users[$key]['userId'],
                    Application::getUrl()
                );
                $users[$key]['userAccountTypeRendered'] = self::getReadableAccountType( $users[$key]['userAccountType']);
            }

            // set data to return
            $response->setdata(['total'  => self::countProfiles(),
                                'offset' => $offset,
                                'limit'  => $limit,
                                'items'  => $users]);
        }

        // return response
        return $response;
    }    

    /** 
     * Create the table user
     * 
     * @access public
     * @static
     * @param Database      $database           The Database instance
     *
     * @return  bool        True if the table has been created, otherwise False
     */
    public static function createTable(Database $database)
    {
        $timeColumn = $database->getDriverName() === 'sqlite' ? 'int' : 'timestamp';

        return $database->table('user')
                        ->create()
                        ->column('userId',                       'int',             'NOT NULL',   'PK',  'AI')               
                        ->column('userEmail',                    'varchar(255)',    'NOT NULL',   'UNIQUE')
                        ->column('userName',                     'varchar(64)',     'NOT NULL',   'UNIQUE')
                        ->column('userPasswordHash',             'varchar(255)',    'NULL',       'DEFAULT', 'NULL')
                        ->column('userActivationHash',           'varchar(255)',    'NULL',       'DEFAULT', 'NULL')
                        ->column('userPasswordResetHash',        'varchar(255)',    'NULL',       'DEFAULT', 'NULL')
                        ->column('userApiTokenHash',             'varchar(255)',    'NULL',       'DEFAULT', 'NULL')
                        ->column('userSessionId',                'varchar(48)',     'NULL',       'DEFAULT', 'NULL')                
                        ->column('userActivated',                'smallint',        'NOT NULL',   'DEFAULT', 0)
                        ->column('userDeleted',                  'smallint',        'NOT NULL',   'DEFAULT', 0)
                        ->column('userAccountType',              'smallint',        'NOT NULL',   'DEFAULT', 1)
                        ->column('userHasAvatar',                'smallint',        'NOT NULL',   'DEFAULT', 0)
                        ->column('userProvider',                 'varchar(48)',     'NOT NULL',   'DEFAULT', 'DEFAULT')
                        ->column('userRememberMeToken',          'varchar(64)',     'NULL')
                        ->column('userFailedLoginCount',         'smallint',        'NOT NULL',   'DEFAULT', 0)
                        ->column('userDataDirectory',            'varchar(64)',     'NULL')
                        ->column('userCreationTimestamp',        $timeColumn,       'NULL')
                        ->column('userSuspensionTimestamp',      $timeColumn,       'NULL')
                        ->column('userDeletionTimestamp',        $timeColumn,       'NULL')
                        ->column('userLastLoginTimestamp',       $timeColumn,       'NULL')
                        ->column('userLastFailedLoginTimestamp', $timeColumn,       'NULL')
                        ->column('userPasswordResetTimestamp',   $timeColumn,       'NULL')
                        ->execute();
    }
}