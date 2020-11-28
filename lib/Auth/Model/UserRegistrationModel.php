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

use Kristuff\Miniweb\Mail\Mailer;
use Kristuff\Miniweb\Auth\Model\UserModel;
use Kristuff\Miniweb\Mvc\TaskResponse;
use Kristuff\Miniweb\Mvc\Application;

/** 
 * Class RegistrationModel
 *
 * Handles registration process.
 */
class UserRegistrationModel extends UserModel
{
    /** 
     * Gets whether the registration process is enabled or not
     * 
     * @access public
     * @static
     *
     * @return bool         True if the the registration process is enabled, otherwise false.
     */
    public static function isRegistrationEnabled()
    {
        return self::config('AUTH_SIGNUP_ENABLED') === true; 
    }

    /**
     * Create and output the registration captcha 
     *
     * @access public
     * @static
     *
     * @return mixed 
     */
    public static function outputCaptcha()
    {
        \Kristuff\Miniweb\Security\CaptchaModel::captcha()->create('AUTH_SIGNUP_captcha');
        \Kristuff\Miniweb\Security\CaptchaModel::captcha()->output();
    }
        
    /**
     * Handle registraion request
     * 
     * Handles the registration process for DEFAULT users: creates a new user in the database and send 
     * an email to user to confirm its account.
     *
     * @access public
     * @static
     * @param  string           $userName               The user's name.
     * @param  string           $userEmail              The user's email address.
     * @param  string           $userEmailRepeat        The repeated user's email address.
     * @param  string           $userPassword           The user's password.
     * @param  string           $userPasswordRepeat     The repeated user's password.
     * @param  string           $captcha                The captacha value.
     *
     * @return TaskResponse
     */
    public static function handleRegistrationRequest($userName, $userEmail, $userEmailRepeat, $userPassword, $userPasswordRepeat, $captcha)
    {
        // the return response
        $response = TaskResponse::create();
        
        // clean the inputs
        $userName           = strip_tags($userName);
        $userEmail          = strip_tags($userEmail);
        $userEmailRepeat    = strip_tags($userEmailRepeat) ;

        // validate the inputs
        self::validateRegistrationCaptcha($response, $captcha);
        self::validateUserNamePattern($response, $userName);
        self::validateUserNameNoConflict($response, $userName);
        self::validateUserEmailPattern($response, $userEmail, $userEmailRepeat);
        self::validateUserEmailNoConflict($response, $userEmail);
        self::validateUserPassword($response, $userPassword, $userPasswordRepeat);

        // stop registration flow if anything breaks the input check rules
        // else continue...
        if ($response->success()) {

            // crypt the password with the PHP 5.5's password_hash() function, results in a 60 character hash string.
            // @see php.net/manual/en/function.password-hash.php for more, especially for potential options
            // and generate random hash for email verification (40 char string)
            $userPasswordHash = password_hash($userPassword, PASSWORD_DEFAULT);
            $userActivationHash = sha1(uniqid(mt_rand(), true));

            // write user data to database
            if ($response->assertTrue(self::writeNewUser($userEmail, $userName, $userPasswordHash, $userActivationHash), 500, 
                                       self::text('USER_NEW_ACCOUNT_ERROR_CREATION_FAILED'))){

                // get user_id of the user that has been created, to keep things clean we DON'T use lastInsertId() here
                $userId = UserModel::getUserIdByUsername($userName);
                if ($response->assertTrue($userId !== false, 500, self::text('UNKNOWN_ERROR'))){

                    // send verification email
                    $mailSent = self::sendVerificationEmail($userId, $userEmail, $userActivationHash);
                    if (!$response->assertTrue($mailSent, 500, self::text('USER_NEW_ACCOUNT_MAIL_SENDING_ERROR'))){

                        // if verification email sending failed: instantly delete the user
                        self::rollbackRegistrationByUserId($userId);
                        return $response;
                    }        

                    // set success message and return response
                    $response->setMessage(self::text('USER_NEW_ACCOUNT_SUCCESSFULLY_CREATED'));
                }
            }
        }

        // return response
        return $response;
    }
    
    /**
     * Verify registered user
     * 
     * Checks the id/verification code combination and set the user's activation status to true in the database
     *
     * @access public
     * @static
     * @param  int       $userId                 The user's id
     * @param  string    $userActivationHash     The user's mail verification hash string
     *
     * @return TaskResponse
     */
    public static function verifyRegisteredUser($userId, $userActivationHash)
    {
        $query = self::database()->update('user')
                                 ->setValue('userActivated', 1)
                                 ->setValue('userActivationHash', null)
                                 ->whereEqual('userId', $userId)
                                 ->where()->notNull('userPasswordHash')
                                 ->whereEqual('userActivationHash', $userActivationHash);
        
        // check if query was successfull
        $success = ($query->execute() && $query->rowCount() == 1);
        
        // create response        
        $response = TaskResponse::create();
        if ($response->assertTrue($success, 500, self::text('USER_NEW_ACCOUNT_ACTIVATION_FAILED'))){
            $response->setMessage(self::text('USER_NEW_ACCOUNT_ACTIVATION_SUCCESSFUL'));
        }

        // return response
        return $response;
    }
    
    /**
     * Validates the registration captcha
     *  
     * Return true if the captcha is valid
     *
     * @access protected
     * @static
     * @param  TaskResponse    $response               The response instance.
     * @param  string           $captcha                The captcha value
     *
     * @return bool
     */
    protected static function validateRegistrationCaptcha(TaskResponse $response, $captcha)
    {
        $isvalid = \Kristuff\Miniweb\Security\CaptchaModel::captcha()->validate($captcha, 'AUTH_SIGNUP_captcha');
        return $response->assertTrue($isvalid, 400, self::text('ERROR_INVALID_CAPTCHA'));
    }

    /**
     * Writes the new user's data to the database
     *
     * @access protected
     * @static
     * @param  string           $userEmail              The user's email address.
     * @param  string           $userName               The user's name.
     * @param  string           $userPasswordHash       The hashed user's password.
     * @param  string           $activationHash         The user's mail verification hash string
     *
     * @return bool
     */
    protected static function writeNewUser($userEmail, $userName, $userPasswordHash, $activationHash)
    {
        $userDirectory = \Kristuff\Miniweb\Security\Token::getNewToken(16);
        $query = self::database()->insert('user')
                        ->setValue('userName', $userName)
                        ->setValue('userEmail', $userEmail)
                        ->setValue('userPasswordHash', $userPasswordHash)
                        ->setValue('userActivationHash', $activationHash)
                        ->setValue('userDataDirectory', $userDirectory)
                        ->setValue('userCreationTimestamp', time())
                        ->setValue('userActivated', 0)
                        ->setValue('userProvider', 'DEFAULT')
                        ->setValue('userAccountType', 1);

        return $query->execute() && $query->rowCount() === 1;
    }

    /**
     * Deletes the user from user table. Currently used to rollback a registration when verification mail sending
     * was not successful.
     *
     * @access protected
     * @method static
     * @param  int              $useId                  The user's id
     *
     * @return bool
     */
    protected static function rollbackRegistrationByUserId($userId)
    {
        return self::database()->delete('user')
                               ->whereEqual('userId', $userId)
                               ->execute();
    }

    /**
     * Sends the verification email to confirm the account.
     *
     * @access protected
     * @method static
     * @param int       $userId                 The user's id
     * @param string    $userEmail              The user's email
     * @param string    $userActivationHash     The user's mail verification hash string
     *
     * @return bool     
     */
    protected static function sendVerificationEmail($userId, $userEmail, $userActivationHash)
    {
        // TODO Html mail
        // TODO locale

        // create email body
        $body = self::config('AUTH_SIGNUP_EMAIL_VERIFICATION_CONTENT') . ' ' . Application::getUrl() .
                self::config('AUTH_SIGNUP_EMAIL_VERIFICATION_URL') . '/' . urlencode($userId) . '/' . urlencode($userActivationHash);

        // $mailBody = Application::config('EMAIL_VERIFICATION_CONTENT') ;
        // $mailBody .= '<a href="'. Application::getUrl() . Application::config('EMAIL_VERIFICATION_URL') ;
        // $mailBody .=  '/' . urlencode($user_id) . '/' . urlencode($user_activation_hash) ;
        // $mailBody .=  '" style="color:#f26522">Activate my account now</a>' ;
        // $mailFooter = "Copyright ". (date("Y"))." ". Application::config('FOOTER_COPYRIGHT');
        // $body = Mail::getHtmlMailString(Application::config('APP_NAME'), Application::config('EMAIL_PASSWORD_RESET_SUBJECT'), 
        // $mailBody, $mailFooter);

        $mail = new Mailer();
        $mailSent = $mail->sendMail($userEmail, 
            self::config('AUTH_SIGNUP_EMAIL_VERIFICATION_FROM_EMAIL'),
            self::config('AUTH_SIGNUP_EMAIL_VERIFICATION_FROM_NAME'), 
            self::config('AUTH_SIGNUP_EMAIL_VERIFICATION_SUBJECT'), 
            $body, false
        );

        return $mailSent ? true : false;
    }
}