<?php

require_once __DIR__.'/../../vendor/autoload.php';

use Kristuff\Miniweb\Core\Environment;

class EnvironmentTest extends \PHPUnit\Framework\TestCase
{
    private $currentEnv = '';

    /*
	 * Save and reset current env for testing
	 */
	public function setUp() : void
	{
        $this->currentEnv =  getenv('APPLICATION_ENV');
		putenv('APPLICATION_ENV=');
	}

	public function testGetDefault()
	{
		$this->assertEquals('development', Environment::app());
		$this->assertEquals('development', Environment::APP_ENV_DEFAULT);
	}

	public function testGetProduction()
	{
		putenv('APPLICATION_ENV=production');
		$this->assertEquals('production', Environment::app());
	}

    public function testGetDevelopment()
	{
		putenv('APPLICATION_ENV=development');
		$this->assertEquals('development', Environment::app());
	}

	/**
	 * Reset everything
	 */
	public function tearDown() : void
	{
		putenv('APPLICATION_ENV=' . $this->currentEnv);
	}


}
