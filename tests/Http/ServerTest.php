<?php

require_once __DIR__.'/../../vendor/autoload.php';

use Kristuff\Miniweb\Http\Server;

class ServerTest extends \PHPUnit\Framework\TestCase
{

    public function testServerHttpHostOnLocalhost()
    {
        $this->assertEquals('localhost', Server::httpHost());
    }

    public function testServerHttpHostWithFakeHost()
    {
        $_SERVER['HTTP_HOST'] = 'www.example.com';
        $this->assertEquals('www.example.com', Server::httpHost());
    }

    public function testServerisHttps()
    {
        $this->assertFalse(Server::isHttps());

        $_SERVER['HTTPS'] = '1';
        $this->assertTrue(Server::isHttps());

        $_SERVER['HTTPS'] = '';
        $this->assertFalse(Server::isHttps());

        //http://php.net/manual/en/reserved.variables.server.php
        $_SERVER['HTTPS'] = 'off';
        $this->assertFalse(Server::isHttps());

    }

    public function testServerRequestUri()
    {
        $this->assertNull(Server::requestUri());
        $_SERVER['REQUEST_URI'] = '/foo/bar/';
        $this->assertEquals('/foo/bar/', Server::requestUri());
    }

    public function testServerRequestMethod()
    {
        $this->assertEquals('GET', Server::requestMethod());
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertEquals('POST', Server::requestMethod());
    }
}