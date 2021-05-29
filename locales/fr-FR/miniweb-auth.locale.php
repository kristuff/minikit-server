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
 * @version    0.9.4
 * @copyright  2017-2021 Kristuff
 */

/**
 * Texts used in the application.
 */
return array(

   /* commons errors */
   'ERROR_INVALID_REQUEST'                      => "Requête inavalide",
   'ERROR_PARAM_NULL_OR_EMPTY'                  => "Le paramètre '%s' était vide.",
   'ERROR_INVALID_AUTHENTFICATION'              => "Mauvaise authentification",
   'ERROR_INVALID_PERMISSIONS'                  => "Mauvaises permissions",
   'ERROR_INVALID_TOKEN'                        => "Token invalide",
   'ERROR_INVALID_CAPTCHA'                      => "Le code de sécurité est incorrecte.",
   'ERROR_UNKNOWN'                              => "Une erreur inconnue est survenue !",

   /* login error */
   "LOGIN_ERROR_ACCOUNT_DELETED"                => "Votre compte a été supprimé.",
   "LOGIN_ERROR_ACCOUNT_SUSPENDED"              => "Votre compte est suspendu pendant encore %s heure(s).",
   "LOGIN_ERROR_ACCOUNT_NOT_ACTIVATED"          => "Votre compte n'est pas encore activé. Activez votre compte en cliquant sur le lien dans l'email qui vous a été envoyé.",
   "LOGIN_ERROR_FAILED_3_TIMES"                 => "3 tentatives de login incorrectes. Attendez 30 secondes pour essayer à nouveau.",
   "LOGIN_ERROR_NAME_OR_PASSWORD_EMPTY"         => "Le nom d'utilisateur ou le mot de passe étaient vide.",
   "LOGIN_ERROR_NAME_OR_PASSWORD_WRONG"         => "Le nom d'utilisateur ou le mot de passe est incorrecte. Veuillez réessayer.",
   
   /* login cookie */
   "LOGIN_COOKIE_ERROR_INVALID"                 => "Votre remember-me-cookie est invalide.",
   "LOGIN_COOKIE_SUCCESSFUL"                    => "Vous vous êtes connecté avec succès via votre remember-me-cookie.",
  
   /* login recovery */
   'LOGIN_RECOVERY_TITLE'                       => "Demander une récupération de mot de passe",
   'LOGIN_RECOVERY_TEXT'                        => "Entrez votre nom d'utilisateur ou votre adresse email et nous vous enverrons un mail avec des instructions :", 
   'LOGIN_RECOVERY_BUTTON'                      => "Envoyer un mail de récupération", 
   'LOGIN_RECOVERY_ERROR_NAME_EMAIL_EMPTY'      => "Le nom d'utilisateur ou l'adresse email étaient vide.",
   'LOGIN_RECOVERY_ERROR_WRITE_TOKEN_FAIL'      => "Impossible d'écrire le tocken dans la base de données.",
   'LOGIN_RECOVERY_MAIL_SENDING_ERROR'          => "Le mail récupération de mot de passe n'a pas pu être envoyé : ",
   'LOGIN_RECOVERY_SUCCESSFUL_HANDLING'         => "Votre demande a été enregistrée. Nous avons envoyé un mail avec des instructions. Surveillez votre boite le réception.",
   'LOGIN_RECOVERY_NAME_HASH_NOT_FOUND'         => "La combinaison nom d'utilisateur/code de vérification est incorrecte.",
   'LOGIN_RECOVERY_MAIL_LINK_VALIDATED'         => "Le lien de récupération est correct. Vous pouvez changer votre mot de passe.",
   'LOGIN_RECOVERY_MAIL_LINK_EXPIRED'           => "Le lien de récupération a expiré. Le lien expire au bout d'une heure.",

   'xx'            => 'If you did not initiate this account recovery request, just ignore this email. We’ll keep your account safe.' ,

   /* user errors */
   'USER_ID_ERROR_EMPTY'                       => "Le champ 'User id' était vide.",
   'USER_ID_ERROR_BAD_FORMAT'                  => "Le champ 'User id' était incorrect.",
  
   /* user name */
   'USER_NAME_ERROR_EMPTY'                     => "Le champ 'Username' était vide.",
   'USER_NAME_ERROR_BAD_PATTERN'               => "Le champ 'Username' ne respecte pas le format requis : seulement les lettres (a-Z) et nombres sont autorisés, de 2 à 64 caractères.",
   'USER_NAME_ERROR_ALREADY_TAKEN'             => "Sorry, that username is already taken. Please choose another one.",
   'USER_NAME_ERROR_NEW_SAME_AS_OLD_ONE'       => "Sorry, that username is the same as your current one. Please choose another one.",
   'USER_NAME_CHANGE_SUCCESSFUL'               => 'Your user name has been changed successfully.',

   /* user email */
   'USER_EMAIL_ERROR_ALREADY_TAKEN'            => "Sorry, that email is already in use. Please choose another one.",
   'USER_EMAIL_ERROR_EMPTY'                    => "Email field was empty.",
   'USER_EMAIL_ERROR_REPEAT_WRONG'             => "Email and email repeat are not the same",
   'USER_EMAIL_ERROR_BAD_PATTERN'              => "Sorry, your chosen email does not fit into the email naming pattern.",
   'USER_EMAIL_ERROR_NEW_SAME_AS_OLD_ONE'      => "Sorry, that email address is the same as your current one. Please choose another one.",
   'USER_EMAIL_CHANGE_SUCCESSFUL'              => 'Your email address has been changed successfully.',

   /* user password */
   'USER_PASSWORD_ERROR_EMPTY'                 => "Password field was empty.",
   'USER_PASSWORD_ERROR_REPEAT_WRONG'          => "Password and password repeat are not the same.",
   'USER_PASSWORD_ERROR_TOO_SHORT'             => "Password has a minimum length of 6 characters.",
   'USER_PASSWORD_CHANGE_SUCCESSFUL'           => "Password successfully changed.",
   'USER_PASSWORD_CHANGE_FAILED'               => "Sorry, your password changing failed.",
   'USER_PASSWORD_CHANGE_NEW_SAME_AS_CURRENT'  => "New password is the same as the current password.",
   'USER_PASSWORD_CHANGE_CURRENT_INCORRECT'    => "Current password entered was incorrect.",
   'USER_PASSWORD_CHANGE_INVALID_TOKEN'        => "No or invalid password reset token.",
   'USER_PASSWORD_CHANGE_ERROR_CURRENT_WRONG'  => "Current password entered was incorrect.",
 
    /* user new account */
   'USER_NEW_ACCOUNT_ERROR_CREATION_FAILED'    => "Sorry, your registration failed. Please go back and try again.",
   'USER_NEW_ACCOUNT_ERROR_DEFAULT_SETTINGS'   => "Internal error: unable to insert defaults settings data",
   'USER_NEW_ACCOUNT_SUCCESSFULLY_CREATED'     => "Your account has been created successfully and we have sent you an email. Please click the VERIFICATION LINK within that mail.",
   'USER_NEW_ACCOUNT_MAIL_SENDING_ERROR'       => "Verification mail could not be sent due to: ",
   'USER_NEW_ACCOUNT_MAIL_SENDING_SUCCESSFUL'  => "A verification mail has been sent successfully.",
   'USER_NEW_ACCOUNT_ACTIVATION_SUCCESSFUL'    => "Activation was successful! You can now log in.",
   'USER_NEW_ACCOUNT_ACTIVATION_FAILED'        => "Sorry, no such id/verification code combination here! It might be possible that your mail provider (Yahoo? Hotmail?) automatically visits links in emails for anti-scam scanning, so this activation link might been clicked without your action. Please try to log in on the main page.",
   
    /* user avatar */
   'USER_AVATAR_UPLOAD_NO_FILE'                         => "Aucun fichier n'a été envoyé.",
   'USER_AVATAR_UPLOAD_FAILED'                 => "Something went wrong with the image upload.",
   'USER_AVATAR_UPLOAD_SUCCESSFUL'             => "Avatar upload was successful.",
   'USER_AVATAR_UPLOAD_ERROR_WRONG_TYPE'       => "Only JPEG and PNG files are supported.",
   'USER_AVATAR_UPLOAD_ERROR_TOO_SMALL'        => "Avatar source file's width/height is too small. Needs to be 100x100 pixel minimum.",
   'USER_AVATAR_UPLOAD_ERROR_TOO_BIG'          => "Avatar source file is too big. 1 Megabyte is the maximum.",
   'USER_AVATAR_DELETE_ERROR_NO_FILE'          => "You don't have a custom avatar.",
   'USER_AVATAR_DELETE_FAILED'                 => "Something went wrong while deleting your avatar.",
   'USER_AVATAR_DELETE_SUCCESSFUL'                      => "Votre avatar a été supprimé.",
   'USER_AVATAR_ERROR_PATH_MISSING'            => "Avatar path does not exist",
   'USER_AVATAR_ERROR_PATH_PERMISSIONS'        => "Avatar path is not writable (invalid permissions)",

   /* user account admin */
   'USER_ACCOUNT_ERROR_DELETE_SUSPEND_OWN'             => 'You can not delete or suspend your own account.',
   'USER_ACCOUNT_SUSPENSION_DELETION_STATUS_CHANGED'   => "The user's suspension / deletion status has been edited.",
   'USER_ACCOUNT_SUCCESSFULLY_KICKED'                  => "The selected user has been successfully kicked out of the system.",
   'USER_ACCOUNT_SUCCESSFULLY_DELETED'                 => "The user's account has been successfully deleted.",
   'USER_ACCOUNT_ERROR_DELETETION_FAILED'              => "The user's account deletion failed!",
   'USER_ACCOUNT_SUCCESSFULLY_CREATED'                 => "The user's account has been successfully created.",
   'USER_ACCOUNT_SUSPENSION_ERROR_DAYS'                => "The suspension days was invalid.",
   
   /* user invitation */
   'USER_INVITATION_NOT_ENABLED'               => 'Invitation process is not enabled.',
   'USER_INVITATION_VALIDATION_SUCCESSFUL'     => 'Please define your user name and password to complete your registration',
   'USER_INVITATION_EMAIL_SUBJECT'             => 'Invitation received from %s',
   'USER_INVITATION_EMAIL_CONTENT_TITLE'       => 'Welcome!',
   'USER_INVITATION_EMAIL_CONTENT_PART_1'      => 'You receive this email from %s. An account has been created for you.',
   'USER_INVITATION_EMAIL_CONTENT_PART_2'      => 'You need to click on link bellow to complete your registration and activate your account. You will be ask to define your user name and a password.',
   'USER_INVITATION_EMAIL_CONTENT_PART_3'      => 'Don\'t wait ! This link will expire on %s.', 
   'USER_INVITATION_EMAIL_LINK_TITLE'          => 'Complete registration', 
   'USER_INVITATION_EMAIL_CONTENT_PART_4'      => 'Regards,', 
   'USER_INVITATION_EMAIL_SIGNATURE'           => 'The %s team', 
   'USER_INVITATION_EMAIL_SENT_SUCCESSFULLY'   => 'An invitation mail has been sent successfully. The user will need to complete its registration before to log in.', 
   "USER_INVITATION_PROCESS_COMPLETE"          => 'An account has been created successfully (temporary user name: %s) and we have sent an email at %s. <br>This account will not be active until the user completes its registration.' ,


);