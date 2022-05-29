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

namespace Kristuff\Minikit\Security;

use Kristuff\Minikit\Mvc\Model;
use Kristuff\Minikit\Security\CaptchaFactory;
use Kristuff\Minikit\Security\CaptchaMath;

/** 
 * class CaptchaModel 
 *
 * Handle global Captcha stuff. 
 */
class CaptchaModel extends Model
{
    /**
     * Gets/Returns the global Captcha instance
     *
     * The Captcha instance is created on demand (created the first time function is called)
     *
     * @access public 
     * @static
     *
     * @return CaptchaMath
     */
    public static function captcha()
    {
         return CaptchaFactory::getFactory()->getCaptcha(self::session());
    }
}