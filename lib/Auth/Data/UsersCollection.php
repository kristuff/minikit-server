<?php

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.22 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */

namespace Kristuff\Minikit\Auth\Data;

use Kristuff\Minikit\Auth\Model\UserModel;
use Kristuff\Minikit\Data\Model\DatabaseModel;
use Kristuff\Minikit\Security\Token;
use Kristuff\Patabase\Database;
use Kristuff\Patabase\Output;
use Kristuff\Patabase\Query\Select;

/** 
 * UsersCollection
 * 
 * Handle users collection in database 
 */
class UsersCollection extends DatabaseModel
{
    /** 
     * Gets the number of users in database
     * 
     * @access public
     * @static
     * 
     * @return int    
     */
    public static function countProfiles(): int
    {
        return self::count('minikit_users');
    }
    
    /** 
     * Get the session id associated to current user
     *
     * @access public
     * @static
     * @param id            $userId             The user's id
     *
     * @return mixed 
     */
    public static function getCurrentSessionid(int $userId)
    {
        return self::database()->select('userSessionId')
                                ->from('minikit_users')
                                ->whereEqual('userId', $userId)
                                ->getColumn();
    }

    /** 
     * Checks if a username is already taken
     *
     * @access public
     * @static
     * @param string        $userName           The user's name
     *
     * @return bool         True if username alreay exists in database, otherwise False.
     */
    public static function isUserNameExists(string $userName, ?int $userId = null): bool
    {
         $query = self::database()->select()
                                ->count('num')
                                ->from('minikit_users')
                                ->whereEqual('userName', $userName);
        
        // exclude self
        if (!empty($userId)){
            $query->where()->notEqual('userId', $userId);
        }

        return $query->getColumn() > 0;
    }

    /** 
     * Checks if a user nice name is already taken
     *
     * @access public
     * @static
     * @param string        $userName           The user's name
     *
     * @return bool         True if username alreay exists in database, otherwise False.
     */
    public static function isUserNiceNameExists(string $userName, ?int $userId = null): bool
    {
        $query = self::database()->select()
                                ->count('num')
                                ->from('minikit_users')
                                ->whereEqual('userNiceName', $userName);

        // exclude self
        if (!empty($userId)){
            $query->where()->notEqual('userId', $userId);
        }

        return $query->getColumn() > 0;
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
    public static function isUserEmailExists(string $userEmail, ?int $userId = null): bool
    {
        $query = self::database()->select()
                               ->count('num')
                               ->from('minikit_users')
                               ->whereEqual('userEmail', $userEmail);

        // exclude self
        if (!empty($userId)){
            $query->where()->notEqual('userId', $userId);
        }

        return $query->getColumn() > 0;
    }

    /**
     * Checks the id/verification code combination in the database
     * and, if found, sets the user's activation status to true in the database
     *
     * @access public
     * @static
     * @param mixed     $userId                 The user's id
     * @param string    $userActivationHash     The user's mail verification hash string
     *
     * @return bool
     */
    public static function validateRegistrationHash($userId, ?string $userActivationHash = null): bool
    {
        $query = self::database()->update('minikit_users')
                                 ->setValue('userStatus', UserModel::USER_STATUS_ACTIVATED)
                                 ->setValue('userActivationHash', null)
                                 ->whereEqual('userId', $userId)
                                 ->whereEqual('userActivationHash', $userActivationHash)
                                 ->where()->notNull('userPasswordHash');
        
        return ($query->execute() && $query->rowCount() === 1);
    }

    /**
     * Set password reset token in database (for DEFAULT user accounts)
     *
     * @access public
     * @static
     * @param int       $userId                 The user id
     * @param string    $passwordResetHash  The password reset hash
     * @param int       $tempTimestamp          Temporary timestamp
     *
     * @return bool     
     */
    public static function updatePasswordResetToken($userId, $passwordResetHash, $tempTimestamp): bool
    {
       $query = self::database()->update('minikit_users')
                                ->setValue('userPasswordResetHash', $passwordResetHash)
                                ->setValue('userPasswordResetTimestamp', $tempTimestamp)
                                ->whereEqual('userId', $userId);

        return $query->execute() && $query->rowCount() === 1;
    }

    /** 
     * Writes new email address to database
     *
     * @access public
     * @static
     * @param int       $userId             The user's id.
     * @param string    $newEmail           The new user's email address.
     *
     * @return bool     True if the email has been successfully changed, otherwise false.
     */
    public static function updateUserEmail(int $userId, string $newEmail): bool
    {
        $query = self::database()->update('minikit_users')
                                 ->setValue('userEmail', $newEmail)
                                 ->whereEqual('userId', $userId);

        return $query->execute() && $query->rowCount() === 1;          
    }

    /** 
     * Writes new username to database
     *
     * @access public
     * @static
     * @param int       $userId             The user's id.
     * @param string    $newName            The new user's name.
     *
     * @return bool     True if the username has been successfully changed, otherwise false.
     */
    public static function updateUserName($userId, $newName): bool
    {
        $query = self::database()->update('minikit_users')
                                 ->setValue('userName', $newName)
                                 ->whereEqual('userId', $userId);

        return $query->execute() && $query->rowCount() === 1;          
    }

    /** 
     * Writes new username, nice name or email to database
     *
     * @access public
     * @static
     * @param int       $userId             The user's id.
     * @param string    $name               The new user's name.
     * @param string    $niceName           The new user's name.
     * @param string    $email              The new user's name.
     *
     * @return bool     True if the username has been successfully changed, otherwise false.
     */
    public static function updateUserNameOrEmail(int $userId, string $name, string $niceName, string $email): bool
    {
        $query = self::database()->update('minikit_users')
                                 ->setValue('userName', $name)
                                 ->setValue('userNiceName', $niceName)
                                 ->setValue('userEmail', $email)
                                 ->whereEqual('userId', $userId);

        return $query->execute() && $query->rowCount() === 1;          
    }    

   /** 
     * Writes new username to database
     *
     * @access public
     * @static
     * @param int       $userId             The user's id.
     * @param string    $newName            The new user's name.
     *
     * @return bool     True if the username has been successfully changed, otherwise false.
     */
    public static function updateUserNiceName($userId, $newName): bool
    {
        $query = self::database()->update('minikit_users')
                                 ->setValue('userNiceName', $newName)
                                 ->whereEqual('userId', $userId);

        return $query->execute() && $query->rowCount() === 1;          
    }


    /**
	 * Writes the new password hash to the database
	 *
     * @access public
     * @static
     * @param int       $userId             The user's id.
	 * @param string    $passwordHash       The hashed user's password
	 *
	 * @return bool     True if the password was succesfully saved, otherwise false.
	 */
	public static function updateUserPasswordHash(int $userId, string $passwordHash): bool
	{
        $query = self::database()->update('minikit_users')
                                 ->setValue('userPasswordHash', $passwordHash)
                                 ->whereEqual('userId', $userId);

		return $query->execute() && $query->rowCount() == 1;
    }

    /**
     * Writes the new password to the database
     *
     * @access public
     * @static
     * @param string    $userName               username
     * @param string    $passwordHash
     * @param string    $passwordResetHash
     *
     * @return bool     True if the password was succesfully saved, otherwise false.
     */
    public static function updateNewPasswordByNameAndResetToken($userName, $passwordHash, $passwordResetHash): bool
    {
       $query = self::database()->update('minikit_users')
                                ->setValue('userPasswordHash', $passwordHash)
                                ->setValue('userPasswordResetHash', null)
                                ->setValue('userPasswordResetTimestamp', null)
                                ->whereEqual('userName', $userName)
                                ->whereEqual('userPasswordResetHash', $passwordResetHash);

        return $query->execute() && $query->rowCount() === 1;
    }
    /**
     * Update the suspension status for the given user.
     *
     * @access public
     * @static
     * @param int       $userId                 The user'id
     * @param int       $suspensionTime         The suspension timestamp.
     *
     * @return bool
     */
    public static function updateSuspensionStatus($userId, $suspensionTime): bool
    {
        $query = self::database()->update('minikit_users')
                                 ->setValue('userSuspensionTimestamp', $suspensionTime)
                                 ->whereEqual('userId', (int) $userId);

        return $query->execute() && $query->rowCount() === 1;
    }

    /**
     * Update the deletion status for the given user.
     *
     * @access public
     * @static
     * @param mixed     $userId             The user'id
     * @param bool      $deleted            Deleted or not.
     *
     * @return bool
     */
    public static function updateDeletionStatus($userId, bool $deleted): bool
    {
        $query = self::database()->update('minikit_users')
                                 ->setValue('userDeleted', $deleted ? 1 : 0)
                                 ->setValue('userDeletionTimestamp', $deleted ? self::getFormattedTimestamp() : null)
                                 ->whereEqual('userId', (int) $userId);
        
        return $query->execute() && $query->rowCount() === 1;
    }

    /**
     * Update avatar in dabase
     *
     * Writes marker to database, saying user has an avatar or not.
     *
     * @access public
     * @static
     * @param mixed     $userId         The user's id
     * @param string    $uid            The avatar id
     * @param bool      $hasAvatar      True if given user has an avatar. Default is false.
     *
     * @return bool     True if user exists and database has been successfully updated, otherwise false.
     */
    public static function updateAvatarStatus($userId, ?string $uid = null, bool $hasAvatar = true): bool
    {
        $query = self::database()->update('minikit_users')
                                 ->setValue('userHasAvatar', $hasAvatar ? 1 : 0)
                                 ->setValue('userAvatarId', $uid)
                                 ->whereEqual('userId', (int) $userId);

        return $query->execute() && $query->rowCount() === 1;          
    }

    /**
	 * Complete registration by setting the password and user name in database
     *
     * @access public
     * @static
	 * @param int       $userId                 The user's id.
     * @param string    $userName               The user's name.
	 * @param string    $userPasswordHash       The hashed user's password.
	 * @param string    $userActivationHash     The user's mail verification hash string
	 *
	 * @return bool     True if the user profile has been successfully updated, otherwise False.
	 */
	public static function updateAndActivateInvitedUser($userId, string $userName, string $userPasswordHash,  string $userActivationHash): bool
	{
        $uid = \Kristuff\Minikit\Security\Token::getNewToken(16);
        $query = self::database()->update('minikit_users')
                                 ->setValue('userName', $userName)
                                 ->setValue('userPasswordHash', $userPasswordHash)
                                 ->setValue('userActivationHash', null)
                                 ->setValue('userIdentifier', $uid)
                                 ->setValue('userStatus', UserModel::USER_STATUS_ACTIVATED)
                                 ->whereEqual('userId', (int) $userId)
                                 ->whereEqual('userActivationHash', $userActivationHash);

        return $query->execute() && $query->rowCount() === 1;
	}

    /** 
     * Update successful login in database.
     *
     * Save the session_id, reset failed logins counter and set the last login timestamp.
     * The remember me token should not be updated when auto login with cookie (
     *
     * @access public
     * @static
     * @param int           $userId
     * @param string        $sessionId
     * @param mixed         $rememberMeToken     
     *
     * @return bool
     */
    public static function updateSuccessfulLogin(int $userId, string $sessionId, $rememberMeToken = null)
    {
        // update session id, 
        // resets the failed-login counter, set last login timestamp
        // update cookie token if defined
        $query= self::database()->update('minikit_users')
                                ->whereEqual('userId', $userId)
                                ->setValue('userFailedLoginCount', 0)
                                ->setValue('userLastLoginTimestamp', self::getFormattedTimestamp())
                                ->setValue('userLastFailedLoginTimestamp', null)
                                ->setValue('userSessionId', $sessionId);
        
        // in case of login with cookie, we don't touch the remember me token                         
        if ($rememberMeToken !== false){
            $query->setValue('userRememberMeToken', $rememberMeToken);
        }

        return $query->execute();
    }

    /**
     * Increments the failed-login counter of a user
     *
     * @access public
     * @static
     * @param string    $userNameOrEmail
     *
     * @return bool
     */
    public static function incrementFailedLoginCounter($userNameOrEmail): bool
    {
        return self::database()->update('minikit_users')
                               ->setValue('userLastFailedLoginTimestamp', self::getFormattedTimestamp())
                               ->increment('userFailedLoginCount', 1)
                               ->where()
                                    ->beginOr()
                                        ->equal('userName', $userNameOrEmail)
                                        ->equal('userEmail', $userNameOrEmail)
                                    ->closeOr()
                               ->execute();
    }

    /**
	 * Writes a new account to database
     *
     * @access public
     * @static
     * @param string    $userName               The user's name.
     * @param string    $userEmail              The user's email address.
	 * @param string    $userPasswordHash       The hashed user's password.
	 *
	 * @return int|bool
	 */
	public static function insertUser(string $userName, string $userEmail, string $userPasswordHash)
	{
        $uid = \Kristuff\Minikit\Security\Token::getNewToken(16);
        $query = self::database()->insert('minikit_users')
                                 ->setValue('userName', $userName)
                                 ->setValue('userNiceName', $userName)
                                 ->setValue('userEmail', $userEmail)
                                 ->setValue('userProvider', 'DEFAULT')
                                 ->setValue('userPasswordHash', $userPasswordHash)
                                 ->setValue('userActivationHash', null)
                                 ->setValue('userIdentifier', $uid)
                                 ->setValue('userStatus', UserModel::USER_STATUS_ACTIVATED)
                                 ->setValue('userCreationTimestamp', self::getFormattedTimestamp())
                                 ->setValue('userAccountType', UserModel::USER_ROLE_GUEST);
    
        return $query->execute() ? $query->lastId() : false;
    }
    
    /**
	 * Writes admin user account to database. This is used during installing process and 
     * required a Database instance as argument
     *
     * @access protected
     * @static
     * @param string    $userName              The user's name.
     * @param string    userEmail              The user's email address.
	 * @param string    userPasswordHash       The hashed user's password.
	 * @param Database  database               The database instance.
	 *
	 * @return int|bool The admin user's id if successfully created, otherwise false 
	 */
    public static function insertAdminUser(string $userEmail, string $userName, string $userPassword, Database $database)
    {
        // crypt the password with the PHP 5.5's password_hash() function, results in a 60 character hash string.
        // @see php.net/manual/en/function.password-hash.php for more, especially for potential options
        $passwordHash = password_hash($userPassword, PASSWORD_DEFAULT);
        $uid = Token::getNewToken(16);
       
        $query = $database->insert('minikit_users')
                       ->setValue('userName', $userName)
                       ->setValue('userNiceName', $userName)
                       ->setValue('userEmail', $userEmail)
                       ->setValue('userPasswordHash', $passwordHash)
                       ->setValue('userProvider', 'DEFAULT')
                       ->setValue('userStatus', UserModel::USER_STATUS_ACTIVATED)
                       ->setValue('userCreationTimestamp', self::getFormattedTimestamp(null, $database))
                       ->setValue('userIdentifier', $uid)
                       ->setValue('userAccountType', UserModel::USER_ROLE_ADMIN);

        return $query->execute() ? $query->lastId() : false;
    }

    /**
     * Writes the new user's data to the database
     *
     * @access public
     * @static
     * @param string    $userEmail              The user's email address.
     * @param string    $userName               The user's name.
     * @param string    $userPasswordHash       The hashed user's password.
     * @param string    $activationHash         The user's mail verification hash string
     *
     * @return bool
     */
    public static function insertUnregisteredUser(string $userEmail, string $userName, ?string $userPasswordHash = null, ?string $activationHash = null)
    {
        $uid = \Kristuff\Minikit\Security\Token::getNewToken(16);
        $query = self::database()->insert('minikit_users')
                        ->setValue('userName', $userName)
                        ->setValue('userNiceName', $userName)
                        ->setValue('userEmail', $userEmail)
                        ->setValue('userPasswordHash', $userPasswordHash)
                        ->setValue('userActivationHash', $activationHash)
                        ->setValue('userIdentifier', $uid)
                        ->setValue('userCreationTimestamp', self::getFormattedTimestamp())
                        ->setValue('userStatus', UserModel::USER_STATUS_WAITING)
                        ->setValue('userProvider', 'DEFAULT')
                        ->setValue('userAccountType', UserModel::USER_ROLE_GUEST);

        return $query->execute() && $query->rowCount() === 1;
    }

    /** 
     * Update session id in database
     *
     * @access public
     * @static
     * @param mixed     $userId
     *
     * @return bool
     */
    public static function resetSessionId($userId): bool
    {
        return self::database()->update('minikit_users')
                               ->setValue('userSessionId', null)
                               ->whereEqual('userId', (int) $userId)
                               ->execute();
    }

    /**
     * Deletes the cookie for given user id
     *
     * @access public
     * @static
     * @param mixed     $userId
     *
     * @return bool
     */
    public static function deleteCookie($userId = null): bool
    {
        //todo
        return self::database()->update('minikit_users')
                           ->setValue('userRememberMeToken', null)
                           ->whereEqual('userId', $userId)
                           ->execute();
    }

    /**
     * Deletes the given user
     *
     * @access public
     * @static
     * @param int       $userId
     *
     * @return bool
     */
    public static function deleteUserById(int $userId): bool
    {
        return self::database()->delete('minikit_users')
                               ->whereEqual('userId', $userId)
                               ->execute();
        //todo meta hosts
    }

    /** 
     * Gets the user's data by user's id and a token (used by login-via-cookie process)
     *
     * @access public
     * @static
     * @param int       $userId     
     * @param string    $token
     *
     * @return mixed    Returns false if user does not exist, returns object with user's data when user exists
     */
    public static function getUserByUserIdAndToken(int $userId, string $token)
    {
        //TODO
        $query = self::database()->select()
                                 ->from('minikit_users')
                                 ->whereEqual('userId', $userId)
                                 ->whereEqual('userRememberMeToken', $token)
                                 ->whereEqual('userProvider', 'DEFAULT');

        $query->where()->notNull('userRememberMeToken');
        self::setSelectUserColumns($query);

        $users = $query->getOne(Output::OBJ);

        return count($users) > 0 ? $users[0] : false;
    }

    /** 
     * Gets a user's profile data, according to the given user's name or user's email
     * Do not check user status as this stage, done later during longin process
     *
     * @access public
     * @static
     * @param string            $userNameOrEmail        The user's name or email address.
     *
     * @return mixed|bool       Returns the user as object if found, otherwise returns false.
     */
    public static function getUserByUserNameOrEmail(string $userNameOrEmail)
    {
        $query = self::database()->select()
                                 ->from('minikit_users');

        self::setSelectUserColumns($query);

        $query->where()
              ->beginOr()
                ->equal('minikit_users.userName', $userNameOrEmail)
                ->equal('minikit_users.userEmail', $userNameOrEmail)
              ->closeOr();

        $query->whereEqual('minikit_users.userProvider','DEFAULT');

        $users = $query->getOne(Output::OBJ);

        return count($users) > 0 ? $users[0] : false;
    }   

    /** 
     * Gets a user's profile data (password reset hash and timestamp), according 
     * to the given user's name and password reset hash. Do not check user status 
     * as this stage, done later during login process.
     *
     * @access public
     * @static
     * @param string            $userName           The user's name.
     * @param string            $passwordResetHash  The password reset hash.
     *
     * @return mixed|bool       Returns the user as object if found, otherwise returns false.
     */
    public static function getUserByNameAndResetPasswordHash(string $userName, string $passwordResetHash)
    {
        $users = self::database()->select()
                                 ->from('minikit_users')
                                 ->column('userPasswordResetHash')
                                 ->column('userPasswordResetTimestamp')
                                 ->whereEqual('userName', $userName)
                                 ->whereEqual('userPasswordResetHash', $passwordResetHash)
                                 ->whereEqual('userProvider','DEFAULT')
                                 ->getOne(Output::OBJ); 

        return count($users) === 1 ? $users[0] : false;
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
                               ->from('minikit_users')
                               ->whereEqual('userName', $userName)
                               ->getColumn();

        return !empty($userId) ?  $userId : false;
    }

    /**
	 * Verify invited user
     * 
     * Checks the id/verification code combination
	 *
     * @access public
     * @static
	 * @param int              $userId                     The user's id
	 * @param string           $userActivationHash         The user's mail verification hash string
	 *
	 * @return bool
	 */
	public static function checkIdAndActivationHash(int $userId, $userActivationHash): bool
	{
        $query = self::database()->select()
                                 ->count('userId')
                                 ->from('minikit_users')
                                 ->whereEqual('userId', (int) $userId)
                                 ->whereEqual('userActivationHash', $userActivationHash)
                                 ->getColumn();

        return count($query) === 1;
    }
           

    /** 
     * Gets user profiles
     *
     * @access public
     * @static
     * //TODO PARAMS
     * 
     * 
     * @return array
     */
    public static function getProfiles(int $limit = -1, int $offset = 0, string $orderBy = 'userName')
    {
        $query = self::database()->select()
                                 ->from('minikit_users');

        self::setSelectUserColumns($query); 

        switch($orderBy) {
            case 'userName': $query->orderAsc('userName');  break;
                //todo
        }

        if ( $limit  > 0 ) { 
            $query->limit($limit)
                  ->offset($offset); 
        }
        
        return $query->getAll(Output::OBJ);
    }    

    /** 
     * Create the table user
     * 
     * @access public
     * @static
     * @param Database  $database           The Database instance
     *
     * @return bool     True if the table has been created, otherwise False
     */
    public static function createTable(Database $database): bool
    {
        $timeColumn = self::getTimeColumnType($database);
        $textColumn = self::getTextColumnType($database);

        return $database->table('minikit_users')
                        ->create()
                        ->column('userId',                       'BIGINT',          'NOT NULL',   'PK',  'AI')               
                        ->column('userEmail',                    'VARCHAR(255)',    'NOT NULL',   'UNIQUE')
                        ->column('userName',                     'VARCHAR(64)',     'NOT NULL',   'UNIQUE')
                        ->column('userNiceName',                 'VARCHAR(64)',     'NOT NULL',   'UNIQUE')
                        ->column('userPasswordHash',             'VARCHAR(255)',    'NULL',       'DEFAULT', 'NULL')
                        ->column('userActivationHash',           'VARCHAR(255)',    'NULL',       'DEFAULT', 'NULL')
                        ->column('userIdentifier',               'VARCHAR(64)',     'NULL',       'UNIQUE')
                        ->column('userStatus',                   'SMALLINT',        'NOT NULL',   'DEFAULT', 0)
                        ->column('userAccountType',              'SMALLINT',        'NOT NULL',   'DEFAULT', 1)
                        ->column('userProvider',                 'VARCHAR(48)',     'NOT NULL',   'DEFAULT', 'DEFAULT')
                        ->column('userCreationTimestamp',        $timeColumn,       'NULL')
                        ->column('userSuspensionTimestamp',      $timeColumn,       'NULL')

                        // soft deletion
                        ->column('userDeleted',                  'SMALLINT',        'NOT NULL',   'DEFAULT', 0)
                        ->column('userDeletionTimestamp',        $timeColumn,       'NULL')

                        // session
                        ->column('userSessionId',                'VARCHAR(255)',    'NULL',       'DEFAULT', 'NULL')                
                        ->column('userLastLoginTimestamp',       $timeColumn,       'NULL')
                        ->column('userLastFailedLoginTimestamp', $timeColumn,       'NULL')
                        ->column('userFailedLoginCount',         'SMALLINT',        'NOT NULL',   'DEFAULT', 0)
                        
                        // pwd reset
                        ->column('userPasswordResetHash',        'VARCHAR(255)',    'NULL',       'DEFAULT', 'NULL')
                        ->column('userPasswordResetTimestamp',   $timeColumn,       'NULL')
                        
                        // avatar
                        ->column('userHasAvatar',                'SMALLINT',        'NOT NULL',   'DEFAULT', 0)
                        ->column('userAvatarId',                 'VARCHAR(64)',     'NULL',       'UNIQUE')

                        // TODO
                        ->column('userRememberMeToken',          'VARCHAR(64)',     'NULL')
                        ->execute(); 
    }

    /** 
     * 
     *
     * @access protected
     * @static
     * @param Select        $query
     * 
     * @return void
     */
    protected static function setSelectUserColumns(Select $query): void
    {
        $query->column('minikit_users.userId')
              ->column('minikit_users.userEmail')
              ->column('minikit_users.userName')
              ->column('minikit_users.userNiceName')
              ->column('minikit_users.userPasswordHash')
              ->column('minikit_users.userActivationHash')
              ->column('minikit_users.userIdentifier')
              ->column('minikit_users.userStatus')
              ->column('minikit_users.userAccountType')
              ->column('minikit_users.userProvider')
              ->column('minikit_users.userCreationTimestamp')
              ->column('minikit_users.userSuspensionTimestamp')
              ->column('minikit_users.userDeleted')
              ->column('minikit_users.userDeletionTimestamp')
              ->column('minikit_users.userSessionId')
              ->column('minikit_users.userLastLoginTimestamp')
              ->column('minikit_users.userLastFailedLoginTimestamp')
              ->column('minikit_users.userFailedLoginCount')
              ->column('minikit_users.userPasswordResetHash')
              ->column('minikit_users.userPasswordResetTimestamp')
              ->column('minikit_users.userHasAvatar')
              ->column('minikit_users.userAvatarId');
    }
}