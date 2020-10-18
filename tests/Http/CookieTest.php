<?php

require_once __DIR__.'/../../vendor/autoload.php';

use Kristuff\Miniweb\Http\Cookie;

class CookieTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @runInSeparateProcess
     */
    public function testCookieDefault()
    {
        // necessary for testing
        header_remove(); 

        $cookie = new Cookie();
        $this->assertTrue($cookie->set('test', 'value'));

        //$this->assertEquals('DEBUG', print_r(xdebug_get_headers(), true));
        // Array... [0] => Set-Cookie: test=value; expires=Sun, 30-Apr-2017 01:45:23 GMT; Max-Age=604800; path=/
        //$this->assertEquals('DEBUG', json_encode(xdebug_get_headers()));
        // ["Set-Cookie: test=value; expires=Sun, 30-Apr-2017 01:17:52 GMT; Max-Age=604800; path=\/"]

        $header = print_r(xdebug_get_headers(), true);
        $this->assertContains('Set-Cookie: test=value;', $header);
        $this->assertContains('; Max-Age=604800;', $header);
        $this->assertContains('; expires=', $header);
        $this->assertContains('GMT', $header);
        $this->assertContains('; path=/', $header);
    }

    /**
     * @runInSeparateProcess
     */
    public function testCookieCustomExpire()
    {
        // necessary for testing
        header_remove(); 

        $cookie = new Cookie();
        $this->assertTrue($cookie->set('test', 'value', '/', 222));

        $header = print_r(xdebug_get_headers(), true);
        $this->assertContains('Set-Cookie: test=value;', $header);
        $this->assertContains('; Max-Age=222;', $header);
        $this->assertContains('; expires=', $header);
        $this->assertContains('GMT', $header);
        $this->assertContains('path=/', $header);
    }

    /**
     * @runInSeparateProcess
     */
    public function testCookieCustomExpireInConstructor()
    {
        // necessary for testing
        header_remove(); 

        $cookie = new Cookie(FALSE , FALSE, '/', '', 2222);
        $this->assertTrue($cookie->set('test', 'value'));

        $header = print_r(xdebug_get_headers(), true);
        $this->assertContains('Set-Cookie: test=value;', $header);
        $this->assertContains('; Max-Age=2222;', $header);
        $this->assertContains('; expires=', $header);
        $this->assertContains('GMT', $header);
        $this->assertContains('; path=/', $header);
    }

    /**
     * @runInSeparateProcess
     */
    public function testCookieDomainInConstructor()
    {
        // necessary for testing
        header_remove(); 

        $cookie = new Cookie(FALSE , FALSE, '/', '.example.com');
        $this->assertTrue($cookie->set('test', 'value'));

        $header = print_r(xdebug_get_headers(), true);
        $this->assertContains('Set-Cookie: test=value;', $header);
        $this->assertContains('; path=/', $header);
        $this->assertContains('; domain=.example.com', $header);
    }

    /**
     * @runInSeparateProcess
     */
    public function testCookieSecureInConstructor()
    {
        // necessary for testing
        header_remove(); 

        $cookie = new Cookie(true);
        $this->assertTrue($cookie->set('test', 'value'));

        $header = print_r(xdebug_get_headers(), true);
        $this->assertContains('Set-Cookie: test=value;', $header);
        $this->assertContains('; secure', $header);
        $this->assertNotContains('; httponly', $header);
    }

      /**
     * @runInSeparateProcess
     */
    public function testCookieHttponlyInConstructor()
    {
        // necessary for testing
        header_remove(); 

        $cookie = new Cookie(false, true);
        $this->assertTrue($cookie->set('test', 'value'));

        $header = print_r(xdebug_get_headers(), true);
        $this->assertContains('Set-Cookie: test=value;', $header);
        $this->assertNotContains('; secure', $header);
        // php7 now set correctly HttpOnly (httponly before)
        $this->assertContains('; httponly', strtolower($header));
    }

    /**
     * @runInSeparateProcess
     */
    public function testCookiePathInConstructor()
    {
        // necessary for testing
        header_remove(); 

        $cookie = new Cookie(FALSE , FALSE, '/path/');
        $this->assertTrue($cookie->set('test', 'value'));

        $header = print_r(xdebug_get_headers(), true);
        $this->assertContains('Set-Cookie: test=value;', $header);
        $this->assertContains('; path=/path/', $header);
        $this->assertNotContains('; domain=', $header);
    }

     /**
     * @runInSeparateProcess
     */
    public function testCookiePath()
    {
        // necessary for testing
        header_remove(); 

        $cookie = new Cookie(FALSE , FALSE, '/');
        $this->assertTrue($cookie->set('test', 'value', '/path/'));

        $header = print_r(xdebug_get_headers(), true);
        $this->assertContains('Set-Cookie: test=value;', $header);
        $this->assertContains('; path=/path/', $header);
        $this->assertNotContains('; domain=', $header);
    }

    /**
     * @runInSeparateProcess
     */
    public function testCookieForSession()
    {
        // necessary for testing
        header_remove(); 

        $cookie = new Cookie();
        $this->assertTrue($cookie->setForSession('test2', 'value2'));

        $header = print_r(xdebug_get_headers(), true);
        $this->assertContains('Set-Cookie: test2=value2;', $header);
        $this->assertNotContains('; Max-Age=;', $header);
        $this->assertNotContains('; expires=', $header);
        $this->assertContains('; path=/', $header);
    }


    /**
     * @runInSeparateProcess
     */
    public function testCookieGet()
    {
        // necessary for testing
        header_remove(); 

        $cookie = new Cookie();

        $this->assertNull($cookie->get('test'));
        $this->assertNull($cookie->get('test2'));

        $_COOKIE['test'] = 'value';
        $_COOKIE['test2'] = 'value2';

        $this->assertEquals('value', $cookie->get('test'));
        $this->assertEquals('value2', $cookie->get('test2'));
    }

     /**
     * @runInSeparateProcess
     */
    public function testCookieDelete()
    {
        // necessary for testing
        header_remove(); 

        $cookie = new Cookie();

        $_COOKIE['test'] = 'value';
        $cookie->delete('test');

        $header = print_r(xdebug_get_headers(), true);
        $this->assertContains('Set-Cookie: test=deleted;', $header);
        $this->assertContains('; Max-Age=0;', $header);
    }
   
}
