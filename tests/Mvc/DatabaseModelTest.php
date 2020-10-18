<?php

require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../_data/FooDatabaseModel.php';

use Kristuff\Miniweb\Mvc\Application;

class DatabaseModelTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @requires PHPUnit = 6.5
     */
    public function testFailureConnection() : void
    {
        $this->assertEquals(true, true);  // dummy OK test
        
        // no connection init exit wil be called
        FooDatabaseModel::tryToDoSomethingWithDatabase();

        $this->assertEquals(true, false); // dummy wrong test, (won't be reported as app should exit before)
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testConnectionMysql() : void
    {
        $params = [
            'DB_DRIVER'     => 'mysql', 
            'DB_HOST'       => 'localhost', 
            'DB_USER'       => 'root', 
            'DB_PASSWORD'   => '',
            'DB_NAME'       => 'patapouf'
        ];

        $app = new Application();
        $app->setConfig($params);


        $this->assertTrue(FooModel::tryCreateTable());

    }

    /**
     * @runInSeparateProcess
     */
    public function testConnectionPgsql() : void
    {
        $params = [
            'DB_DRIVER'     => 'pgsql', 
            'DB_HOST'       => 'localhost', 
            'DB_USER'       => 'postgres', 
            'DB_PASSWORD'   => '',
            'DB_NAME'       => 'patapouf'
        ];

        $app = new Application();
        $app->setConfig($params);
        
        $this->assertTrue(FooModel::tryCreateTable());

    }

    /**
     * @runInSeparateProcess
     */
    public function testConnectionSqlite() : void
    {
        $params = [
            'DB_DRIVER'     => 'sqlite', 
            'DB_NAME'       => ':memory:'
        ];

        $app = new Application();
        $app->setConfig($params);
        
        $this->assertTrue(FooModel::tryCreateTable());
    }

}