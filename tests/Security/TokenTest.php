<?php
namespace Kristuff\Minikit\Tests\Security;

use Kristuff\Minikit\Http\Session;
use Kristuff\Minikit\Security\Token;

class TokenTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testAnctiCsrfGlobal()
    {
        $null = null;
        $session = new Session();
        $session->init();

        $antiCsrf = new Token($session);
        
        $token = $antiCsrf->value();
        $tokenAfter = $antiCsrf->value();

        // should be same as it has not expired
        $this->assertEquals($token,  $tokenAfter);

        $this->assertFalse($antiCsrf->isTokenValid($null));
        $this->assertFalse($antiCsrf->isTokenValid(''));
        $this->assertFalse($antiCsrf->isTokenValid('foo'));

        $this->assertTrue($antiCsrf->isTokenValid($token));
        $this->assertTrue($antiCsrf->isTokenValid($tokenAfter));
    }

    /**
     * @runInSeparateProcess
     */
    public function testAnctiCsrfWithKey()
    {
        $null = null;
        $session = new Session();
        $session->init();

        $antiCsrf = new Token($session);
        $token = $antiCsrf->value('myKey');

        // should be same
        $tokenAfter = $antiCsrf->value('myKey');
        $this->assertEquals($token, $tokenAfter);

        $this->assertFalse($antiCsrf->isTokenValid($null, 'myKey'));
        $this->assertFalse($antiCsrf->isTokenValid('', 'myKey'));
        $this->assertFalse($antiCsrf->isTokenValid('foo', 'myKey'));
        $this->assertTrue($antiCsrf->isTokenValid($token, 'myKey'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testTokenExpire()
    {
        $session = new Session();
        $session->init();

        $antiCsrf = new Token($session, 0);
        $tokenBefore = $antiCsrf->value();
        sleep(3);
        $tokenAfter = $antiCsrf->value();

        // should NOT be same as it should expire
        $this->assertNotEquals($tokenBefore, $tokenAfter);
    }

 }