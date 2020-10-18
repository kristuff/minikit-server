<?php
namespace Kristuff\Miniweb\Tests\Mvc;

use Kristuff\Miniweb\Core\Config;
use Kristuff\Miniweb\Core\Locale;
use Kristuff\Miniweb\Core\Environment;
use Kristuff\Miniweb\Mvc\Application;

class ApplicationConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testAppConfigBeforeInit()
    {
        $this->assertNull(Application::config('CONTROLLER_DEFAULT'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testAppConfigAfterInitDefault()
    {
        $app = new Application();
        $this->assertEquals('',      Application::config('APP_NAMESPACE'));

        // config: controller
        $this->assertEquals('index', Application::config('CONTROLLER_DEFAULT'));
        $this->assertEquals('index', Application::config('CONTROLLER_ACTION_DEFAULT'));
        $this->assertEquals('',      Application::config('CONTROLLER_NAMESPACE'));
        $this->assertEquals('',      Application::config('CONTROLLER_PATH'));
        $this->assertEquals('Controller', Application::config('CONTROLLER_EXTENSION'));
        $this->assertTrue(Application::config('CONTROLLER_UCWORDS'));  //TODO remove?

    }

     /**
     * @runInSeparateProcess
     */
    public function testAppConfigAfterInitOverwrite()
    {
        $params = ['APP_NAME' => 'test', 'CONTROLLER_DEFAULT' => 'mycontroller'];
        $app = new Application();
        $app->setConfig($params);

        $this->assertEquals('mycontroller', Application::config('CONTROLLER_DEFAULT'));
        $this->assertEquals('test',         Application::config('APP_NAME'));
        $this->assertEquals('Controller',   Application::config('CONTROLLER_EXTENSION'));
    }
}