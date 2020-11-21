<?php
namespace Kristuff\Miniweb\Tests\Mvc;

require_once __DIR__.'/../_data/mvc_app/model/DummySessionModel.php';
require_once __DIR__.'/../_data/mvc_app/model/DummyModel.php';

use Kristuff\Miniweb\Mvc\Application;
use Kristuff\Miniweb\Mvc\Controller;
use Kristuff\Miniweb\Mvc\TaskResponse;
use Kristuff\Miniweb\Http\Session;

class ModelTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testSessionInModel()
    {
        // for testing
        header_remove(); 
        $_GET['url'] = 'testSession/test';

        $app = new Application();
        $app->setConfig(['CONTROLLER_PATH' =>   __DIR__ . '/../_data/mvc_app/controller/']);

        $this->assertTrue($app->handleRequest());

        // dup object for tesing
        $session = new Session();
        $this->assertEquals('value_from_model', $session->get('data_from_model'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testConfigInModel()
    {
        $params = ['APP_NAME' => 'test_conf_model'];
        $app = new Application();
        $app->setConfig($params);

        \DummyModel::configTestMethod();

        // dup object for tesing
        $session = new Session();
        $this->assertEquals('test_conf_model', $session->get('data_from_model'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testLocaleInModel()
    {
        $app = new Application();
        $this->assertTrue($app->locales()->registerAutoloader(__DIR__ . '/../_data/locale', ['en-US', 'fr-FR'], 'app.locale.php'));
        $this->assertTrue($app->locales()->setDefault('fr-FR'));

        \DummyModel::localeTestMethod();

        // dup object for tesing
        $session = new Session();
        $this->assertEquals('Bonjour', $session->get('data_from_model'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testResponseInModel()
    {
        $app = new Application();

        \DummyModel::responseTestMethod();

        // dup object for tesing
        $session = new Session();
        $this->assertEquals(409,         $session->get('data_from_model')['code']);
        $this->assertFalse(              $session->get('data_from_model')['success']);
        $this->assertEquals('hello :)',  $session->get('data_from_model')['message']);
        $this->assertEquals('the_value', $session->get('data_from_model')['data']['the_key']);
   
        $this->assertEquals(200,          $session->get('data_from_model_2')['code']);
        $this->assertTrue(                $session->get('data_from_model_2')['success']);
        $this->assertEquals('hello2 :)',  $session->get('data_from_model_2')['message']);
        $this->assertEquals('the_value2', $session->get('data_from_model_2')['data']['the_key']);
   

        $response = TaskResponse::create(200, '', ['key' => 'value']);
        $response->assertEquals(2,2, 500, 'error!');
        $this->assertTrue($response->success());
        $this->assertEquals(200, $response->code());

        $response->assertTrue(2 === 2, 500, 'error!');
        $response->assertFalse(2 === 3, 500, 'error!');
        $response->assertEquals(2,3, 500, 'error!');
        $this->assertFalse($response->success());
        $this->assertEquals(500, $response->code());
        $this->assertEquals('error!', $response->errors()[0]['message']);

        $response->setData(['TheKey'=> 'TheValue']);
        $this->assertEquals('TheValue', $response->data()['TheKey']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testRequestInModel()
    {
         // for testing
        header_remove(); 
        $_GET['url'] = 'testRequest/test';
        $_GET['test_arg'] = 'value222';

        $app = new Application();
        $app->setConfig(['CONTROLLER_PATH' =>   __DIR__ . '/../_data/mvc_app/controller/']);

        $this->assertTrue($app->handleRequest());

        // dup object for tesing
        $session = new Session();

        $this->assertEquals('value222', $session->get('test_request_in_model'));
    }


    /**
     * @runInSeparateProcess
     */
    public function testCookieInModel()
    {
        // necessary for testing
        header_remove(); 
        $_GET['url'] = 'testCookie/test';

        $app = new Application();
        $app->setConfig([
            'CONTROLLER_PATH'   =>   __DIR__ . '/../_data/mvc_app/controller/',
            'COOKIE_PATH'       => '/testcookie/',
            'COOKIE_RUNTIME'    => 22222,
            'COOKIE_SECURE'     => TRUE,
            'COOKIE_HTTP'       => TRUE,
            'COOKIE_DOMAIN'     => 'mydomain.com'
        ]);

        $this->assertTrue($app->handleRequest());

        $header = print_r(xdebug_get_headers(), true);
        $this->assertStringContainsString('Set-Cookie: test=value;', $header);
        $this->assertStringContainsString('; Max-Age=22222;', $header); 
        $this->assertStringContainsString('; expires=', $header); // ...
        $this->assertStringContainsString('GMT', $header); // ...
        $this->assertStringContainsString('; path=/testcookie/', $header);
        $this->assertStringContainsString('; secure', $header);
        $this->assertStringContainsString('; domain=mydomain.com', $header);
        // php7 now set correctly HttpOnly (httponly before)
        $this->assertStringContainsString('; httponly', strtolower($header));
    }

}