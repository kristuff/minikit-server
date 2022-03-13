<?php

namespace Kristuff\Minikit\Tests\Http;

use Kristuff\Minikit\Http\Session;

class SessionTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @runInSeparateProcess
     */
    public function testSessionGetSet()
    {
        session_start();

        $session = new Session();

        $this->assertNull($session->get('test'));
        $session->set('test', 'value');
        $this->assertEquals('value', $session->get('test'));

    }

     /**
     * @runInSeparateProcess
     */
    public function testSessionAdd()
    {
        session_start();

        $session = new Session();
        $session->add('collection', 'value1');
        $session->add('collection', 'value2');
        $session->add('collection', 'value3');
        $this->assertEquals(3,count($session->get('collection')));
        $this->assertEquals('value3', $session->get('collection')[2]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSessionStartDestroyWithCookieOnClientOnly()
    {
        ini_set("session.use_cookies", '1');
        ini_set("session.use_only_cookies", '1');

        $session = new Session();


        $this->assertEquals('', $session->sessionId());
        $this->assertNull($session->get('test'));
        
        $session->init();
        $session->set('test', 'value');
        $this->assertEquals('value', $session->get('test'));
        $this->assertNotEquals('', $session->SessionId());

        $params = session_get_cookie_params();
        $this->assertEquals(0, $params['lifetime']);
        
        $session->destroy();
        $this->assertEquals('', $session->sessionId());
        $this->assertNull($session->get('test'));
    }
    
     /**
     * @runInSeparateProcess
     */
    public function testSessionStartDestroyWithCookie()
    {
        ini_set("session.use_cookies", '1');
        ini_set("session.use_only_cookies", '0');

        $session = new Session();

        $this->assertEquals('', $session->sessionId());
        $this->assertNull($session->get('test'));
        
        $session->init();
        $session->set('test', 'value');
        $this->assertEquals('value', $session->get('test'));

        $this->assertNotEquals('', $session->sessionId());

        // suppose the cookie is send by browser
        $_COOKIE[session_name()] = $session->sessionId();

        $params = session_get_cookie_params();
        $this->assertEquals(0, $params['lifetime']);
        
        $session->destroy();
        $this->assertEquals('', $session->sessionId());
        $this->assertNull($session->get('test'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testSessionCookieSessings()
    {
        $session = new Session();

        $session->init();

        $this->assertEquals(ini_get("session.use_cookies"), $session->isUsingCookie());
        $this->assertEquals(ini_get("session.use_only_cookies"), $session->isUsingCookieOnly());

        session_destroy();
        ini_set("session.use_cookies", '0');
        ini_set("session.use_only_cookies", '0');

        $this->assertFalse($session->isUsingCookie());
        $this->assertFalse($session->isUsingCookieOnly());

        ini_set("session.use_cookies", '1');
        ini_set("session.use_only_cookies", '1');

        $this->assertTrue($session->isUsingCookie());
        $this->assertTrue($session->isUsingCookieOnly());

    }
    
}