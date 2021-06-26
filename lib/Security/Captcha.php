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
 * @version    0.9.8
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Security;

use Kristuff\Miniweb\Http\Session;
use Gregwar\Captcha\CaptchaBuilder;

/** 
 * Class Captcha
 *
 * This class handles all the captcha stuff.
 * Currently this uses the Captcha generator lib from https://github.com/Gregwar/Captcha
 */
class Captcha
{
    /** 
     * @access private
     * @var Gregwar\Captcha\CaptchaBuilder      $captcha    The CaptachBuilder instance
     */
    private $captcha = null;

    /** 
     * @access private
     * @var string              $identifier     The captcha identifier
     */
    private $identifier = '';

    /** 
     * @access private
     * @var int                 $witdh          The captcha image width. Default is 260.
     */
    private $width = 260;

    /** 
     * @access private
     * @var int                 $height         The captcha image height. Default is 80.
     */
    private $height = 80;

    /** 
     * Constructor
     *
     * @access public
	 * @param  Http\Session     $session        The session instance
     * @param  int              $witdh          (optional) The captcha image width. Default is 260.
     * @param  int              $height         (optional) The captcha image height. Default is 80.
	 */
    public function __construct(Session $session, int $width =260, int $height = 80)
    {
        $this->session  = $session;
        $this->width    = $width ?? 260;
        $this->height   = $height ?? 80;
    }

    /** 
     * Generates a captcha and new identifier and store captcha value it into session.
     *
     * @access public
	 * @param  int          $width          The captcha image width (pixels)
	 * @param  int          $height         The captcha image height (pixels)
     *
     * @return void 
	 */
    public function create($identifier = null)
    {
        // create new id
        $this->identifier = $identifier ? $identifier : uniqid('captcha');

		// create a captcha with the CaptchaBuilder lib
	    $this->captcha = new CaptchaBuilder();
		$this->captcha->build($this->width, $this->height);

		// write the captcha character into session
		$this->session->set($this->identifier, $this->captcha->getPhrase());
    }
    
    /**
     * Outputs the image
     *
     * @access public
     * @return void 
     */
    public function output()
    {
		header('Content-type: image/jpeg');
		$this->captcha->output();
    }

    /**
     * Saves the Captcha to a jpeg file
     *
     * @access public
     * @param  string       $filePath       The file path.
     * @param  int          $quality        (optional) The image quality. Default is 90.
     *
     * @return void
     */
    public function save($filePath, $quality = 90)
    {
		$this->captcha->save($filePath, $quality);
    }

    /**
     * Gets the HTML inline base64
     *
     * @access public
     * @return string
     */
    public function inline()
    {
        return $this->captcha->inline();
    }

    /**
     * Gets the HTML image tag completed with inline base64 image and identifier
     *
     * @access public
     * @return string
     */
    public function inlineHtml($tagId = 'captcha')
    {
        return sprintf('<img id="%s" src="%s" />', 
            $tagId, 
            $this->captcha->inline());
    }

    /**
     * Validate Captcha
     * 
     * Checks if the entered captcha is the same like the one from the rendered image 
     * which has been saved in session
     *
     * @access public
	 * @param mixed         $value          The captcha characters
	 * @param string        $identifer      The captcha identifier
     *
	 * @return bool         True if the given value matchs the expected value , otherwise false.
	 */
	public function validate($value, string $identifier)
	{
        $expected = $this->session->get($identifier); 

        return !empty($expected) && 
               !empty($value) && 
               $value === $expected;
	}

    /**
     * Gets/returns the Captcha identifier key
     * 
     * @access private
	 * @return string
	 */
	public function identifier()
	{
        return $this->identifier;
	}
}
