<?php

namespace Kristuff\Minikit\Tests\Mvc;

use Kristuff\Minikit\Mvc\Application;

class ApplicationControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testAppplicationLoadDefaultController()
    {
         // for testing
        header_remove(); 
        $_GET['url'] = '';

        $app = new Application();
        $app->setConfig(['CONTROLLER_PATH' =>   __DIR__ . '/../_data/mvc_app/controller/']);

        $this->assertTrue($app->handleRequest());
        $this->assertEquals('IndexController::index() in IndexController.php', $app->rooter()->controller()->index());
    }

    /**
     * @runInSeparateProcess
     */
    public function testAppplicationLoadController()
    {
         // for testing
        header_remove(); 

        $app = new Application();
        $app->setConfig(['CONTROLLER_PATH' =>   __DIR__ . '/../_data/mvc_app/controller/']);

        $_GET['url'] = '/index/index';
        $this->assertTrue($app->handleRequest());
        $this->assertEquals('IndexController::index() in IndexController.php', $app->rooter()->controller()->index());
        
        $_GET['url'] = 'bar/foo';
        $this->assertTrue($app->handleRequest());
        $this->assertEquals('BarController::foo() in BarController.php', $app->rooter()->controller()->foo());
  
        $_GET['url'] = 'not_existing_controller/foo';
        $this->assertFalse($app->handleRequest());

        $_GET['url'] = 'bar/not_existing_method';
        $this->assertFalse($app->handleRequest());
    }

     /**
     * @runInSeparateProcess
     */
    public function testAppplicationLoadControllerWithNamespace()
    {
         // for testing
        header_remove(); 

        $app = new Application();
        $app->setConfig([
                 'CONTROLLER_PATH' =>   __DIR__ . '/../_data/mvc_app_namespace/controller/',
                 'CONTROLLER_NAMESPACE' =>   'MyAwesomeApp\\'
            ]
        );

        $_GET['url'] = '/index/index';
        $this->assertTrue($app->handleRequest());
        $this->assertEquals('MyAwesomeApp\IndexController::index() in IndexController.php', $app->rooter()->controller()->index());


        $_GET['url'] = 'bar/foo';
        $this->assertTrue($app->handleRequest());
        $this->assertEquals('MyAwesomeApp\BarController::foo() in BarController.php', $app->rooter()->controller()->foo());
  
        $_GET['url'] = 'not_existing_controller/foo';
        $this->assertFalse($app->handleRequest());

        $_GET['url'] = 'bar/not_existing_method';
        $this->assertFalse($app->handleRequest());

    }

    /**
     * @runInSeparateProcess
     */
    public function testAppplicationLoadControllerWithoutExtension()
    {
         // for testing
        header_remove(); 
        $_GET['url'] = 'foo/bar';

        $app = new Application();
        $app->setConfig([
                 'CONTROLLER_PATH' =>   __DIR__ . '/../_data/mvc_app/controller/',
                 'CONTROLLER_EXTENSION' =>   ''
            ]
        );

        $this->assertEquals('',   Application::config('CONTROLLER_EXTENSION'));
        $this->assertTrue($app->handleRequest());
        $this->assertEquals('Foo::bar() in Foo.php', $app->rooter()->controller()->bar());
    }

    /**
     * @runInSeparateProcess
     */
    public function testAppplicationLoadControllerWithNamespaceWithoutExtension()
    {
         // for testing
        header_remove(); 
        $_GET['url'] = 'foo/bar';

        $app = new Application();
        $app->setConfig([
                 'CONTROLLER_PATH' =>   __DIR__ . '/../_data/mvc_app_namespace/controller/',
                 'CONTROLLER_EXTENSION' =>   '',
                 'CONTROLLER_NAMESPACE' =>   'MyAwesomeApp\\'
            ]
        );

        $this->assertEquals('',   Application::config('CONTROLLER_EXTENSION'));
        $this->assertTrue($app->handleRequest());
        $this->assertEquals('MyAwesomeApp\Foo::bar() in Foo.php', $app->rooter()->controller()->bar());
    }

}