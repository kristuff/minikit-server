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


/**
 * Texts used in the application.
 */
return array(

    /* system */
    'LOCAL_NAME'                                => "Français",
    'LOCAL_CODE'                                => "fr-FR",

    /* formats */
    'FIELD_ENDING'                              => ' :',
    'FORMAT_DATE'                               => "d/m/Y",
    'FORMAT_DATE_LONG'                          => "j F Y",
    'FORMAT_DATE_TIME'                          => "d/m/Y H:i:s",
    'FORMAT_DECIMAL_SEPARATOR '                 => ',',
    'FORMAT_THOUSANDS_SEPARATOR '               => ' ',

    /* time */
    'TIME_YEAR'                                 => "année",
    'TIME_MONTH'                                => "mois",
    'TIME_DAY'                                  => "jour",
    'TIME_HOUR'                                 => "heure",
    'TIME_MINUTE'                               => "minute",
    'TIME_SECOND'                               => "seconde",
    'REL_TIME_YEAR'                             => "Il y a %d an%s",
    'REL_TIME_MONTH'                            => "Il y a %d mois%s",
    'REL_TIME_DAY'                              => "Il y a %d jour%s",
    'REL_TIME_WEEK'                             => "Il y a %d semaine%s",
    'REL_TIME_HOUR'                             => "Il y a %d heure%s",
    'REL_TIME_MINUTE'                           => "Il y a %d minute%s",
    'REL_TIME_SECOND'                           => "Il y a %d seconde%s",

    /* commons errors */
    'ERROR_INVALID_REQUEST'                      => "Requête invalide",
    'ERROR_PARAM_NULL_OR_EMPTY'                  => "Le paramètre '%s' était vide.",
    'ERROR_INVALID_AUTHENTFICATION'              => "Mauvaise authentification",
    'ERROR_INVALID_PERMISSIONS'                  => "Mauvaises permissions",
    'ERROR_INVALID_TOKEN'                        => "Token invalide",
    'ERROR_INVALID_CAPTCHA'                      => "Le code de sécurité est incorrect.",
    'ERROR_UNKNOWN'                              => "Une erreur inconnue est survenue !",

    /* auth */
    'AUTH_EMAIL_POLITE_PHRASE'                  => 'Cordialement,', 
    'AUTH_EMAIL_SIGNATURE'                      => "L'équipe %s", 

    /* login error */
    "LOGIN_ERROR_ACCOUNT_DELETED"                => "Votre compte a été supprimé.",
    "LOGIN_ERROR_ACCOUNT_SUSPENDED"              => "Votre compte est suspendu pendant encore %s heure(s).",
    "LOGIN_ERROR_ACCOUNT_NOT_ACTIVATED"          => "Votre compte n'est pas encore activé. Activez votre compte en cliquant sur le lien dans l'email qui vous a été envoyé.",
    "LOGIN_ERROR_FAILED_3_TIMES"                 => "3 tentatives de login incorrectes. Attendez 30 secondes pour essayer à nouveau.",
    "LOGIN_ERROR_NAME_OR_PASSWORD_EMPTY"         => "Le nom d'utilisateur ou le mot de passe étaient vide.",
    "LOGIN_ERROR_NAME_OR_PASSWORD_WRONG"         => "Le nom d'utilisateur ou le mot de passe est incorrect. Veuillez réessayer.",
    
    /* login cookie */
    "LOGIN_COOKIE_ERROR_INVALID"                 => "Votre remember-me-cookie est invalide.",
    "LOGIN_COOKIE_SUCCESSFUL"                    => "Vous vous êtes connecté avec succès via votre remember-me-cookie.",
    
    /* login recovery */
    'LOGIN_RECOVERY_TITLE'                       => "Demander une récupération de mot de passe",
    'LOGIN_RECOVERY_TEXT'                        => "Entrez votre nom d'utilisateur ou votre adresse email et nous vous enverrons un email avec des instructions.", 
    'LOGIN_RECOVERY_BUTTON'                      => "Envoyer un mail de récupération", 
    'LOGIN_RECOVERY_ERROR_NAME_EMAIL_EMPTY'      => "Le nom d'utilisateur ou l'adresse email étaient vide.",
    'LOGIN_RECOVERY_ERROR_WRITE_TOKEN_FAIL'      => "Impossible d'écrire le tocken dans la base de données.",
    'LOGIN_RECOVERY_MAIL_SENDING_ERROR'          => "Le mail récupération de mot de passe n'a pas pu être envoyé : ",
    'LOGIN_RECOVERY_SUCCESSFUL_HANDLING'         => "Votre demande a été enregistrée. Nous vous avons envoyé un mail avec des instructions. Surveillez votre boite de réception.",
    'LOGIN_RECOVERY_NAME_HASH_NOT_FOUND'         => "La combinaison nom d'utilisateur/code de vérification est incorrecte.",
    'LOGIN_RECOVERY_MAIL_LINK_VALIDATED'         => "Le lien de récupération est correct. Vous pouvez changer votre mot de passe.",
    'LOGIN_RECOVERY_MAIL_LINK_EXPIRED'           => "Le lien de récupération a expiré. Le lien expire au bout d'une heure.",
    'LOGIN_RECOVERY_EMAIL_SUBJECT'               => "Demande de récupération de mot de passe reçue pour %s",
    'LOGIN_RECOVERY_EMAIL_INTRO'                 => "Nous avons reçu une demande de récupération de mot de passe pour %s sur %s.",
    'LOGIN_RECOVERY_EMAIL_LINK_MESSAGE'          => "Si vous avez initié cette demande, cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :",
    'LOGIN_RECOVERY_EMAIL_LINK_TITLE'            => 'Réinitialiser mon mot de passe', 
    'LOGIN_RECOVERY_EMAIL_EXPIRE_NOTICE'         => "N'attendez pas ! Ce lien expirera dans une heure.", 
    'LOGIN_RECOVERY_EMAIL_NOT_YOU_NOTICE'        => "Si vous n'avez pas effectué cette demande de récupération de compte, veuillez simplement ignorer cet e-mail. Nous garderons votre compte en sécurité.", 

    /* user errors */
    'USER_ID_ERROR_EMPTY'                       => "Le champ 'User id' était vide.",
    'USER_ID_ERROR_BAD_FORMAT'                  => "Le champ 'User id' était incorrect.",
    
    /* user name */
    'USER_NAME_ERROR_EMPTY'                     => "Le champ 'Username' était vide.",
    'USER_NAME_ERROR_BAD_PATTERN'               => "Le champ 'Username' ne respecte pas le format requis : seulement les lettres (a-Z) et nombres sont autorisés, de 2 à 64 caractères.",
    'USER_NAME_ERROR_ALREADY_TAKEN'             => "Désolé, ce nom est déjà utilisé. Choisissez un autre nom.",
    'USER_NAME_ERROR_NEW_SAME_AS_OLD_ONE'       => "Désolé, ce nom est identique au nom actuel. Choisissez un autre nom.",
    'USER_NAME_CHANGE_SUCCESSFUL'               => 'Votre nom d\'utilisateur a été modifié avec succès.',

    /* user email */
    'USER_EMAIL_ERROR_ALREADY_TAKEN'            => "Désolé, cette adresse email est déjà utilisée. Choisissez une autre adresse email.",
    'USER_EMAIL_ERROR_EMPTY'                    => "Le champ 'Email' était vide.",
    'USER_EMAIL_ERROR_REPEAT_WRONG'             => "Les champs 'Email' et 'email_repeat' ne sont pas identiques.",
    'USER_EMAIL_ERROR_BAD_PATTERN'              => "Désolé, l'adresse email ne correspond pas à une adresse email valide.",
    'USER_EMAIL_ERROR_NEW_SAME_AS_OLD_ONE'      => "Désolé, cett adresse email est identique à l'adresse actuelle. Choisissez une autre adresse email.",
    'USER_EMAIL_CHANGE_SUCCESSFUL'              => 'Votre adresse email a été modifiée avec succès.',

    /* user password */
    'USER_PASSWORD_ERROR_EMPTY'                 => "Le champ 'Password' était vide.",
    'USER_PASSWORD_ERROR_REPEAT_WRONG'          => "Les champs 'password' et 'password_repeat' ne sont pas identiques.",
    'USER_PASSWORD_ERROR_TOO_SHORT'             => "Le mot de passe doit contenir au moins 8 caractères.",
    'USER_PASSWORD_CHANGE_SUCCESSFUL'           => "Votre mot de passe a été modifiée avec succès.",
    'USER_PASSWORD_CHANGE_FAILED'               => "Désolé, votre changement de mot de passe a échoué.",
    'USER_PASSWORD_CHANGE_NEW_SAME_AS_CURRENT'  => "Le nouveau mot de passe est identique au mot de passe actuel.",
    'USER_PASSWORD_CHANGE_INVALID_TOKEN'        => "Le token de vérification de changement de mot de passe était incorrect.",
    'USER_PASSWORD_CHANGE_ERROR_CURRENT_WRONG'  => "Le mot de passe entré était incorrect.",
    
    /* user new account */
    'USER_NEW_ACCOUNT_ERROR_CREATION_FAILED'    => "Désolé, votre enregistrement a échoué. Veuillez réessayer plus tard.",
    'USER_NEW_ACCOUNT_ERROR_DEFAULT_SETTINGS'   => "Erreur : impossible d'ajouter les paramètres utilisateurs.",
    'USER_NEW_ACCOUNT_SUCCESSFULLY_CREATED'     => "Votre compte a été créé avec succès et nous vous avons envoyé un email. Veuillez cliquer sur le lien d'activation dans cet email.",
    'USER_NEW_ACCOUNT_MAIL_SENDING_ERROR'       => "Le mail de vérification n'a pas pu être envoyé. ",
    'USER_NEW_ACCOUNT_ACTIVATION_SUCCESSFUL'    => "L'activation a réussi ! Vous pouvez vous connecter dès maintenant.",
    'USER_NEW_ACCOUNT_ACTIVATION_FAILED'        => "Désolé, le code de vérification ne peut être vérifié ! Il est possible que automatically visits links in emails for anti-scam scanning, so this activation link might been clicked without your action. Please try to log in on the main page.",
    'USER_SIGNUP_EMAIL_VERIFICATION_SUBJECT'    => 'Activation du compte %s sur %s',
    'USER_SIGNUP_EMAIL_VERIFICATION_INTRO'      => "Merci d'avoir créé un compte. Il vous reste à l'activer pour pouvoir vous connecter avec ce compte.",
    'USER_SIGNUP_EMAIL_VERIFICATION_LINK_MESSAGE'    => 'Veuillez cliquer sur le lien pour activer votre compte : ',
    'USER_SIGNUP_EMAIL_VERIFICATION_LINK_TITLE' => "Activer mon compte",

    /* user avatar */
    'USER_AVATAR_UPLOAD_NO_FILE'                => "Aucun fichier n'a été envoyé.",
    'USER_AVATAR_UPLOAD_FAILED'                 => "Une erreur est survenue durant le téléchargement de l'image.",
    'USER_AVATAR_UPLOAD_SUCCESSFUL'             => "L'avatar a été téléchargé avec succès.",
    'USER_AVATAR_UPLOAD_ERROR_WRONG_TYPE'       => "Seuls les fichiers images JPEG et PNG sont supportés.",
    'USER_AVATAR_UPLOAD_ERROR_TOO_SMALL'        => "Le fichier image source est trop petit. Il doit être d'au moins 100x100 pixels.",
    'USER_AVATAR_UPLOAD_ERROR_TOO_BIG'          => "Le fichier image source est trop gros. 1 Mégabyte est le maximum.",
    'USER_AVATAR_DELETE_ERROR_NO_FILE'          => "Vous n'avez pas d'avatar personnalisé.",
    'USER_AVATAR_DELETE_FAILED'                 => "Une erreur est survenue durant la suppression de votre avatar.",
    'USER_AVATAR_DELETE_SUCCESSFUL'             => "Votre avatar a été supprimé.",
    'USER_AVATAR_ERROR_PATH_MISSING'            => "Le répertoire Avatar est introuvable.",
    'USER_AVATAR_ERROR_PATH_PERMISSIONS'        => "Le répertoire Avatar n'est pas accessible en écriture (mauvaises permissions)",

    /* user account admin */
    'USER_ACCOUNT_ERROR_DELETE_SUSPEND_OWN'             => 'Vous ne pouvez pas supprimer ou suspendre votre propre compte.',
    'USER_ACCOUNT_SUSPENSION_DELETION_STATUS_CHANGED'   => "Le status de suspension / suppression  de l'utilisateur a été modifié.",
    'USER_ACCOUNT_SUCCESSFULLY_KICKED'                  => "L'utilisateur sélectionné a été expulsé du système avec succès.",
    'USER_ACCOUNT_SUCCESSFULLY_DELETED'                 => "Le compte utilisateur a été supprimé avec succès.",
    'USER_ACCOUNT_ERROR_DELETION_FAILED'              => "La suppression du compte utilisateur a échoué !",
    'USER_ACCOUNT_SUCCESSFULLY_CREATED'                 => "Le compte utilisateur a été créé avec succès.",
    'USER_ACCOUNT_SUSPENSION_ERROR_DAYS'                => "Les jours de suspension étaient invalides.",
    
    /* user invitation */
    'USER_INVITATION_NOT_ENABLED'               => "Le processus d'invitation n'est pas activé.",
    'USER_INVITATION_VALIDATION_SUCCESSFUL'     => "Veuillez définir votre nom d'utilisateur et votre mot de passe pour terminer votre inscription",
    'USER_INVITATION_EMAIL_SUBJECT'             => 'Invitation reçue de %s',
    'USER_INVITATION_EMAIL_CONTENT_TITLE'       => 'Bienvenue !',
    'USER_INVITATION_EMAIL_INTRO'               => "Vous recevez cet e-mail de %s. Un compte a été créé pour vous.",
    'USER_INVITATION_EMAIL_LINK_MESSAGE'        => "Vous devez cliquer sur le lien ci-dessous pour terminer votre inscription et activer votre compte. Il vous sera demandé de définir votre nom d'utilisateur et un mot de passe.",
    'USER_INVITATION_EMAIL_EXPIRE_NOTICE'       => "N'attendez pas ! Ce lien expirera le %s.",
    'USER_INVITATION_EMAIL_LINK_TITLE'          => "Terminer l'inscription",
    'USER_INVITATION_EMAIL_SENT_SUCCESSFULLY'   => "Un e-mail d'invitation a été envoyé avec succès. L'utilisateur devra compléter son inscription avant de se connecter.",
    "USER_INVITATION_PROCESS_COMPLETE"          => "Un compte a été créé avec succès (nom d'utilisateur temporaire : %s) et nous avons envoyé un email à %s. <br>Ce compte ne sera pas actif tant que l'utilisateur n'aura pas terminé son inscription." , 

);