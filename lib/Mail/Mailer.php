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

    
namespace Kristuff\Minikit\Mail;

use Kristuff\Minikit\Mvc\Application;

/** 
 * Class Mailer
 *
 * Handles mail-sending.
 */
class Mailer
{
	/**
     * @var mixed   	$error      	variable to collect errors 
     */
	private $error;

   /**
     * Get a configuration value 
     * 
	 * @param string   	$key        	The config key
	 * 
     * @return mixed 	
     */
	private function getConfig(string $key)
	{
		return Application::config($key);
	}

    /**
     * The main mail sending method, this simply calls a certain mail sending method depending on which mail provider
     * you've selected in the application's config.
     * 
	 * @param string   	$toEmail        The email adress to send to
	 * @param string   	$fromEmail      The email adress of sender
	 * @param string   	$fromName       The name of sender
	 * @param string   	$subjectEmail   The email subject
	 * @param string   	$bodyEmail      The main content message to be sent.
	 * @param bool     [$isHtmlEmail]   True for html email. Default is false. 
     *
     * @return bool the success status of the according mail sending method
     */
	public function sendMail($toEmail, $fromEmail, $fromName, $subjectEmail, $bodyEmail, $isHtmlEmail = false)
	{
        switch( $this->getConfig('EMAIL_MAILER')){
			
			case 'phpmailer': 
				return $this->sendMailWithPhpMailer($toEmail, $fromEmail, $fromName, $subjectEmail, $bodyEmail, $isHtmlEmail); 
			
			case 'native':
				return $this->sendMailWithNativeFunction($toEmail, $fromEmail, $fromName, $subjectEmail, $bodyEmail, $isHtmlEmail);
			
			default: 
				return false;
        }
	}

    /**
     * The different mail sending methods write errors to the error property $this->error,
     * this method simply returns this error / error array.
     *
     * @return mixed
     */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * Sends a mail by using PHPMailer.
	 * Depending on your EMAIL_USE_SMTP setting this will work via SMTP credentials or via native mail()
	 *
     * @access public
	 * @param string   	$toEmail        The email adress to send to
	 * @param string   	$fromEmail      The email adress of sender
	 * @param string   	$fromName       The name of sender
	 * @param string   	$subjectEmail   The email subject
	 * @param string   	$bodyEmail      The main content message to be sent.
	 * @param bool     [$isHtmlEmail]   True for html email. Default is false. 
	 *
	 * @return bool
	 * @throws Exception
	 * @throws phpmailerException
	 */
	private function sendMailWithPhpMailer($toEmail, $fromEmail, $fromName, $subjectEmail, $bodyEmail, $isHtmlEmail = false)
	{
		$mail = new \PHPMailer\PHPMailer\PHPMailer;
        
        // you should use UTF-8 to avoid encoding issues
        $mail->CharSet = 'UTF-8';

		// if you want to send mail via PHPMailer using SMTP credentials
		if ($this->getConfig('EMAIL_USE_SMTP')) {
			$mail->IsSMTP(); 		// set PHPMailer to use SMTP
			$mail->SMTPDebug = 0; 	// 0 = off, 1 = commands, 2 = commands and data, perfect to see SMTP errors
			$mail->SMTPAuth = $this->getConfig('EMAIL_SMTP_AUTH'); // enable SMTP authentication

			// encryption
			if ($this->getConfig('EMAIL_SMTP_ENCRYPTION')) {
				$mail->SMTPSecure = $this->getConfig('EMAIL_SMTP_ENCRYPTION');
			}
			// set SMTP provider's credentials
			$mail->Host 	= $this->getConfig('EMAIL_SMTP_HOST');
			$mail->Username = $this->getConfig('EMAIL_SMTP_USERNAME');
			$mail->Password = $this->getConfig('EMAIL_SMTP_PASSWORD');
			$mail->Port 	= $this->getConfig('EMAIL_SMTP_PORT');
		
		} else {
			$mail->IsMail();
		}

		// fill mail with data
		$mail->AddAddress($toEmail);
        $mail->IsHTML($isHtmlEmail); 
		$mail->From 		= $fromEmail;
		$mail->FromName 	= $fromName;
		$mail->Subject 		= $subjectEmail;
		$mail->Body 		= $bodyEmail;

		// try to send mail, put result status (true/false into $wasSendingSuccessful)
		if(!$mail->Send()){
		    // if not successful, copy errors into Mail's error property
		    $this->error = $mail->ErrorInfo;
		    return false;
		}

        return true;
	}

    /**
	 * Sends a mail by using native function
	 *
     * @access public
	 * @param string   	$toEmail        The email adress to send to
	 * @param string   	$fromEmail      The email adress of sender
	 * @param string   	$fromName       The name of sender
	 * @param string   	$subjectEmail   The email subject
	 * @param string   	$bodyEmail      The main content message to be sent.
	 * @param bool     [$isHtmlEmail]   True for html email. Default is false. 
	 *
	 * @return bool
	 */
	public function sendMailWithNativeFunction($toEmail, $fromEmail, $fromName, $subjectEmail, $bodyEmail, $isHtmlEmail = false)
    {
        // email header
        $headers = []; 

        // To send HTML mail, the Content-type header must be set
        if ($isHtmlEmail){
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=iso-8859-1';
        }
        
        // @see http://php.net/manual/en/function.mail.php
        // Each line should be separated with a CRLF (\r\n). Lines should not be larger than 70 characters.
        $message = wordwrap($bodyEmail, 70, "\r\n");

        // Additional headers
        $headers[] = 'From: '. $fromName. ' <' . $fromEmail .'>';

        // Mail it
       return mail($toEmail, $subjectEmail, $message, implode("\r\n", $headers));
    }
}