<?php

require_once __DIR__.'/../../vendor/autoload.php';

use Kristuff\Miniweb\Core\Config;
use Kristuff\Miniweb\Core\Locale;
use Kristuff\Miniweb\Core\Environment;
use Kristuff\Miniweb\Mvc\Application;

class ConfigTest extends \PHPUnit\Framework\TestCase
{

    public function testConfigConstructor()
    {
        $params = ['APP_NAME' => 'test', 'APP_VERSION' => '0.1.2'];
        $config = new Config($params);

        $this->assertEquals('test', $config->get('APP_NAME'));
        $this->assertEquals('0.1.2', $config->get('APP_VERSION'));
        $this->assertNull($config->get('NOT_EXISTING_KEY'));
    }

    public function testSetConfig()
    {
        $params = ['APP_NAME' => 'test', 'APP_VERSION' => '0.1.2'];
        $config = new Config();
        $config->set($params);
        
        $this->assertEquals('test', $config->get('APP_NAME'));
        $this->assertEquals('0.1.2', $config->get('APP_VERSION'));
        $this->assertNull($config->get('NOT_EXISTING_KEY'));
    }

    public function testSetNamedConfig()
    {
        $params = ['APP_NAME' => 'test', 'APP_VERSION' => '0.1.2'];
        $config = new Config();
        $config->set($params, 'toto');
        
        $this->assertEquals('test', $config->get('APP_NAME', 'toto'));
        $this->assertEquals('0.1.2', $config->get('APP_VERSION', 'toto'));
        $this->assertNull($config->get('NOT_EXISTING_KEY', 'toto'));
    }

    public function testConfigOverwriteOrComplete()
    {
        $params = ['APP_NAME' => 'test'];
        $config = new Config();
        $config->set($params);

        $newparams = ['APP_NAME' => 'test2', 'APP_CUSTOM' => 'value'];
        $config->OverwriteOrComplete($newparams);
        
        $this->assertEquals('test2', $config->get('APP_NAME'));
        $this->assertEquals('value', $config->get('APP_CUSTOM'));
    }

    public function testNamedConfigOverwriteOrComplete()
    {
        $params = ['APP_NAME' => 'test', 'APP_COPYRIGHT' => 'Kristuff', 'APP_VERSION' => '0.1.2'];
        $config = new Config();
        $config->set($params, 'toto');

        $newparams = ['APP_NAME' => 'test2', 'APP_CUSTOM' => 'value'];
        $config->OverwriteOrComplete($newparams, 'toto');
        
        $this->assertEquals('test2', $config->get('APP_NAME', 'toto'));
        $this->assertEquals('Kristuff', $config->get('APP_COPYRIGHT', 'toto'));
        $this->assertEquals('0.1.2', $config->get('APP_VERSION', 'toto'));
        $this->assertEquals('value', $config->get('APP_CUSTOM', 'toto'));
    }

}