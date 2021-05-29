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
 * @version    0.9.4
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Auth\Model;

use Kristuff\Miniweb\Mvc\TaskResponse;
use Kristuff\Miniweb\Auth\Model\UserModel;

/** 
 * Class UserEditModel
 *
 *
 */
class UserEditModel extends UserModel
{
    /** 
     * Edit the user's name
     * 
     * @method static
     * @access public
     * @param  string   $newName        The new user's name
     * @param  string   $token          The token
     *
     * @return 
     */
    public static function editCurrentUserName($newName, $token, $tokenKey)
    {
        // the return response
        $response = TaskResponse::create();
 
        // validate token
        if (self::validateToken($response, $token, $tokenKey)
                   
            // check that new username is not the same as old one
            && $response->assertFalse($newName == self::session()->get('userName'), 400, self::text('USER_NAME_ERROR_NEW_SAME_AS_OLD_ONE'))

            // check pattern and conflicts
            && self::validateUserNamePattern($response, $newName)
            && self::validateUserNameNoConflict($response, $newName)){
        
            // save in database
            $userId = self::getCurrentUserId();
            $saved = self::writeUserName($userId, $newName);

            // saved?
            if ($response->assertTrue($saved, 500, self::text('ERROR_UNKNOWN'))){

                // save new name in session
                self::session()->set('userName', $newName);
            
                // set success message
                $response->setMessage(self::text('USER_NAME_CHANGE_SUCCESSFUL'));
            }
        }

        return $response;
    }

    /** 
     * Edit the user's email
     *
     * @method static
     * @access public
     * @param string        $newEmail           The new user's email
     * @param string        $token              The token value
     * @param string        $tokenKey           The token key
     *
     * @return 
     */
    public static function editCurrentUserEmail(string $newEmail, string $token, string $tokenKey)
    {
        // the return response
        $response = TaskResponse::create();

        // validate token
        if (self::validateToken($response, $token, $tokenKey) 
                   
            // check is new email is same as old one
            && $response->assertFalse($newEmail == self::session()->get('userEmail'), 400, self::text('USER_EMAIL_ERROR_NEW_SAME_AS_OLD_ONE'))

            // check pattern and conflicts
            && self::validateUserEmailPattern($response, $newEmail, $newEmail)
            && self::validateUserEmailNoConflict($response, $newEmail) ){
           
            // strip tags, just to be sure
            $newEmail = substr(strip_tags($newEmail), 0, 254);

            // write to database
            $userId = self::getCurrentUserId();
            $saved = self::writeUserEmail($userId, $newEmail);    

            // if successful ...
            if ($response->assertTrue($saved, 500, self::text('ERROR_UNKNOWN'))){
        
                // write new email to session and 
                // reset avatar url in case user uses gravatar     
                self::session()->set('userEmail', $newEmail);
                $userHasAvatar = self::session()->get('userHasAvatar');
                UserAvatarModel::setAvatarInSession($userId, $userHasAvatar);

                $response->setMessage(self::text('USER_EMAIL_CHANGE_SUCCESSFUL'));
            }
        }

        // return response
        return $response;
    }

    /**
     * Edit the user's password
	 *
	 * @param string        $currentPassword
	 * @param string        $newPassword
	 * @param string        $repeatNewPassword
     * @param string        $token                      The token value
     * @param string        $tokenKey                   The token key
	 *
	 * @return bool     
	 */
    public static function editCurrentUserPassword(string $currentPassword, string $newPassword, 
                                                   string $repeatNewPassword, string $token, string $tokenKey)
	{
        // the return response
        $response = TaskResponse::create();
       
        // validate token
        if (self::validateToken($response, $token, $tokenKey)){
            
            // get current user (pass hash not stored in session)
            $currentUserName = self::session()->get('userName');
            $currentUserId = self::getCurrentUserId();
            $user = self::getUserByUserNameOrEmail($currentUserName);

            // user exists?
            if ($response->assertTrue($user !== false, 500, self::text('USER_PASSWORD_CHANGE_FAILED'))){
                
                // check current password and validate the new password pattern
		        if ($response->assertTrue(password_verify($currentPassword, $user->userPasswordHash), 400, self::text('USER_PASSWORD_CHANGE_ERROR_CURRENT_WRONG')) && 
                    self::validateUserPassword($response, $newPassword, $repeatNewPassword)){
                        
                    // crypt the password (with the PHP 5.5+'s password_hash() function, result is a 60 character hash string)
		            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

		            // write the password to database (as hashed and salted string), reset userPasswordResetHash
		            if ($response->assertTrue(self::writeUserPasswordHash($currentUserId, $passwordHash), 500, self::text('USER_PASSWORD_CHANGE_FAILED'))) {
			            
                        // set message
                        $response->setMessage(self::text('USER_PASSWORD_CHANGE_SUCCESSFUL'));
		            } 
		        } 
		    } 
        }

        // return response
        return $response;
	}

    /** 
     * Writes new username to database
     *
     * @access private
     * @method static
     * @param  int          $userId             The user's id.
     * @param  string       $newName            The new user's name.
     *
     * @return bool         True if the username has been successfully changed, otherwise false.
     */
    private static function writeUserName($userId, $newName)
    {
        $query = self::database()->update('user')
                                 ->setValue('userName', $newName)
                                 ->whereEqual('userId', $userId);

        return $query->execute() && $query->rowCount() === 1;          
    }

    /** 
     * Writes new email address to database
     *
     * @access private
     * @method static
     * @param  int          $userId             The user's id.
     * @param  string       $newEmail           The new user's email address.
     *
     * @return bool         True if the email has been successfully changed, otherwise false.
     */
    private static function writeUserEmail(int $userId, string $newEmail)
    {
        $query = self::database()->update('user')
                                 ->setValue('userEmail', $newEmail)
                                 ->whereEqual('userId', $userId);

        return $query->execute() && $query->rowCount() === 1;          
    }
    
	/**
	 * Writes the new password hash to the database
	 *
     * @access private
     * @method static
     * @param  int          $userId             The user's id.
	 * @param  string       $passwordHash       The hashed user's password
	 *
	 * @return bool         True if the password was succesfully saved, otherwise false.
	 */
	private static function writeUserPasswordHash(int $userId, string $passwordHash)
	{
        $query = self::database()->update('user')
                                 ->setValue('userPasswordHash', $passwordHash)
                                 ->whereEqual('userId', $userId);

		// check if exactly one row was successfully changed
		return $query->execute() && $query->rowCount() == 1;
	}
}