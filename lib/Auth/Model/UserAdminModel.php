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
 * @version    0.9.10
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Auth\Model;

use Kristuff\Miniweb\Auth\Model\UserLoginModel;
use Kristuff\Miniweb\Mvc\TaskResponse;
use Kristuff\Patabase\Database;

/** 
 * Class UserAdminModel
 *
 * Handles the suspension and (soft) deletion of users.
 * Requires admin permissions. 
 */
class UserAdminModel extends UserLoginModel
{

    /**
     * Return an array with data for users management.
	 *
     * @access public
     * @static
     *
     * @return array        
     */
    public static function getUserAdminDatas()
    {
        return [
            'usersToken'         => self::token()->value('users'),
        ];
    }

    /**
	 * Create new account
     *
     * Creates and activates a new account. This action expects the token given by UserAdmingModel::getUserAdminDatas() to 
     * be passed as argument. This action needs ADMIN permissions. The possible response codes are: 
     *  - 200 (success) 
     *  - 403 (no admin) 
     *  - 405 (invalid name email password or token) 
     *  - 409 (name or email conflict) 
     *  - 500 (Houston we..)
     *
     * @access public
     * @static
     * @param  string           $userName               The user's name.
     * @param  string           $userEmail              The user's email address.
     * @param  string           $userPassword           The user's password.
     * @param  string           $userPasswordRepeat     The repeated user's password.
     * @param  string           $token                  The token value.
     * @param  string           $tokenKey               The token key
     *
	 * @return TaskResponse
	 */
    public static function createNewAccount(string $userName = null, 
                                            string $userEmail = null, 
                                            string $userPassword = null,
                                            string $userPasswordRepeat = null, 
                                            string $token = null, 
                                            string $tokenkey = null)
    {
        // the return response
        $response = TaskResponse::create();
    
        // clean the inputs
		$userName           = strip_tags($userName);
		$userEmail          = strip_tags($userEmail);

        // validate the inputs
        if (self::validateToken($response, $token, $tokenkey) && 
            self::validateUserNamePattern($response, $userName) &&
            self::validateUserNameNoConflict($response, $userName) &&
            self::validateUserEmailPattern($response, $userEmail, $userEmail) &&
            self::validateUserEmailNoConflict($response, $userEmail) &&
            self::validateUserPassword($response, $userPassword, $userPasswordRepeat)){
                
            // crypt the password with the PHP 5.5's password_hash() function, results in a 60 character hash string.
		    // @see php.net/manual/en/function.password-hash.php for more, especially for potential options
		    $userPasswordHash = password_hash($userPassword, PASSWORD_DEFAULT);
		
            // write user data to database
            $userId = self::writeNewAccount($userName, $userEmail, $userPasswordHash);

            if ($response->assertTrue($userId !== false, 500, self::text('USER_NEW_ACCOUNT_ERROR_CREATION_FAILED')) &&
                $response->assertTrue(UserSettingsModel::loadDefaultSettings(self::database(), (int) $userId), 500, self::text('USER_NEW_ACCOUNT_ERROR_DEFAULT_SETTINGS'))){

                $response->setMessage(self::text('USER_ACCOUNT_SUCCESSFULLY_CREATED'));                       
            }
        }

        // return response
        return $response;            
    }

    /**
     * Update suspension status.
     * 
     * Sets the suspension values for a given user. This action expects the 
     * token given by UserAdmingModel::getUserAdminDatas() to be passed as argument. This 
     * action need ADMIN permissions. The possible response codes are: 
     * - 200 (success) 
     * - 403 (no admin) 
     * - 405 (invalid userId, own userId or invalid token) 
     * - 500 (Houston we..)
     *
     * @access public
     * @static
     * @param  mixed            $userId             The user'id
     * @param  string           $token              The token
     * @param  string           $tokenKey           The token key
     * @param  int              [$suspensionDays]   (optional) The suspension value in days. Default is 0.
     *
     * @return TaskResponse    
     */
    public static function updateSuspensionStatus($userId, string $token, string $tokenKey, int $suspensionDays = 0)
    {
        // the return response
        $response = TaskResponse::create();
        
        // perform all necessary checks (token, is admin, userId)
        // Prevent to suspend or delete own account.
        // If admin suspend or delete own account will not be able to do any action.
		if (self::validateToken($response, $token, $tokenKey) && 
            self::validateAdminPermissions($response) && 
            self::validateUserId($response, $userId) &&
            self::validateUserIdIsNotSelf($response, $userId)) {

            // define or reset suspension time
            $days = (int) $suspensionDays; 
            $suspensionTime = $days > 0 ? time() + ($days * 60 * 60 * 24) : null;
        
            // write the above info to the database
            if ($response->assertTrue(self::writeSuspensionStatus($userId, $suspensionTime), 500, 'TODO _')){
       
                // if suspension or deletion should happen, then also kick user out of the application 
                // 'instantly' by resetting the user's session 
                if ($suspensionTime != null) {

                    // see method is UserLoginModel
                    if ($response->assertTrue(self::resetSessionIdInDatabase($userId), 500, 'TODO _rsid')){
                        $response->setMessage(self::text('USER_ACCOUNT_SUCCESSFULLY_KICKED'));
                    }
                    // return response
                    return $response;      
                 }

                // set success message
                $response->setMessage(self::text('USER_ACCOUNT_SUSPENSION_DELETION_STATUS_CHANGED'));
            }
        }

        // return response
        return $response;            
    }
    
    /**
     * Update deletion status.
     * 
     * Sets the deletion and suspension values for a given user. This action expects the 
     * token given by UserAdmingModel::getUserAdminDatas() to be passed as argument. This 
     * action need ADMIN permissions. The possible response codes are: 
     * - 200 (success) 
     * - 403 (no admin) 
     * - 405 (invalid userId, own userId or invalid token) 
     * - 500 (Houston we..)
     *
     * @access public
     * @static
     * @param  mixed        $userId             The user'id
     * @param  string       $token              The token
     * @param  bool        [$deleteStatus]      True to mark user a deleted. Default is false. 
     *
     * @return TaskResponse    
     */
    public static function updateDeletionStatus($userId, string $token, string $tokenKey, bool $deleteStatus = true)
    {
        // the return response
        $response = TaskResponse::create();
        
        // perform all necessary checks (token, is admin)
        // Prevent to suspend or delete own account.
        // If admin suspend or delete own account will not be able to do any action.
		if (self::validateToken($response, $token, $tokenKey) && 
            self::validateAdminPermissions($response) && 
            self::validateUserId($response, $userId) &&
            self::validateUserIdIsNotSelf($response, $userId)) {
        
            // write the above info to the database
            if ($response->assertTrue(self::writeDeletionStatus($userId, $deleteStatus), 500, 'TODO houston')){
       
                // see method is UserLoginModel
                if ($response->assertTrue(self::resetSessionIdInDatabase($userId), 500, 'TODO rsid')){
                    $response->setMessage(self::text('USER_ACCOUNT_SUCCESSFULLY_KICKED'));
                    return $response;            
                }
                       
                // set success message
                $response->setMessage(self::text('USER_ACCOUNT_SUSPENSION_DELETION_STATUS_CHANGED'));
            }
        }

        // return response
        return $response;            
    }

	/**
	 * Deletes the user from user table and delete its settings
	 *
     * @access public
     * @static
     * @param  int          $userId             The user's id
     * @param  string       $token              The token
     *
     * @return TaskResponse 
	 */
    public static function deleteUserAndSettings($userId, $token, $tokenKey)
    {
        // the return response
        $response = TaskResponse::create();
        
        // perform all necessary checks (token, is admin)
        // Prevent to suspend or delete own account.
        // If admin suspend or delete own account will not be able to do any action.
		if (self::validateToken($response, $token, $tokenKey) && 
            self::validateAdminPermissions($response) && 
            self::validateUserId($response, $userId) &&
            self::validateUserIdIsNotSelf($response, $userId)) {

            //todo
            $deleteSettings = self::database()->delete('user_setting')->whereEqual('userId', (int) $userId)->execute();
            $deleteUser     = self::database()->delete('user')->whereEqual('userId', (int) $userId)->execute();

            if ($response->assertTrue($deleteSettings && $deleteUser, 500 , self::text('USER_ACCOUNT_ERROR_DELETETION_FAILED'))){
                $response->setMessage(self::text('USER_ACCOUNT_SUCCESSFULLY_DELETED'));                
            }
        }

        // return response
        return $response;            
    }
    
    /**
     * Validates userId is not self.
     * 
     * Checks that the given userId is not curret user id. Prevent to suspend or delete own account.
     *
     * @access protected
     * @static
	 * @param  TaskResponse     $response               The reponse instance.
     * @param  mixed            $userId                 The user's id.
     *
	 * @return bool             True if the given userId is not the current user id, otherwise False.   
     */
    protected static function validateUserIdIsNotSelf(TaskResponse $response, $userId)
    {
        // is current user id
        $isCurrentuserId = ($userId == self::getCurrentUserId());
        return $response->assertFalse($isCurrentuserId, 405, self::text('USER_ACCOUNT_ERROR_DELETE_SUSPEND_OWN'));
    }

    /**
     * Writes the suspension status for the given user into the database.
     *
     * @access protected
     * @static
     * @param  int              $userId                 The user'id
     * @param  int              $suspensionTime         The suspension timestamp.
     *
     * @return bool
     */
    protected static function writeSuspensionStatus($userId, $suspensionTime)
    {
        $query = self::database()->update('user')
                                 ->setValue('userSuspensionTimestamp', $suspensionTime)
                                 ->whereEqual('userId', (int) $userId);
        return $query->execute() && $query->rowCount() === 1;
    }

    /**
     * Writes the deletion status for the given user into the database.
     *
     * @access protected
     * @static
     * @param  int          $userId             The user'id
     * @param  bool         $deleted            Deleted or not.
     *
     * @return bool
     */
    protected static function writeDeletionStatus($userId, $deleted)
    {
        $query = self::database()->update('user')
                                 ->setValue('userDeleted', $deleted ? 1 : 0)
                                 ->setValue('userDeletionTimestamp', $deleted ? time() : null)
                                 ->whereEqual('userId', (int) $userId);
        
        return $query->execute() && $query->rowCount() === 1;
    }

    /**
	 * Writes a new account to database
     *
     * @access protected
     * @static
     * @param  string           $userName               The user's name.
     * @param  string           $userEmail              The user's email address.
	 * @param  string           $userPasswordHash       The hashed user's password.
	 *
	 * @return int|bool
	 */
	protected static function writeNewAccount($userName, $userEmail, $userPasswordHash)
	{
        $userDirectory = \Kristuff\Miniweb\Security\Token::getNewToken(16);
        $query = self::database()->insert('user')
                               ->setValue('userName', $userName)
                               ->setValue('userEmail', $userEmail)
                               ->setValue('userProvider', 'DEFAULT')
                               ->setValue('userPasswordHash', $userPasswordHash)
                               ->setValue('userActivationHash', null)
                               ->setValue('userDataDirectory', $userDirectory)
                               ->setValue('userActivated', 1)
                               ->setValue('userCreationTimestamp', time())
                               ->setValue('userAccountType', 1);
    
        return $query->execute() ? $query->lastId() : false;
    }
    
    /**
	 * Writes admin user account to database. This is used during installing process and 
     * required a Database instance as argument
     *
     * @access protected
     * @static
     * @param  string               $userName               The user's name.
     * @param  string               $userEmail              The user's email address.
	 * @param  string               $userPasswordHash       The hashed user's password.
	 * @param  Patabase\Database    $database               The database instance.
	 *
	 * @return int|bool             The admin user's id if successfully created, otherwise false 
	 */
    public static function insertAdminUser(string $userEmail, string $userName, string $userPassword, Database $database)
   {
        // crypt the password with the PHP 5.5's password_hash() function, results in a 60 character hash string.
        // @see php.net/manual/en/function.password-hash.php for more, especially for potential options
        $passwordHash = password_hash($userPassword, PASSWORD_DEFAULT);
        $userDirectory = \Kristuff\Miniweb\Security\Token::getNewToken(16);
       
        $query = $database->insert('user')
                       ->setValue('userName', $userName)
                       ->setValue('userEmail', $userEmail)
                       ->setValue('userPasswordHash', $passwordHash)
                       ->setValue('userActivated', 1)
                       ->setValue('userCreationTimestamp', time())
                       ->setValue('userDataDirectory', $userDirectory)
                       ->setValue('userAccountType', 7);

        return $query->execute() ? $query->lastId() : false;
   }
}