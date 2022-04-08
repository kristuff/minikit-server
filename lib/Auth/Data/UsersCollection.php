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

namespace Kristuff\Minikit\Auth\Data;

use Kristuff\Minikit\Data\Model\DatabaseModel;
use Kristuff\Patabase\Database;

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
        return self::count('user');
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
    public static function isUserNameExists(string $userName): bool
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
    public static function isUserEmailExists(string $userEmail): bool
    {
        return self::database()->select()
                               ->count('num')
                               ->from('user')
                               ->whereEqual('userEmail', $userEmail)
                               ->limit(1)
                               ->getColumn() > 0;
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
        $query = self::database()->update('user')
                                 ->setValue('userActivated', 1)
                                 ->setValue('userActivationHash', null)
                                 ->whereEqual('userId', $userId)
                                 ->where()->notNull('userPasswordHash')
                                 ->whereEqual('userActivationHash', $userActivationHash);
        
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
    public static function savePasswordResetToken($userId, $passwordResetHash, $tempTimestamp): bool
    {
       $query = self::database()->update('user')
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
        $query = self::database()->update('user')
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
        $query = self::database()->update('user')
                                 ->setValue('userName', $newName)
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
        $query = self::database()->update('user')
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
       $query = self::database()->update('user')
                                ->setValue('userPasswordHash', $passwordHash)
                                ->setValue('userPasswordResetHash', null)
                                ->setValue('userPasswordResetTimestamp', null)
                                ->whereEqual('userName', $userName)
                                ->whereEqual('userPasswordResetHash', $passwordResetHash);

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
        return self::database()->update('user')
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
        return self::database()->update('user')
                           ->setValue('userRememberMeToken', null)
                           ->whereEqual('userId', $userId)
                           ->execute();
        
    }

    /**
     * Deletes the given user
     *
     * @access public
     * @static
     * @param mixed     $userId
     *
     * @return bool
     */
    public static function deleteUser($userId = null): bool
    {
        return self::database()->delete('user')
                               ->whereEqual('userId', $userId)
                               ->execute();
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
        return self::database()->update('user')
                               ->setValue('userLastFailedLoginTimestamp', time())
                               ->increment('userFailedLoginCount', 1)
                               ->where()
                                    ->beginOr()
                                        ->equal('userName', $userNameOrEmail)
                                        ->equal('userEmail', $userNameOrEmail)
                                    ->closeOr()
                               ->execute();
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
        $users = self::database()->select('userId','userName', 'userEmail', 'userPasswordHash', 'userAccountType', 
                                            'userCreationTimestamp', 'userIdentifier', 'userHasAvatar', 'userAvatarId', 'userDeleted', 'userActivated',
                                            'userSuspensionTimestamp', 'userFailedLoginCount', 'userLastFailedLoginTimestamp')
                               ->from('user')
                               ->whereEqual('userId', $userId)
                               ->where()->notNull('userRememberMeToken')
                               ->whereEqual('userRememberMeToken', $token)
                               ->whereEqual('userProvider', 'DEFAULT')
                               ->getOne('obj');

        return count($users) > 0 ? $users[0] : false;
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
        $query = self::database()->update('user')
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
    public static function updateDeletionStatus($userId, $deleted): bool
    {
        $query = self::database()->update('user')
                                 ->setValue('userDeleted', $deleted ? 1 : 0)
                                 ->setValue('userDeletionTimestamp', $deleted ? time() : null)
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
        $query = self::database()->update('user')
                                 ->setValue('userHasAvatar', $hasAvatar ? 1 : 0)
                                 ->setValue('userAvatarId', $uid)
                                 ->whereEqual('userId', (int) $userId);

        return $query->execute() && $query->rowCount() === 1;          
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
	public static function insertNewAccount($userName, $userEmail, $userPasswordHash)
	{
        $userDirectory = \Kristuff\Minikit\Security\Token::getNewToken(16);
        $query = self::database()->insert('user')
                                 ->setValue('userName', $userName)
                                 ->setValue('userEmail', $userEmail)
                                 ->setValue('userProvider', 'DEFAULT')
                                 ->setValue('userPasswordHash', $userPasswordHash)
                                 ->setValue('userActivationHash', null)
                                 ->setValue('userIdentifier', $userDirectory)
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
     * @param string    $userName               The user's name.
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
        $userDirectory = \Kristuff\Minikit\Security\Token::getNewToken(16);
       
        $query = $database->insert('user')
                       ->setValue('userName', $userName)
                       ->setValue('userEmail', $userEmail)
                       ->setValue('userPasswordHash', $passwordHash)
                       ->setValue('userActivated', 1)
                       ->setValue('userCreationTimestamp', time())
                       ->setValue('userIdentifier', $userDirectory)
                       ->setValue('userAccountType', 7);

        return $query->execute() ? $query->lastId() : false;
    }

    /** 
     * Gets a user's profile data, according to the given user's name or user's email
     *
     * @access public
     * @static
     * @param string            $userNameOrEmail        The user's name or email address.
     *
     * @return mixed|bool       Returns the user as object if found, otherwise returns false.
     */
    public static function getUserByUserNameOrEmail(string $userNameOrEmail)
    {
        $users = self::database()->select('userId','userName', 'userEmail', 'userPasswordHash', 'userAccountType', 
                                            'userIdentifier', 'userHasAvatar', 'userAvatarId', 'userDeleted', 'userActivated',
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
                        ->column('userSessionId',                'varchar(64)',     'NULL',       'DEFAULT', 'NULL')                
                        ->column('userActivated',                'smallint',        'NOT NULL',   'DEFAULT', 0)
                        ->column('userDeleted',                  'smallint',        'NOT NULL',   'DEFAULT', 0)
                        ->column('userAccountType',              'smallint',        'NOT NULL',   'DEFAULT', 1)
                        ->column('userHasAvatar',                'smallint',        'NOT NULL',   'DEFAULT', 0)
                        ->column('userAvatarId',                 'varchar(32)',     'NULL')
                        ->column('userProvider',                 'varchar(48)',     'NOT NULL',   'DEFAULT', 'DEFAULT')
                        ->column('userRememberMeToken',          'varchar(64)',     'NULL')
                        ->column('userFailedLoginCount',         'smallint',        'NOT NULL',   'DEFAULT', 0)
                        ->column('userIdentifier',               'varchar(64)',     'NULL')
                        ->column('userCreationTimestamp',        $timeColumn,       'NULL')
                        ->column('userSuspensionTimestamp',      $timeColumn,       'NULL')
                        ->column('userDeletionTimestamp',        $timeColumn,       'NULL')
                        ->column('userLastLoginTimestamp',       $timeColumn,       'NULL')
                        ->column('userLastFailedLoginTimestamp', $timeColumn,       'NULL')
                        ->column('userPasswordResetTimestamp',   $timeColumn,       'NULL')
                        ->execute();
    }

}