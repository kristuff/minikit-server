<?php

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.23 
 * Copyright (c) 2017-2023 Christophe Buliard  
 */

namespace Kristuff\Minikit\Auth\Model;

use Kristuff\Minikit\Core\Filter;
use Kristuff\Minikit\Mvc\TaskResponse;
use Kristuff\Minikit\Mvc\Application;
use Kristuff\Minikit\Auth\Data\UsersCollection;

/** 
 * UserModel
 */
class UserModel extends BaseModel
{
    const USER_STATUS_WAITING    = 0;
    const USER_STATUS_ACTIVATED  = 1;
    const USER_STATUS_DELETED    = 9;

    const USER_ROLE_GUEST       = 1;
    const USER_ROLE_STANDARD    = 2;
    const USER_ROLE_ADMIN       = 7;

    /** 
     * Get the account type as human readable string value .
     * @see isUserLoggedInAndAdmin()
     * 
     * @access public
     * @static 
     * @param int           $userAccountType        The user account type id
     * 
     * @return string
     */
    public static function getReadableAccountType(int $userAccountType)
    {
        switch ((int) $userAccountType){
            case self::USER_ROLE_GUEST: return 'guest';
            case self::USER_ROLE_STANDARD: return 'standard';
            case self::USER_ROLE_ADMIN: return 'admin';
            default: return '';
        }
    }

    /** 
     * Gets whether the auth process use HTML email
     * 
     * @access public
     * @static
     *
     * @return bool         True if the auth process use HTML email, otherwise false.
     */
    public static function isHtmlEmailEnabled()
    {
        return self::config('AUTH_EMAIL_HTML') === true; 
    }

    /** 
     * Returns the current user id.
     *
     * @access public
     * @static
     *
     * @return int          True current user id.
     */
    public static function getCurrentUserId()
    {
        return intval(self::session()->get('userId'));
    }

    /** 
     * Returns the current user uid.
     *
     * @access public
     * @static
     *
     * @return string          True current user string uid.
     */
    public static function getCurrentUserIdentifier()
    {
        return intval(self::session()->get('userIdentifer'));
    }

    /** 
     * Returns the current user account type.
     * 
     * @access public
     * @static
     *
     * @return int          The current user account type.
     */
    public static function getCurrentUserAccountType(): int
    {
        return intval(self::session()->get('userAccountType'));
    }

    /** 
     * Checks if the user is logged in or not
     *
     * @access public
     * @static
     *
     * @return bool         True if the current user is logged in, otherwise false.
     */
    public static function isUserLoggedIn()
    {
        return self::session()->get('userIsLoggedIn') ? true : false;
    }

    /** 
     * Checks if a given id corresponds to current user id
     *
     * @access public
     * @static
     *
     * @return bool         True if the id corresponds to current user id, otherwise false.
     */
    public static function isCurrentUserId(int $userId)
    {
        return $userId === self::getCurrentUserId();
    }

    /** 
     * Checks if the user is logged and has admin permissions.
     * @see getReadableAccountType() 
     * 
     * @access public
     * @static
     *
     * @return bool         True if the current user is logged in and has admin permissions, otherwise false.
     */
    public static function isUserLoggedInAndAdmin()
    {
        return self::isUserLoggedIn() && self::getCurrentUserAccountType() === self::USER_ROLE_ADMIN;
    }

    /** 
     * Checks whether the user's name is valid
     *
     * @access public
     * @static
     * @param  string       $userName               The user's name
     *
     * @return bool         True if username matchs expected pattern, otherwise false.
     */
    public static function isUserNamePatternValid($userName)
    {
        // username cannot be empty and must be azAZ09 and 2-64 characters
         return preg_match("/^[a-zA-Z0-9]{2,64}$/", $userName) === 1;
    }

    /** 
     * Checks whether the user's nice name is valid
     * 
     * Username cannot be empty and must be azAZ09 and 2-64 characters
     *
     * @access public
     * @static
     * @param  string       $userName               The user's name
     *
     * @return bool         True if username matchs expected pattern, otherwise false.
     */
    public static function isUserNiceNamePatternValid($userName)
    {
         return preg_match("/^\w+( [\w\-_ ]+)$/", $userName) === 1 
            && strlen($userName) <= 64  
            && strlen($userName) >= 2;
    }

    /**
     * Validates self is admin
     * 
     * Checks if current user is logged in and is admin, if not, sets the response code to 403 with common error message.
     *
     * @access public
     * @static
	 * @param TaskResponse  $response               The reponse instance.
     *
	 * @return bool         True if the given username is valid, otherwise false.   
     */
    public static function validateAdminPermissions(TaskResponse $response)
    {
        return $response->assertTrue(self::isUserLoggedInAndAdmin(), 403, 
                                     self::text('ERROR_INVALID_PERMISSIONS')); 
    }

    /**
	 * Validate a password 
	 *
     * @access public
     * @static
     * @param TaskResponse  $response               The response instance.
     * @param string        $newPassword            The new password.
     * @param string        $repeatNewPassword      The repeated password.
     *
     * @return bool             True if the given password is valid, otherwise false.   
     */
	public static function validateUserPassword(TaskResponse $response, string $newPassword, string $repeatNewPassword)
	{
        // TODO force strong password?
        return $response->assertFalse(empty($newPassword), 400, self::text('USER_PASSWORD_ERROR_EMPTY')) &&
               $response->assertEquals($newPassword, $repeatNewPassword, 400, self::text('USER_PASSWORD_ERROR_REPEAT_WRONG')) &&
               $response->assertFalse(strlen($newPassword) < 8, 400, self::text('USER_PASSWORD_ERROR_TOO_SHORT'));
    }
    
    /**
     * Validates the username pattern
     *
     * @access public
     * @static
     * @param  TaskResponse     $response               The TaskResponse instance.
     * @param  string           $userName               The user's name.
     *
     * @return bool             True if the given username is valid, otherwise false.   
     */
    public static function validateUserNamePattern(TaskResponse $response, string $userName)
    {
        // username cannot be empty and must be azAZ09 and 2-64 characters
        return $response->assertTrue(self::isUserNamePatternValid($userName), 400, self::text('USER_NAME_ERROR_BAD_PATTERN'));
    }

     /**
     * Validates the username pattern
     *
     * @access public
     * @static
     * @param  TaskResponse     $response               The TaskResponse instance.
     * @param  string           $userName               The user's name.
     *
     * @return bool             True if the given username is valid, otherwise false.   
     */
    public static function validateUserNiceNamePattern(TaskResponse $response, string $userName)
    {
        // username cannot be empty and must be azAZ09 and 2-64 characters
        return $response->assertTrue(self::isUserNamePatternValid($userName), 400, self::text('USER_NAME_ERROR_BAD_PATTERN'));
    }

    /**
     * Validates the email pattern
     *
     * @access public
     * @static
     * @param TaskResponse      $response               The reponse instance.
     * @param string            $userEmail              The user's email address.
     * @param string            $userEmailRepeat        The repeated user's email address.
     *
	 * @return bool             True if the given username is valid, otherwise false. 
     */
    public static function validateUserEmailPattern(TaskResponse $response, $userEmail, $userEmailRepeat)
    {
        // validate the email with PHP's internal filter
        // side-fact: Max length seems to be 254 chars
        // @see http://stackoverflow.com/questions/386294/what-is-the-maximum-length-of-a-valid-email-address
        return $response->assertFalse(empty($userEmail), 400, self::text('USER_EMAIL_ERROR_EMPTY')) &&
               $response->assertEquals($userEmail, $userEmailRepeat, 400, self::text('USER_EMAIL_ERROR_REPEAT_WRONG')) &&
               $response->assertTrue((filter_var($userEmail, FILTER_VALIDATE_EMAIL) !== false), 400, self::text('USER_EMAIL_ERROR_BAD_PATTERN'));
    }

    /**
     * Validates a userId
     * Checks if the user id is set and is int
     *
     * @access protected
     * @static
     * @param TaskResponse      $response               The reponse instance.
     * @param mixed             $userId                 The user's id.
     *
     * @return bool             True if the given username is valid, otherwise false.   
     */
    protected static function validateUserId(TaskResponse $response, $userId)
    {
        return $response->assertTrue(!empty($userId), 405, self::text('USER_ID_ERROR_EMPTY')) &&
               $response->assertTrue(is_numeric($userId) || ctype_digit($userId), 405, self::text('USER_ID_ERROR_BAD_FORMAT'));
    }

    /**
     * Validates token
     * 
     * Checks if a token is valid according to its key. 
     *
     * @access protected
     * @static
     * @param TaskResponse      $response               The reponse instance.
     * @param string            $token                  The token.
     * @param string            $tokenKey               The token key.
     *
	 * @return bool             True if the given username is valid, otherwise false.   
     */
    protected static function validateToken(TaskResponse $response, ?string $token, string $tokenKey)
    {
        return $response->assertTrue(self::token()->isTokenValid($token, $tokenKey), 405, self::text('ERROR_INVALID_TOKEN'));
    }

    /**
     * Validates that the username is not already taken
     * 
     * @access public
     * @static
	 * @param TaskResponse      $response               The TaskResponse instance.
     * @param string            $userName               The user's name.
     * @param int               $userId                 The user's id, if already created.
     *
	 * @return bool             True if the given username is valid, otherwise false.   
     */
    public static function validateUserNameNoConflict(TaskResponse $response, string $userName, ?int $userId = null): bool
    {
        return $response->assertFalse(UsersCollection::isUserNameExists($userName, $userId), 409, self::text('USER_NAME_ERROR_ALREADY_TAKEN'));
    }

    /**
     * Validates that the user nice name is not already taken
     * 
     * @access public
     * @static
	 * @param TaskResponse      $response               The TaskResponse instance.
     * @param string            $userName               The user's nice name.
     * @param int               $userId                 The user's id, if already created.
     *
	 * @return bool             True if the given username is valid, otherwise false.   
     */
    public static function validateUserNiceNameNoConflict(TaskResponse $response, string $userName, ?int $userId = null): bool
    {
        return $response->assertFalse(UsersCollection::isUserNiceNameExists($userName, $userId), 409, self::text('USER_NAME_ERROR_ALREADY_TAKEN'));
    }

    /**
     * Validates that email is not already token
     *
     * @access public
     * @static
     * @param TaskResponse      $response               The TaskResponse instance.
     * @param string            $userEmail              The user's email address.
     * @param int               $userId                 The user's id, if already created.
     * 
     * @return bool             True if the given username is valid, otherwise false. 
     */
    public static function validateUserEmailNoConflict(TaskResponse $response, string $userEmail, ?int $userId = null)
    {
        return $response->assertFalse(UsersCollection::isUserEmailExists($userEmail, $userId), 409, self::text('USER_EMAIL_ERROR_ALREADY_TAKEN'));
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
    public static function getProfiles(int $limit = 0,int $offset = 0, string $orderBy = 'userName')
    {
        $response = TaskResponse::create();
        
        // need admin auth
        if (self::validateAdminPermissions($response)){
            
           $users = UsersCollection::getProfiles($limit, $offset, $orderBy);
       
            // set/fix additional infos avatar url and ...
            foreach($users as $user) {
            
            // all elements of array passed to Filter::XSSFilter for XSS sanitation.
            // Removes (possibly bad) JavaScript etc from the user's values
            Filter::XssFilter($users);

                $user->userAvatarUrl =  UserAvatarModel::getAvatarUrl(
                    $user->userHasAvatar, 
                    $user->userAvatarId,
                    Application::getUrl()
                );
                $user->userType = self::getReadableAccountType($user->userAccountType);
            }

            // set data to return
            $response->setdata(['total'  => UsersCollection::countProfiles(),
                                'offset' => $offset,
                                'limit'  => $limit,
                                'items'  => $users]);
        }

        // return response
        return $response;
    }    
}