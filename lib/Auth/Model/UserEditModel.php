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

use Kristuff\Minikit\Mvc\TaskResponse;
use Kristuff\Minikit\Auth\Model\UserModel;
use Kristuff\Minikit\Auth\Data\UsersCollection;

/** 
 * Class UserEditModel
 *
 * Handle user name, user email, and user password changes.
 * For avatar, see UserAvatarModel class.
 */
class UserEditModel extends UserModel
{
    /** 
     * Edit the user's name
     * 
     * @static
     * @access public
     * @param string    $newName        The new user's name
     * @param string    $token          The token
     *
     * @return 
     */
    public static function editCurrentUserName($newName, $token, $tokenKey)
    {
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
            $saved = UsersCollection::updateUserName($userId, $newName);

            // saved?
            if ($response->assertTrue($saved, 500, self::text('ERROR_UNKNOWN'))){

                // save new name in session and set success message
                self::session()->set('userName', $newName);

                $response->setMessage(self::text('USER_NAME_CHANGE_SUCCESSFUL'));
                $response->addData([ 
                    'newName'   => self::session()->get('userName'),
                    'newEmail'  => self::session()->get('userEmail'),
                ]);
            }
        }
        return $response;
    }

    /** 
     * Edit the user's email
     *
     * @static
     * @access public
     * @param string    $newEmail           The new user's email
     * @param string    $token              The token value
     * @param string    $tokenKey           The token key
     *
     * @return TaskResponse
     */
    public static function editCurrentUserEmail(string $newEmail, string $token, string $tokenKey): TaskResponse
    {
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
            $saved  = UsersCollection::updateUserEmail($userId, $newEmail);    

            // if successful ...
            if ($response->assertTrue($saved, 500, self::text('ERROR_UNKNOWN'))){
        
                // write new email to session and 
                // reset avatar url in case user uses gravatar     
                self::session()->set('userEmail', $newEmail);
                $userHasAvatar = self::session()->get('userHasAvatar');
                UserAvatarModel::setAvatarInSession($userId, $userHasAvatar);

                $response->setMessage(self::text('USER_EMAIL_CHANGE_SUCCESSFUL'));
                $response->addData([ 
                    'newName'   => self::session()->get('userName'),
                    'newEmail'  => self::session()->get('userEmail'),
                ]);
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
	 * @return TaskResponse   
	 */
    public static function editCurrentUserPassword(string $currentPassword, string $newPassword, 
                                    string $repeatNewPassword, string $token, string $tokenKey): TaskResponse
	{
        $response = TaskResponse::create();
       
        // validate token
        if (self::validateToken($response, $token, $tokenKey)){
            
            // get current user (pass hash not stored in session)
            $currentUserName = self::session()->get('userName');
            $currentUserId = self::getCurrentUserId();
            $user = UsersCollection::getUserByUserNameOrEmail($currentUserName);

            // user exists?
            if ($response->assertTrue($user !== false, 500, self::text('USER_PASSWORD_CHANGE_FAILED'))){
                
                // check current password and validate the new password pattern
		        if ($response->assertTrue(password_verify($currentPassword, $user->userPasswordHash), 400, self::text('USER_PASSWORD_CHANGE_ERROR_CURRENT_WRONG')) && 
                    self::validateUserPassword($response, $newPassword, $repeatNewPassword)){
                        
                    // crypt the password (with the PHP 5.5+'s password_hash() function, result is a 60 character hash string)
		            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                    $updated = UsersCollection::updateUserPasswordHash($currentUserId, $passwordHash);    

                    // write the password to database (as hashed and salted string), reset userPasswordResetHash
		            if ($response->assertTrue($updated, 500, self::text('USER_PASSWORD_CHANGE_FAILED'))) {
                        $response->setMessage(self::text('USER_PASSWORD_CHANGE_SUCCESSFUL'));
		            } 
		        } 
		    } 
        }
        return $response;
	}
}