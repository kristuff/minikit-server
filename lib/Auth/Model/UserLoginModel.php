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

use Kristuff\Minikit\Core\Format;
use Kristuff\Minikit\Security\Encryption;
use Kristuff\Minikit\Auth\Model\UserModel;
use Kristuff\Minikit\Auth\Data\UserMetaCollection;
use Kristuff\Minikit\Auth\Model\UserAvatarModel;
use Kristuff\Minikit\Core\Syslog;
use Kristuff\Minikit\Auth\Data\UsersCollection;
use Kristuff\Minikit\Http\Request;
use Kristuff\Minikit\Mvc\TaskResponse;

/**
 * UserLoginModel
 *
 * The login part of the user model: Handles the login / logout stuff
 */
class UserLoginModel extends UserModel
{
    /** 
     * Checks for session concurrency and for suspended/deleted user
     *
     * - Session concurrency is done as the following:
     * UserA logs in with his session id('123') and it will be stored in the database.
     * Then, UserB logs in also using the same email and password of UserA from another PC,
     * and also store the session id('456') in the database
     * Now, Whenever UserA performs any action,
     * You then check the session_id() against the last one stored in the database('456'),
     * If they don't match then log both of them out.
     * 
     * - Check also, if user has not been suspended or deleted:
     * if there is not userSessionId anymore stored in database
     * 
     * @access public
     * @static
     *
     * @return bool     True if a concurrent session exists, otherwise false.
     */
    public static function isSessionValid()
    {
        // compare data in current session with data in database
        $currentSessionId = session_id();
        $currentUserId    = self::getCurrentUserId();

        if (isset($currentUserId) && isset($currentSessionId)) {
            $storedSessionId = UsersCollection::getCurrentSessionid($currentUserId);

            // if user has been suspended or deleted                                               
            if (empty($storedSessionId)) {
                return false;
            }

            // compare session id
            return  $currentSessionId == $storedSessionId;
        }
        return false;
    }

    /**
     * Logout 
     * 
     * Deletes session cookie, resets sessionId in database and destroys the session
     * 
     * @access public
     * @static
     *
     * @return void
     */
    public static function logout()
    {
        $userId = self::getCurrentUserId();
        self::deleteCookie($userId);
        UsersCollection::resetSessionId($userId);
        self::session()->destroy();
    }

    /**
     * Gets the necessary data for login view
     * 
     * @access public
     * @static
     *
     * @return array
     */
    public static function getPreLoginData()
    {
        return [
            'redirect'      => self::request()->get('redirect'),
            'token'         => self::token()->value('login'),
            'allowCookie'   => self::config('AUTH_LOGIN_COOKIE_ENABLED'),
            'allowRecovery' => UserRecoveryModel::isRecoveryEnabled(), 
        ];
    }

    /**
     * Gets data to be passed to all view for logged in user.
     * 
     * @access public
     * @static
     *
     * @return array
     */
    public static function getPostLoginData()
    {
        $createdTimeStamp = self::session()->get('userCreationTimestamp');
        $createdSince =  Format::getHumanTime(time() - $createdTimeStamp, 'day'); 

        return [
            'userId'                => self::getCurrentUserId(),
            'userName'              => self::session()->get('userName'),

            'userEmail'             => self::session()->get('userEmail'),
            'userAccountType'       => self::session()->get('userAccountType'),
            'userIdentifier'        => self::session()->get('userIdentifier'),
            'userProvider'          => 'DEFAULT', // TODO
            'userCreationTimestamp' => $createdTimeStamp,
            'userIsAdmin'           => self::isUserLoggedInAndAdmin(),

            'avatarMaxSize'         => self::config('USER_AVATAR_UPLOAD_MAX_SIZE'),
            'userAvatarUrl'         => self::session()->get('userAvatarUrl'),
            'userHasAvatar'         => self::session()->get('userHasAvatar'),

            'userMemberSince'       => $createdSince,
            'userType'              => self::getReadableAccountType((int) self::session()->get('userAccountType')),

            'apiToken'              => self::token()->value('api'),
            'userEditToken'         => self::token()->value('user_edit'),

        ];
    } 

    /**
     * Login process (for DEFAULT user accounts)
     *
     * @access public
     * @static
     * @param  string       $userName           The user's name
     * @param  string       $userPassword       The user's password
     * @param  bool         $rememberMe         True to use the remember-me cookie feature. Default is false.
     *
     * @return TaskResponse    A reponse array with errors detail.
     */
    public static function login($userNameOrEmail, $userPassword, $rememberMe, $token)
    {
        $response = TaskResponse::create();
        
        // validate token and for simplicity empty username and empty password in one line
        if (self::validateToken($response, $token, 'login') &&
            $response->assertTrue(!empty($userNameOrEmail) && 
            !empty($userPassword), 400, self::text('LOGIN_ERROR_NAME_OR_PASSWORD_EMPTY'))){
	    
            // checks if user exists, if login is not blocked (due to failed logins) and if password fits the hash
	        $user = self::validateAndGetUser($response, $userNameOrEmail, $userPassword);

            // check if that user exists 
            if ($user){

                // stop the user's login if account has been soft deleted
                if ($response->assertFalse($user->userDeleted == 1, 400, self::text('LOGIN_ERROR_ACCOUNT_DELETED'))){

                    // stop the user from logging in if user has a suspension.
                    $userHasSuspension = isset($user->userSuspensionTimestamp) && $user->userSuspensionTimestamp - time() > 0;
                    
                    $userHasSuspensionMessage = $userHasSuspension ? sprintf(self::text('LOGIN_ERROR_ACCOUNT_SUSPENDED'), 
                        Format::getHumanTime($user->userSuspensionTimestamp - time(), 'hour')) : ''; 
                    //
                    //  round(abs($user->userSuspensionTimestamp - time())/60/60, 2));
                    if ($response->assertFalse($userHasSuspension, 400, $userHasSuspensionMessage)){
        
                        // -----------------------------------------
                        // successful login validation, now persist 
                        // some data into session and database
                        // -----------------------------------------

                        // if user has checked the "remember me" checkbox, then write token into cookie and get the token
                        // to write it later into database 
                        $rememberMeToken = $rememberMe ? self::createRememberMeCookie($user->userId) : null;
       
                        // write all necessary data into the session
                        self::saveSuccessfulLoginInSession($user);

                        // persist login in database (login timestamp, reset failded login counter if needed, save coookie 
                        // token if defined
                        UsersCollection::updateSuccessfulLogin(
                            (int) $user->userId, 
                            session_id(), 
                            $rememberMeToken
                        );
                    }
                }
            }
        }
        // return the positive response to make clear the login was successful
        return $response;
    }

    /**
     * Login process (via cookie (or DEFAULT user accounts)
     *
     * @access public
     * @static
     * @param string    $cookie     The "remember_me" cookie 
     *
     * @return TaskResponse    
     */
    public static function loginWithCookie($cookie)
    {
        $response = TaskResponse::create(400); // error response by defaut

        // make sure cookie is set and
        if ( !empty($cookie) ){

            // decrypt cookiestring
            $cookieString =  Encryption::decrypt($cookie, self::config('ENCRYPTION_KEY'), self::config('HMAC_SALT'));

            // before list(), check it can be split into 3 strings.
            if ( count (explode(':', $cookieString)) === 3 ) {

                list($userId, $token, $hash) = explode(':', $cookieString);
                
                // check cookie's contents, check if cookie contents belong together or token is empty
                if (!empty($token) && !empty($userId) && $hash === hash('sha256', $userId . ':' . $token) ) {

                    // get data of user that has this id and this token
                    $result = UsersCollection::getUserByUserIdAndToken(intval($userId), $token);

                    // if user with that id and exactly that cookie token exists in database
                    if ( $result !== false ) {
                        
                        // successfully logged in, so we write all necessary data into the session and set "userIsLoggedIn" to true
                        self::saveSuccessfulLoginInSession($result);
                        
                        // persist successful login in database (login timestamp)
                        // NOTE: we don't set another remember_me-cookie here as the current cookie should always
                        // be invalid after a certain amount of time, so the user has to login with username/password
                        // again from time to time. This is good and safe ! ;)
                        // This is done by setting the $remerberMeToken to False in saveSuccessfulLoginInDatabase(). 
                        UsersCollection::updateSuccessfulLogin((int) $result->userId, session_id(), false);
                        $response->setCode(200);
                        $response->setMessage(self::text('LOGIN_COOKIE_SUCCESSFUL'));
                    }
                }
            }
        }
        return $response;
    }

	/**
	 * Validates the inputs of the users, checks if password is correct etc.
	 * If successful, user is returned
	 *
     * @access private
     * @static
     * @param  string   $userName
     * @param  string   $userPassword
     *
     * @return bool|mixed
     */
	private static function validateAndGetUser(TaskResponse $response, $userNameOrEmail, $userPassword)
	{
        // null password are invalids
        if ($response->assertTrue((!empty($userPassword) && trim($userPassword) != ''), 400, self::text('LOGIN_ERROR_NAME_OR_PASSWORD_EMPTY'))){

            // brute force attack mitigation: use session failed login count and last failed login for not 
            // found users. Block login attempt if somebody has already failed 3 times and the last login 
            // attempt is less than 30sec ago (limits user searches in database)
            $failureOne = (self::session()->get('userFailedLoginCount') >= 3) && 
                          (self::session()->get('userLastFailedLogin') > (time() - 30));

            if ($response->assertFalse($failureOne, 400, self::text('LOGIN_ERROR_FAILED_3_TIMES'))){
		
		        // get all data of that user (to later check if password and password_hash fit)
		        $user = UsersCollection::getUserByUserNameOrEmail($userNameOrEmail);

                // check if that user exists. We don't give back a cause in the feedback to avoid giving an 
                // attacker details. Brute force attack mitigation: reset failed login counter because of found user
                if (!$response->assertTrue($user !== false, 400, self::text('LOGIN_ERROR_NAME_OR_PASSWORD_WRONG'))){

                    // increment the user not found count, helps mitigate user enumeration
                    self::incrementUserNotFoundCounter();
                    Syslog::warning('Invalid authentification from host [' . Request::remoteIp() . ']');
 
                    // user does not exist, but we won't to give a potential attacker this details
                    return false;
                };
      
		        // block login attempt if somebody has already failed 3 times and the last login attempt is 
                // less than 30sec ago
                $failureTwo = ($user->userFailedLoginCount >= 3) && 
                              ($user->userLastFailedLoginTimestamp > (time() - 30));
        
                if ($response->assertFalse($failureTwo, 400, self::text('LOGIN_ERROR_FAILED_3_TIMES'))){
		
		            // if hash of provided password does NOT match the hash in the database
		            if (!$response->assertTrue(password_verify($userPassword, $user->userPasswordHash), 400, self::text('LOGIN_ERROR_NAME_OR_PASSWORD_WRONG'))){
		    
                        //  +1 failed-login counter
                        UsersCollection::incrementFailedLoginCounter($user->userName);
                        Syslog::warning('Invalid authentification from host [' . Request::remoteIp() . ']');
                        return false;   
		            }

		            // if user is active (= has verified account by verification mail)
		            if ($response->assertTrue($user->userStatus == UserModel::USER_STATUS_ACTIVATED, 400, self::text('LOGIN_ERROR_ACCOUNT_NOT_ACTIVATED'))){

                        // reset the user not found counter
                        self::resetUserNotFoundCounter();

                        // return the user
                        return $user;
                    }
                }
            }
        }

        //something was wrong
        return false;
	}

    /**
     * Reset the userFailedLoginCount to 0.
     * Reset the userLastFailedLogin to an empty string.
     *
     * @access private
     * @static
	 * @return void
     */
    private static function resetUserNotFoundCounter()
    {
        self::session()->set('userFailedLoginCount', 0);
        self::session()->set('userLastFailedLogin', '');
    }

    /**
     * Increment the userFailedLoginCount by 1.
     * Add timestamp to userLastFailedLogin.
     *
     * @access private
     * @static
	 * @return void
     */
    private static function incrementUserNotFoundCounter()
    {
        // Username enumeration prevention: set session failed login count 
        // and last failed login for users not found
        self::session()->set('userFailedLoginCount', self::session()->get('userFailedLoginCount') + 1);
        self::session()->set('userLastFailedLogin', time());
    }

   
    /**
     * The real login process: The user's data is written into the session.
     * Cheesy name, maybe rename. Also maybe refactoring this, using an array.
     *
     * @access private
     * @static
     * @param mixed   $user
     *
     * @return void
     */
    private static function saveSuccessfulLoginInSession($user)
    {
        // remove old and regenerate session ID.
        // It's important to regenerate session on sensitive actions,
        // and to avoid fixated session.
        // e.g. when a user logs in
        self::session()->reset();
     
        // filter user data: all elements of array passed to Filter::XSSFilter for XSS sanitation.
        // Removes (possibly bad) JavaScript etc from the user's values
        \Kristuff\Minikit\Core\Filter::XssFilter($user);
        
        self::session()->set('userId', $user->userId);
        self::session()->set('userName', $user->userName);
        self::session()->set('userEmail', $user->userEmail);
        self::session()->set('userAccountType', $user->userAccountType);
        self::session()->set('userIdentifier', $user->userIdentifier);
        self::session()->set('userProvider', 'DEFAULT'); // TODO
        self::session()->set('userCreationTimestamp', $user->userCreationTimestamp);
        
        // set avatar url      
        UserAvatarModel::setAvatarInSession($user->userAvatarId, ($user->userHasAvatar == 1));
                
        // get and set user settings data into session
        $settingsData = UserMetaModel::getUserMeta(intval($user->userId), true);
        self::session()->set('userSettings', $settingsData);

        // set session cookie setting manually,
        // Why? because you need to explicitly set session expiry, path, domain, secure, and HTTP.
        // @see https://www.owasp.org/index.php/PHP_Security_Cheat_Sheet#Cookies
        self::cookie()->set(session_name(), session_id());

        // finally, set user as logged-in
        self::session()->set('userIsLoggedIn', true);
    }

    /**
     * Write remember-me token into cookie and return the token
     *
     * @access private
     * @static
     * @param int       $userId
     *
     * @return string   The cookie token
     */
    private static function createRememberMeCookie($userId)
    {
        
        // generate 64 char random string
        $cookieToken = self::token()->getNewToken(64); //hash('sha256', mt_rand());

        // generate cookie string that consists of user id, token and cominaison of both 
        // and encrypt it to never expose the original user id.
        $cookie = $userId .':'. $cookieToken ;
        $cookie .= ':'. hash('sha256', $userId .':'. $cookieToken);
        $cookieString  = Encryption::encrypt($cookie , 
                                    self::config('ENCRYPTION_KEY'), 
                                    self::config('HMAC_SALT'));
 		
        
        // generate 64 char random string
        //$cookieToken = hash('sha256', mt_rand());

        // generate cookie string that consists of user id, random string and combined hash of both
        // never expose the original user id, instead, encrypt it.
        //$cookieString  = Encryption::encrypt($userId, self::config('ENCRYPTION_KEY'), self::config('HMAC_SALT')) . ':' . $cookieToken;
        //$cookieString .= ':' . hash('sha256', $userId . ':' . $cookieToken);
		
		// set cookie, and make it available only for the domain created on (to avoid XSS attacks, where the
        // attacker could steal your remember-me cookie string and would login itself).
        // If you are using HTTPS, then you should set the "secure" flag (the second one from right) to true, too.
        // See Cookie section in application config
        self::cookie()->set('remember_me', $cookieString);

        // return token
        return $cookieToken;
    }

    /**
     * Deletes the cookie
     *
     * It's necessary to split deleteCookie() and logout() as cookies are deleted without logging out too!
     * Sets the remember-me-cookie to ten years ago (3600sec * 24 hours * 365 days * 10).
     * that's obviously the best practice to kill a cookie @see http://stackoverflow.com/a/686166/1114320
     *
     * @access private
     * @static
     * @param  int      $userId
     *
     * @return void
     */
    private static function deleteCookie($userId = null)
    {
        // clear rememberMeToken in database
        if(isset($userId)){
            UsersCollection::deleteCookie($userId);
        }

        // delete remember_me cookie in browser
        self::cookie()->delete('remember_me');
    }
}