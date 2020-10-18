<?php
require_once __DIR__.'/../../vendor/autoload.php';

use Kristuff\Miniweb\Http\Session;
use Kristuff\Miniweb\Security\Captcha;

class CaptchaTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testCaptchaValidation()
    {
        $null = null;
        $session = new Session();
        $session->init();

        $captcha = new Captcha($session);
        $captcha->create();
        $identifier = $captcha->identifier();
        $captchaPhrase = $session->get($identifier);
        
        $this->assertEquals(5, strlen($captchaPhrase)); 

        $this->assertFalse($captcha->validate($null, $identifier)); 
        $this->assertFalse($captcha->validate('', $identifier)); 
        $this->assertFalse($captcha->validate('XXXXX', $identifier)); 
        $this->assertFalse($captcha->validate(12345, $identifier)); 
        $this->assertTrue($captcha->validate($captchaPhrase, $identifier)); 
    }

    /**
     * @runInSeparateProcess
     */
    public function testCaptchaImage()
    {
        $session = new Session();
        $session->init();

        $captcha = new Captcha($session);
        $captcha->create();

        ob_start();
        $captcha->output();
        $image = ob_get_contents();
        ob_end_clean();

        $encodedImage   = 'data:image/jpeg;base64,'. base64_encode($image);
        $inline         = $captcha->inline();

        $this->assertContains('data:image/jpeg;base64,', $inline); 
        $this->assertEquals($encodedImage, $inline); 
    }

    /**
     * @runInSeparateProcess
     */
    public function testCaptchaSave()
    {
        $session = new Session();
        $session->init();

        $captcha = new Captcha($session);
        $captcha->create();
       
        $path = '/tmp/image.jpeg';
        $captcha->save($path);

        $this->assertTrue(file_exists($path)); 
    }


    /**
     * @runInSeparateProcess
     */
    public function testCaptchaInline()
    {
        $session = new Session();
        $session->init();

        $captcha = new Captcha($session);
        $captcha->create();

        $inline         = $captcha->inline();
        $inlineHtml     = $captcha->inlineHtml();

        $this->assertContains('data:image/jpeg;base64,', $inline); 
        $this->assertContains($inline, $inlineHtml); 
    }
}