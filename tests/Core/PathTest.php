<?php

namespace Kristuff\Minikit\Tests\Core;

use Kristuff\Minikit\Core\Path;

class PathTest extends \PHPUnit\Framework\TestCase
{
    public function testPathExists()
    {
        $this->assertFalse(Path::exists('/home/tototototo'));
        $this->assertTrue(Path::exists('/home'));
    }

    public function testPathIsReadable()
    {

        $this->assertTrue(Path::exists(__DIR__));
        $this->assertTrue(Path::isReadable(__DIR__));
        $this->assertFalse(Path::isReadable('/home/totototo'));
        // TODO+ real existing non readable path
    }

    public function testPathIsWritable()
    {
        $this->assertTrue(Path::isWritable('/tmp'));
        $this->assertFalse(Path::isWritable('/root'));
        $this->assertFalse(Path::isWritable('/home/tototototo'));
    }


    public function testPathinfo()
    {
        $path = realpath(__DIR__ . '/../_data/model/FooModel.php');
        $this->assertEquals(Path::getExtension($path), 'php');
        $this->assertEquals(Path::getBaseName($path),  'FooModel.php');
        $this->assertEquals(Path::getFileName($path), 'FooModel');
        
        // don't know the real path so 
        //$dir = Path::getDirName($path);
        //$this->assertEquals($dir, '/home/scrutinizer/build/tests/_data/model');
    }

    public function testPathFileExists()
    {
        $path = realpath(__DIR__ . '/../_data/model/FooModel.php');
        $this->assertTrue(Path::fileExists($path));
        $this->assertFalse(Path::fileExists('/toto/foo/bar'));
    }

    public function testPathFileReadable()
    {
        $path = realpath(__DIR__ . '/../_data/model/FooModel.php');
        $this->assertTrue(Path::isfileReadable($path));
        $this->assertFalse(Path::isfileReadable('/toto/foo/bar'));
    }

    public function testPathFileWritable()
    {
        $path = realpath(__DIR__ . '/../_data/model/FooModel.php');
        $this->assertTrue(Path::isfileWritable($path));
        $this->assertFalse(Path::isfileWritable('/toto/foo/bar'));
    }

}