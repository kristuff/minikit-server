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
 * @version    0.9.12
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Security;

use Kristuff\Miniweb\Http;
use Kristuff\Miniweb\Security;
use Kristuff\Miniweb\Mvc\Application;

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
     * @var Security\Captcha        $captcha        The Captcha instance
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
     * @return Security\Captcha
     */
    public function getCaptcha(Http\Session $session, int $width, int $height)
    {
        if (!$this->captcha) {
            $this->captcha = new Security\Captcha($session, $width, $height);
        }
        return $this->captcha;
    }
}