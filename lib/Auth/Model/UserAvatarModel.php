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

namespace Kristuff\Minikit\Auth\Model;

use Kristuff\Minikit\Auth\Data\UsersCollection;
use Kristuff\Minikit\Auth\Model\UserEditModel;
use Kristuff\Minikit\Mvc\TaskResponse;
use Kristuff\Minikit\Mvc\Application;
use Kristuff\Minikit\Security\Token;

/** 
 * Class UserAvatarModel
 */
class UserAvatarModel extends UserEditModel
{
    /* the name of the input */
    const INPUT_AVATAR_FILE_NAME = 'user_avatar_file';

    /** 
     * Get the path of avatars collection 
     *
     * @access public
     * @static 
     * 
     * @return  string    The avatar path with ending /
     */
    public static function getPath()
    {
        return self::config('USER_AVATAR_PATH');
    }

    /**
     * Gets the user's avatar file path
     *
     * @access public
     * @static
     * @param int       $userHasAvatar  Marker from database
     * @param int       $UID        The avatar's string identifier
     *
     * @return string   Avatar file path
     */
    public static function getAvatarFilePath($userHasAvatar, $UID)
    {
        //TODO id...
        return $userHasAvatar ? self::getPath().$UID.'.jpg' : 
                                self::getPath().self::config('USER_AVATAR_DEFAULT_IMAGE');
    }

    /**
     * Gets the user's avatar URL
     * 
     * @access public
     * @static
     * @param bool      $userHasAvatar
     * @param string    $UID
     * @param string    $baseUrl
     * 
     * @return string   Avatar url
     */
    public static function getAvatarUrl($userHasAvatar, $UID, $baseUrl)
    {
        return $userHasAvatar ? $baseUrl.self::config('USER_AVATAR_URL').$UID.'.jpg' :
                                $baseUrl.self::config('USER_AVATAR_URL').self::config('USER_AVATAR_DEFAULT_IMAGE');
    }

    /**
     * Set avatar info in session
     *
     * @access public
     * @static
     * @param string   $UID
     * @param bool     $hasAvatar
     *
     * @return void
     */
    public static function setAvatarInSession($UID, $hasAvatar = false)
    {
        self::session()->set('userHasAvatar', $hasAvatar);
        self::session()->set('userAvatarUrl', self::getAvatarUrl($hasAvatar, $UID, Application::getUrl()));
    }

    /**
     * Create an avatar picture (and checks all necessary things too)
     * TODO decouple
     * TODO total rebuild
     */
    public static function createCurrentUserAvatar($token, $tokenKey)
    {
        // the return response
        $response = TaskResponse::create();
        $response->setData([
            'userHasAvatar' => self::session()->get('userHasAvatar'),
            'userAvatarUrl' => self::session()->get('userAvatarUrl'),
        ]);

        // validate token
        if (self::validateToken($response, $token, $tokenKey) && 
            self::validateAvatarFolder($response) &&
            self::validateImageFile($response)){
                
            // get current userId
            $currentUserId  = self::getCurrentUserId();
            $uid            = Token::getNewToken(16); // => 32 

            // create a jpg file in the avatar folder
            $targetFilePath = self::getAvatarFilePath(true, $uid);
            $size           = self::config('USER_AVATAR_SIZE');
            $imageResized   = self::resizeAvatarImage($_FILES[self::INPUT_AVATAR_FILE_NAME]['tmp_name'], $targetFilePath, $size, $size);

            if ($response->assertTrue($imageResized, 400, self::text('USER_AVATAR_UPLOAD_FAILED'))){
                    
                // write marker to database
                UsersCollection::updateAvatarStatus($currentUserId, $uid, true);

                // set avatar in session      
                self::setAvatarInSession($uid, true);
                
                // set new data and message in response   
                $response->setData([
                    'userHasAvatar' => true,
                    'userAvatarUrl' => self::session()->get('userAvatarUrl'),
                ]);
                
                $response->setMessage(self::text('USER_AVATAR_UPLOAD_SUCCESSFUL'));
            }
        }

        // return response
        return $response;
    }

    /**
     * Delete the current user's avatar
     *
     * @param string      $token       The token value
     * @param string      $tokenKey    The token key
     *
     * @return TaskResponse        
     */
    public static function deleteCurrentUserAvatar(string $token, string $tokenKey)
    {
        $response       = TaskResponse::create();
        $currentUserId  = self::getCurrentUserId();
        $currentUserUID = self::getCurrentUserIdentifier();
         
        // validate token and try to delete
        if (self::validateToken($response, $token, $tokenKey) && 
            self::deleteAvatar($response, $currentUserId, $currentUserUID)){
               
            // update session
            self::setAvatarInSession($currentUserUID, false);
            $response->setMessage(self::text('USER_AVATAR_DELETE_SUCCESSFUL'));
            $response->setData([
                'userAvatarUrl' =>  self::session()->get('userAvatarUrl')
            ]);
        }

        return $response;
    }

    /** 
     * Checks if the avatar folder exists and is writable
     *
     * @return bool|array     
     */
    protected static function validateAvatarFolder(TaskResponse $response)
    {
        // get avatar path
        $path = self::getPath();

        return $response->assertTrue(file_exists($path), 500, self::text('USER_AVATAR_ERROR_PATH_MISSING'))
            && $response->assertTrue(is_writable($path), 500, self::text('USER_AVATAR_ERROR_PATH_PERMISSIONS'));
    }

    /** 
     * Gets a gravatar image link from given email address
     *
     * Gravatar is the #1 (free) provider for email address based global avatar hosting.
     * The URL (or image) returns always a .jpg file ! For deeper info on the different parameter possibilities:
     * @see https://gravatar.com/site/implement/images/
     * @source https://gravatar.com/site/implement/images/php/
     *
     * This method will return something like http://www.gravatar.com/avatar/79e2e5b48aec07710c08d50?s=80&d=mm&r=g
     * Note: the url does NOT have something like ".jpg" ! It works without.
     *
     * Set the configs inside the application/config/ files.
     *
     * @access protected
     * @static
     * @param  string   $userEmail      The user email address
     *
     * @return string
     */
    protected static function getGravatarLink($userEmail)
    {
        return 'https://www.gravatar.com/avatar/' .
                md5(strtolower(trim($userEmail))) .
               '?s=' . self::config('USER_AVATAR_SIZE') . 
               '&d=' . self::config('GRAVATAR_DEFAULT_IMAGESET') . 
               '&r=' . self::config('GRAVATAR_RATING');
    }

    /**
     * Validates the post image
     * 
     * Only accepts gif, jpg, png types. Image must respect a min physical size in pixels size and a 
     * max size in bytes defined in application settings.
     *
     * @see https://php.net/manual/en/function.image-type-to-mime-type.php
     *
     * @return bool|array       True if the posted file is valid, otherwise an response array with error details.
     */
    protected static function validateImageFile(TaskResponse $response)
    {
        // file sets?
        if ($response->assertTrue(isset($_FILES[self::INPUT_AVATAR_FILE_NAME]), 400, self::text('USER_AVATAR_UPLOAD_NO_FILE'))){
            
            // input file not too big (>1MB)?
            if ($response->assertTrue($_FILES[self::INPUT_AVATAR_FILE_NAME]['size'] <= self::config('USER_AVATAR_UPLOAD_MAX_SIZE'), 400, self::text('USER_AVATAR_UPLOAD_ERROR_TOO_BIG'))){
                                            
                // get the image width, height and mime type
                $imageSize = getimagesize($_FILES[self::INPUT_AVATAR_FILE_NAME]['tmp_name']);

                // check if input file is too small, [0] is the width, [1] is the height
                $isTooSmall = $imageSize[0] < self::config('USER_AVATAR_SIZE') || 
                              $imageSize[1] < self::config('USER_AVATAR_SIZE');

                // check if file type is jpg, gif or png
                $isCorrectType = in_array($imageSize['mime'], array('image/jpeg', 'image/gif', 'image/png'));

                return $response->assertFalse($isTooSmall, 400, self::text('USER_AVATAR_UPLOAD_ERROR_TOO_SMALL'))
                    && $response->assertTrue($isCorrectType, 400, self::text('USER_AVATAR_UPLOAD_ERROR_WRONG_TYPE'));
            }
        }

        // something was wrong
        return false;
    }


    /**
     * Resize avatar image (while keeping aspect ratio and cropping it off in a clean way).
     * Only works with gif, jpg and png file types.
     *
     * @access protected
     * @static
     * @param  string   $srcImage       The location to the original raw image
     * @param  string   $destination    The location to save the new image
     * @param  int      $width          The desired width of the new image
     * @param  int      $height         The desired height of the new image
     *
     * @return bool     True if the avatar file has been successfully resized and save, otherwise false.
     */
    protected static function resizeAvatarImage($srcImage, $destination, $width = 90, $height = 90)
    {
        $imageData = getimagesize($srcImage);
        $tmpWidth  = $imageData[0];
        $tmpHeight = $imageData[1];
        $mimeType  = $imageData['mime'];

        if (!$tmpWidth || !$tmpHeight) {
            return false;
        }

        switch ($mimeType) {
            case 'image/jpeg': $myImage = imagecreatefromjpeg($srcImage); break;
            case 'image/png': $myImage = imagecreatefrompng($srcImage); break;
            case 'image/gif': $myImage = imagecreatefromgif($srcImage); break;
            default: return false;
        }

        // calculating the part of the image to use for thumbnail
        if ($tmpWidth > $tmpHeight) {
            $verticalCoordinateOfSource = 0;
            $horizontalCoordinateOfSource = ($tmpWidth - $tmpHeight) / 2;
            $smallestSide = $tmpHeight;
        } else {
            $horizontalCoordinateOfSource = 0;
            $verticalCoordinateOfSource = ($tmpHeight - $tmpWidth) / 2;
            $smallestSide = $tmpWidth;
        }

        // copying the part into thumbnail, maybe edit this for square avatars
        $thumb = imagecreatetruecolor($width, $height);
        imagecopyresampled($thumb, $myImage, 0, 0, $horizontalCoordinateOfSource, $verticalCoordinateOfSource, $width, $height, $smallestSide, $smallestSide);

        // add '.jpg' to file path, save it as a .jpg file with our $destination_filename parameter
        imagejpeg($thumb, $destination, self::config('USER_AVATAR_JPEG_QUALITY'));
        imagedestroy($thumb);

        return file_exists($destination);
    }

    /**
     * Delete a user's avatar
     *
     * @param int $userId
     * @return bool success
     */
    protected static function deleteAvatar(TaskResponse $response, int $userId, string $userIdentifer ): bool
    {
        // try to delete image, but still go on regardless of file deletion result
        $deleteFile           = self::deleteAvatarImageFile($response, $userId);
        $deleteDatabaseMarker = UsersCollection::updateAvatarStatus($userId, false);
            
        return $deleteFile && $deleteDatabaseMarker;
    }

    /**
     * Removes the avatar image file from the filesystem
     *
     * @access private
     * @static
     * @param string    $userUId
     *
     * @return bool     True if the avatar file has been deleted, otherwise False.
     */
    private static function deleteAvatarImageFile(TaskResponse $response, int $userIdentifier)
    {
        $path = self::getAvatarFilePath(true, $userIdentifier);

        // Check file exists first
        return $response->assertTrue(file_exists($path), 500, self::text('USER_AVATAR_DELETE_ERROR_NO_FILE'))
            
            // and has been now deleted 
            && $response->assertTrue(unlink($path), 500, self::text('USER_AVATAR_DELETE_FAILED'));
    }
}