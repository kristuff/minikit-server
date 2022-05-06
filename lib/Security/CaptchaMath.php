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

namespace Kristuff\Minikit\Security;

use Kristuff\Minikit\Http\Session;

/** 
 * Class Captcha
 *
 * This class handles the captcha stuff using a simple math question.
 */
class CaptchaMath 
{
    private $session;

    /** 
     * Constructor
     *
     * @access public
	 * @param  Http\Session     $session        The session instance
	 */
    public function __construct(Session $session)
    {
        $this->session  = $session;
    }
    
    /** 
     * Generates a captcha and new identifier and store captcha value it into session.
     *
     * @access public
     *
     * @return void 
	 */
    public function createAndOutput(string $identifier, $darkTheme = false)
    {
        $addNum1 = rand(0, 9);
		$addNum2 = rand(0, 9);

        // Create a canvas
        $captchaImg = @imagecreatetruecolor(90, 36);
        if ($captchaImg === false) {
            throw new \RuntimeException('Creation of true color image failed');
        }

        // Allocate black and white colors
        $colorBlack = imagecolorallocate($captchaImg, 0, 0, 0);
        $colorWhite = imagecolorallocate($captchaImg, 255, 255, 255);

        // Make the background of the image
        imagefilledrectangle($captchaImg, 0, 0, 90, 36, $darkTheme ? $colorBlack : $colorWhite);

        // Make the background transparent
        imagecolortransparent($captchaImg, $colorBlack);

        // Draw the math question on the image
        imagestring($captchaImg, 5, 2, 10,  ' ' . $addNum1 . ' + ' . $addNum2 . ' =', $darkTheme ? $colorWhite : $colorBlack);

        // write the captcha answer into session
        $this->session->set($identifier, $addNum1 + $addNum2);
   
        header('Content-Disposition: Attachment;filename=captcha.png');
        header('Content-Type: image/png');
        imagepng($captchaImg);
        imagedestroy($captchaImg);
    }

    /**
     * Validate Captcha
     * 
     * Checks if the entered captcha answer is correct.
     *
     * @access public
     * @param mixed         $value          The captcha answer
     * @param string        $identifer      The captcha identifier
     *
     * @return bool         True if the given value matchs the expected value, otherwise false.
     */
    public function validate($value, string $identifier)
    {
        $expected = $this->session->get($identifier); 

        return !empty($expected) && 
               !empty($value) && 
               (int) trim($value) === $expected;
    }
}
