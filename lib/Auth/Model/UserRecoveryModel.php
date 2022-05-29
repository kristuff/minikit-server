<?php

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.21 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */

namespace Kristuff\Minikit\Auth\Model;

use Kristuff\Minikit\Auth\Model\UserModel;
use Kristuff\Minikit\Mail\EmailBuilder;
use Kristuff\Minikit\Mvc\TaskResponse;
use Kristuff\Minikit\Mvc\Application;
use Kristuff\Minikit\Mail\Mailer;
use Kristuff\Minikit\Security\CaptchaModel;
use Kristuff\Minikit\Auth\Data\UsersCollection;
use Kristuff\Patabase;

/**
 * Class UserRecoveryModel
 *
 * Handles all the stuff that is related to the password-reset process.
 *
 * 1) UserRecoveryModel::verifyPasswordRecoveryRequest() => Verifies the recovery request (userName or email / captcha)
 *                                                          and sends an email with reset link if successfull 
 * 2) UserRecoveryModel::verifyPasswordResetLink()       => Verifies the password reset link sent by email
 * 3) UserRecoveryModel::handlePasswordReset()           => Handles the password reset form submission
 */
class UserRecoveryModel extends UserModel
{
    /** 
     * Gets whether the recovery process is enabled or not
     * 
     * @access public
     * @static
     *
     * @return bool         True if the the recovery process is enabled, otherwise false.
     */
    public static function isRecoveryEnabled(): bool
    {
        return self::config('AUTH_PASSWORD_RESET_ENABLED') === true; 
    }

    /**
     * Create and output the recovery captcha 
     *
     * @access public
     * @static
     * @param bool          $darkTheme
     *
     * @return void 
     */
    public static function outputCaptcha(bool $darkTheme = false)
    {
        CaptchaModel::captcha()->createAndOutput('recovery_captcha', $darkTheme);
    }
    
    /**
     * Handles the password recorey request 
     *
     * @access public
     * @static
     * @param string         $userNameOrEmail
     * @param string         $captcah
     *
     * @return TaskResponse
     */
    public static function verifyPasswordRecoveryRequest(?string $userNameOrEmail = '', ? string $captcha = ''): TaskResponse
    {
        $response = TaskResponse::create();  // default response is valid
  
        // check captcha and empty user name or email 
        if ($response->assertTrue(\Kristuff\Minikit\Security\CaptchaModel::captcha()->validate($captcha, 'recovery_captcha'), 400, self::text('ERROR_INVALID_CAPTCHA')) &&
            $response->assertFalse(empty($userNameOrEmail), 400, self::text('LOGIN_RECOVERY_ERROR_NAME_EMAIL_EMPTY'))){

            // get the userand check if that username exists
            // we need to check is the user exists, but we do not tell end user if so 
            // it could be an attacker who is testing for existing email/name 
            $user = UsersCollection::getUserByUserNameOrEmail($userNameOrEmail);
            if ($user !== false) {

                // generate integer-timestamp (to see when exactly the user (or an attacker) requested the password reset mail)
                // generate random hash for email password reset verification (40 char bytes)
                // expire in one hour (3600 secs)
                $tempTimestamp = time() + 3600 ;
                $userPasswordResetHash = bin2hex(random_bytes(40)); //TODO

                // set token (= a random hash string and a timestamp) into database ...
                // and send a mail to the user, containing a link with username and token hash string
                $response->assertTrue(UsersCollection::updatePasswordResetToken($user->userId, $userPasswordResetHash, $tempTimestamp), 400, self::text('LOGIN_RECOVERY_ERROR_WRITE_TOKEN_FAIL')) &&
                $response->assertTrue(self::sendPasswordResetMail($user->userName, $userPasswordResetHash, $user->userEmail), 500, self::text('LOGIN_RECOVERY_MAIL_SENDING_ERROR'));
            }
        }

        // If no errors, set a basic message. Request handled, that's all
        // Do not tell to a possible attacker if user or email exits or not in database 
        if (count($response->errors()) === 0){
            $response->setMessage(self::text('LOGIN_RECOVERY_SUCCESSFUL_HANDLING'));
        }

        return $response;
    }
       
    /**
     * Set the new password
     * Please note: At this point the user has already pre-verified via verifyPasswordReset() (within one hour),
     * so we don't need to check again for the 60min-limit here. In this method we authenticate
     * via username & password-reset-hash from (hidden) form fields.
     *
     * @access public
     * @static
     * @param  string       $userName
     * @param  string       $passwordResetHash
     * @param  string       $newPassword
     * @param  string       $repeatNewPassword
     *
     * @return TaskResponse
     */
    public static function handlePasswordReset($userName, $passwordResetHash, $newPassword, $repeatNewPassword): TaskResponse
    {
        $response = TaskResponse::create();
  
        // validate the password
        if (self::validatePasswordReset($response, $userName, $passwordResetHash, $newPassword, $repeatNewPassword)){

            // crypt the password (with the PHP 5.5+'s password_hash() function, result is 
            // a 60 character hash string)
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

            // write the passwordhash to database, reset userPasswordResetHash
            $saved = UsersCollection::updateNewPasswordByNameAndResetToken($userName, $passwordHash, $passwordResetHash);
        
            // set response message
            $response->assertTrue($saved, 500, self::text('USER_PASSWORD_CHANGE_FAILED')) &&
            $response->setMessage(self::text('USER_PASSWORD_CHANGE_SUCCESSFUL'));
        }
        return $response;
    }

    /**
     * Verifies the password reset request via the verification hash token (that's only valid for one hour)
     *
     * @static
     * @param  string   $userName           Username
     * @param  string   $verificationCode   Hash token
     *
     * @return 
     */
    public static function verifyPasswordResetLink($userName, $verificationCode)
    {
        $response = TaskResponse::create();

        // validate input
        $validateInput = !empty($userName) && !empty($verificationCode);
        if ($response->assertTrue($validateInput, 400, self::text('LOGIN_RECOVERY_NAME_HASH_NOT_FOUND'))){

            //get users by name and reset pass token
            $user = UsersCollection::getUserByNameAndResetPasswordHash($userName, $verificationCode);

            // if this user with exactly this verification hash code does NOT exist
            if ($response->assertTrue($user !== false, 400, self::text('LOGIN_RECOVERY_NAME_HASH_NOT_FOUND'))){
            
                // Check for timeout (password reset link is valid for one hour)
                $currentDate = new \DateTime('NOW');
                $expireDate = new \DateTime();
                $expireDate->setTimestamp((int) $user->userPasswordResetTimestamp);
                $expired = $currentDate > $expireDate;

                // set response message
                if ($response->assertFalse($expired, 400, self::text('LOGIN_RECOVERY_MAIL_LINK_EXPIRED'))){
                    $response->setMessage(self::text('LOGIN_RECOVERY_MAIL_LINK_VALIDATED'));
                }
            }
        }
        return $response;
    }

    /**
     * Send the password reset mail
     *
     * @access protected
     * @static
     * @param string    $userName              username
     * @param string    $passwordResetHash     password reset hash
     * @param string    $userEmail             user email
     *
     * @return bool     success status
     */
    protected static function sendPasswordResetMail($userName, $passwordResetHash, $userEmail)
    {
        $useHtml          = self::isHtmlEmailEnabled();
        $mailSubject      = sprintf(self::text('LOGIN_RECOVERY_EMAIL_SUBJECT'), $userEmail);
        $appName          = self::config('APP_NAME');
        $politePhrase     = self::text('AUTH_EMAIL_POLITE_PHRASE');
        $mailSignature    = sprintf(self::text('AUTH_EMAIL_SIGNATURE'), $appName);
        $mailCopyright    = "Copyright ". (date("Y"))." ".self::config('APP_COPYRIGHT');

        $intro            = sprintf(self::text('LOGIN_RECOVERY_EMAIL_INTRO'), $userEmail, $appName);
        $message          = self::text('LOGIN_RECOVERY_EMAIL_LINK_MESSAGE');
        $linkTitle        = self::text('LOGIN_RECOVERY_EMAIL_LINK_TITLE');
        $resetLink        = Application::getUrl().self::config('AUTH_PASSWORD_RESET_VERIFY_URL').'/'.urlencode($userName).'/'.urlencode($passwordResetHash);
        $expireNotice     = self::text('LOGIN_RECOVERY_EMAIL_EXPIRE_NOTICE');
        $notYouNotice = self::text('LOGIN_RECOVERY_EMAIL_NOT_YOU_NOTICE');
      
        if ($useHtml){
            $builder = EmailBuilder::getEmailBuilder();
            EmailBuilder::createHeader($builder, $mailSubject, '');
            EmailBuilder::createContent($builder, [$intro, $message]);
            EmailBuilder::createButton($builder, $linkTitle, $resetLink);
            EmailBuilder::createContent($builder, [$expireNotice, $notYouNotice]);
            EmailBuilder::createContent($builder, [$politePhrase, $mailSignature]);
            EmailBuilder::createFooter($builder, $appName, $mailCopyright);
            $content = $builder->getHtml();

        } else {
            $content  = $intro ;
            $content .= PHP_EOL ;
            $content .= PHP_EOL ;
            $content .= $message ;
            $content .= PHP_EOL ;
            $content .= PHP_EOL ;
            $content .= $linkTitle . PHP_EOL . $resetLink ;
            $content .= PHP_EOL ;
            $content .= PHP_EOL ;
            $content .= $expireNotice;
            $content .= PHP_EOL ;
            $content .= PHP_EOL ;
            $content .= $notYouNotice;
            $content .= PHP_EOL ;
            $content .= PHP_EOL ;
            $content .= $politePhrase ;
            $content .= PHP_EOL ;
            $content .= $mailSignature;
            $content .= PHP_EOL ;
            $content .= PHP_EOL ;
            $content .= $appName . ' | ' . $mailCopyright ;
        }
        $mail = new Mailer();
        $sent = $mail->sendMail($userEmail, 
                                self::config('AUTH_EMAIL_FROM_EMAIL'), 
                                self::config('AUTH_EMAIL_FROM_NAME'), 
                                $mailSubject, 
                                $content, 
                                $useHtml);

        return $sent ? true : false;
    }

    /**
     * Validate the password change or submission
     *
     * @access protected
     * @static
     * @param string    $userName
     * @param string    $userPasswordResetHash
     * @param string    $newPassword
     * @param string    $repeatNewPassword
     *
     * @return bool|array
     */
    protected static function validatePasswordReset(TaskResponse $response, $userName, $userPasswordResetHash, $newPassword, $repeatNewPassword)
    {
        // empty user name or hash?
        $response->assertFalse(empty($userName), 400, self::text('USER_NAME_ERROR_EMPTY'));
        $response->assertFalse(empty($userPasswordResetHash), 400, self::text('USER_PASSWORD_CHANGE_INVALID_TOKEN'));

        if ($response->success()){
            return self::validateUserPassword($response, $newPassword, $repeatNewPassword);
        }

        return false;
    }
}