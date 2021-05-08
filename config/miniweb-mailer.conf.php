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
 * @version    0.9.3
 * @copyright  2017-2021 Kristuff
 */

/** 
 * Returns the default configuration as array
 */
return array(

    /**
     * -------------------------------------------
     * Configuration for: Email server credentials
     * -------------------------------------------
     *
     * Here you can define how you want to send emails.
     * If you have successfully set up a mail server on your linux server and you know
     * what you do, then you can skip this section. Otherwise please set EMAIL_USE_SMTP to true
     * and fill in your SMTP provider account data.
     *
     * EMAIL_MAILER: 'phpmailer' or 'native'
     * EMAIL_USE_SMTP: Use SMTP or not
     * EMAIL_SMTP_AUTH: leave this true unless your SMTP service does not need authentication
     */
    'EMAIL_MAILER'          => 'phpmailer',
    'EMAIL_USE_SMTP'        => false,
    'EMAIL_SMTP_HOST'       => 'yourhost',
    'EMAIL_SMTP_AUTH'       => true,
    'EMAIL_SMTP_USERNAME'   => 'yourusername',
    'EMAIL_SMTP_PASSWORD'   => 'yourpassword',
    'EMAIL_SMTP_PORT'       => 465,
    'EMAIL_SMTP_ENCRYPTION' => 'ssl',

);