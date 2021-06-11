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
 * @version    0.9.7
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Auth\Model;

use Kristuff\Miniweb\Mvc\TaskResponse;
use Kristuff\Miniweb\Auth\Model\BaseModel;

/** 
 * 
 */
abstract class UserBaseModel extends BaseModel
{
    /** 
     * Get the account type as human readable string value .
     * @see isUserLoggedInAndAdmin()

     * @access public
     * @static 
     * @param int           $userAccountType        The user account type id
     * 
     * @return string          .
     */
    public static function getReadableAccountType(int $userAccountType)
    {
        switch ((int) $userAccountType){
            case 1: return 'guest';
            case 2: return 'normal';
            case 7: return 'admin';
            default: return '';
        }
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
     * Returns the current user account type.
     * 
     * @access public
     * @static
     *
     * @return int          The current user account type.
     */
    public static function getCurrentUserAccountType()
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
        return self::isUserLoggedIn() && self::getCurrentUserAccountType() === 7;
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
     * Checks whether the user the given user name is valid
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
        // empty password?
        return $response->assertFalse(empty($newPassword), 400, self::text('USER_PASSWORD_ERROR_EMPTY'))
		
            // repeat password ok?
            && $response->assertEquals($newPassword, $repeatNewPassword, 400, self::text('USER_PASSWORD_ERROR_REPEAT_WRONG'))

		    // TODO force strong password?
            && $response->assertFalse(strlen($newPassword) < 8, 400, self::text('USER_PASSWORD_ERROR_TOO_SHORT'));
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
        // check email is not empty
        return $response->assertFalse(empty($userEmail), 400, self::text('USER_EMAIL_ERROR_EMPTY'))
  
            // check email repeat
            && $response->assertEquals($userEmail, $userEmailRepeat, 400, self::text('USER_EMAIL_ERROR_REPEAT_WRONG'))

            // validate the email with PHP's internal filter
            // side-fact: Max length seems to be 254 chars
            // @see http://stackoverflow.com/questions/386294/what-is-the-maximum-length-of-a-valid-email-address
            && $response->assertTrue((filter_var($userEmail, FILTER_VALIDATE_EMAIL) !== false), 400, self::text('USER_EMAIL_ERROR_BAD_PATTERN'));
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
        // checks for empty and no int value
        return $response->assertTrue(!empty($userId), 405, self::text('USER_ID_ERROR_EMPTY')) &&
               $response->assertTrue(is_numeric($userId) || 
                                     ctype_digit($userId), 405, self::text('USER_ID_ERROR_BAD_FORMAT'));
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
        return $response->assertTrue(self::token()->isTokenValid($token, $tokenKey), 405, 
                                     self::text('ERROR_INVALID_TOKEN'));
    }
}
