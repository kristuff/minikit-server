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

use Kristuff\Minikit\Http;
use Kristuff\Minikit\Security;

/** 
 * class Factory
 */
class CaptchaFactory
{
    /**
     * @access private
     * @static
     * @var Security\CaptchaFactory $factory        The CaptchaFactory instance 
     */
    private static $factory;

    /**
     * @access private
     * @var Security\CaptchaMath        $captcha        The Captcha instance
     */
    private $captcha;

    /**
     * Gets or creates a global static Factory instance
     *
     * @access public
     * @static
     *
     * @return CaptchaFactory
     */
    public static function getFactory()
    {
        if (!self::$factory) {
            self::$factory = new CaptchaFactory();
        }
        return self::$factory;
    }

    /**
     * Get or create a Captcha instance
     *
     * @access public
     * @param Http\Session      $session        The Session instance
     * @param int               $witdh          The captcha image width
     * @param int               $height         The captcha image height
     * 
     * @return Security\CaptchaMath
     */
    public function getCaptcha(Http\Session $session) //, int $width, int $height)
    {
        if (!$this->captcha) {
            //TODO clean
            //$this->captcha = new Security\Captcha($session, $width, $height);
            $this->captcha = new Security\CaptchaMath($session);//, $width, $height);
        }
        return $this->captcha;
    }
}