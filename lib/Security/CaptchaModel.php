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

namespace Kristuff\Miniweb\Security;

use Kristuff\Miniweb\Mvc\Model;
use Kristuff\Miniweb\Mvc\Application;
use Kristuff\Miniweb\Security\Captcha;
use Kristuff\Miniweb\Security\CaptchaFactory;

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
     * The Captcha is build on demand (created the first time function is called)
     *
     * @access public 
     * @static method
     *
     * @return Security\Captcha
     */
    public static function captcha()
    {
         return CaptchaFactory::getFactory()->getCaptcha( self::session(), 
                                                          self::config('CAPTCHA_WIDTH'), 
                                                          self::config('CAPTCHA_HEIGHT'));
    }

  
}