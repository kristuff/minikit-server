<?php
namespace Kristuff\Miniweb\Tests\Mvc;

use Kristuff\Miniweb\Mvc\Application;
use Kristuff\Miniweb\Mvc\Controller;

class ViewTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @runInSeparateProcess
     */
    public function testViewIncludeFileInMainFolder()
    {
         // for testing
        header_remove(); 
        $_GET['url'] = 'foo/test_include_file_in_main_folder';

        $app = new Application();
        $app->setConfig([
                 'CONTROLLER_PATH'      =>   __DIR__ . '/../_data/mvc_app/controller/',
                 'CONTROLLER_EXTENSION' =>   '',
                 'VIEW_PATH'            =>   __DIR__ . '/../_data/mvc_app/view/'
            ]
        );

        $this->assertEquals('',   Application::config('CONTROLLER_EXTENSION'));
        $this->assertEquals( __DIR__ . '/../_data/mvc_app/view/',   Application::config('VIEW_PATH'));
        $this->assertTrue($app->handleRequest());
        $this->assertTrue( $app->rooter()->controller()->isIncluded);
        $this->assertEquals('I am a var in test_include.php', $app->rooter()->controller()->varInIncludedFile);
    }

    /**
     * @runInSeparateProcess
     */
    public function testViewIncludeFileInsubFolder()
    {
         // for testing
        header_remove(); 
        $_GET['url'] = 'foo/test_include_file_in_sub_folder';

        $app = new Application();
        $app->setConfig([
                 'CONTROLLER_PATH'      =>   __DIR__ . '/../_data/mvc_app/controller/',
                 'CONTROLLER_EXTENSION' =>   '',
                 'VIEW_PATH'            =>   __DIR__ . '/../_data/mvc_app/view/'
            ]
        );

        $this->assertEquals('',   Application::config('CONTROLLER_EXTENSION'));
        $this->assertEquals( __DIR__ . '/../_data/mvc_app/view/',   Application::config('VIEW_PATH'));
        $this->assertTrue($app->handleRequest());
        $this->assertTrue( $app->rooter()->controller()->isIncluded);
        $this->assertEquals('I am a var in foo/test_include.php', $app->rooter()->controller()->varInIncludedFile);
    }


     /**
     * @runInSeparateProcess
     */
    public function testViewIncludeMulti()
    {
         // for testing
        header_remove(); 
        $_GET['url'] = 'foo/test_include_multi_files';

        $app = new Application();
        $app->setConfig([
                 'CONTROLLER_PATH'      =>   __DIR__ . '/../_data/mvc_app/controller/',
                 'CONTROLLER_EXTENSION' =>   '',
                 'VIEW_PATH'            =>   __DIR__ . '/../_data/mvc_app/view/'
            ]
        );

        $this->assertEquals('',   Application::config('CONTROLLER_EXTENSION'));
        $this->assertEquals( __DIR__ . '/../_data/mvc_app/view/',   Application::config('VIEW_PATH'));
        $this->assertTrue($app->handleRequest());
        $this->assertTrue( $app->rooter()->controller()->isIncluded);
        $this->assertEquals('I am a var in test_include.php', $app->rooter()->controller()->varInIncludedFile[0]);
        $this->assertEquals('I am a var in foo/test_include.php', $app->rooter()->controller()->varInIncludedFile[1]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testViewIncludeNotFound()
    {
         // for testing
        header_remove(); 
        $_GET['url'] = 'foo/test_include_file_that_does_not_exists';

        $app = new Application();
        $app->setConfig([
                 'CONTROLLER_PATH'      =>   __DIR__ . '/../_data/mvc_app/controller/',
                 'CONTROLLER_EXTENSION' =>   '',
                 'VIEW_PATH'            =>   __DIR__ . '/../_data/mvc_app/view/'
            ]
        );

        $this->assertEquals('',   Application::config('CONTROLLER_EXTENSION'));
        $this->assertEquals( __DIR__ . '/../_data/mvc_app/view/',   Application::config('VIEW_PATH'));
        $this->assertTrue($app->handleRequest());
        $this->assertFalse($app->rooter()->controller()->isIncluded);
    }


    /**
     * @runInSeparateProcess
     */
    public function testViewIncludeWithData()
    {
         // for testing
        header_remove(); 
        $_GET['url'] = 'testData/test_include_file_with_data';

        $app = new Application();
        $app->setConfig([
                 'CONTROLLER_PATH'      =>   __DIR__ . '/../_data/mvc_app/controller/',
                 'VIEW_PATH'            =>   __DIR__ . '/../_data/mvc_app/view/'
            ]
        );

        $this->assertEquals( __DIR__ . '/../_data/mvc_app/view/',   Application::config('VIEW_PATH'));
        $this->assertTrue($app->handleRequest());
        $this->assertTrue($app->rooter()->controller()->isIncluded);
        $this->assertEquals('I am the data from TestDataController', $app->rooter()->controller()->varInIncludedFile);
    }


    /**
     * @runInSeparateProcess
     */
    public function testViewLocale_default()
    {
         // for testing
        header_remove(); 
        $_GET['url'] = 'testData/test_locale_in_view_default';


        $app = new Application();
        $this->assertTrue($app->locales()->registerAutoloader(__DIR__ . '/../_data/locale', ['en-US', 'fr-FR'], 'app.locale.php'));
        $this->assertTrue($app->locales()->setDefault('en-US'));
        $app->setConfig([
                 'CONTROLLER_PATH'      =>   __DIR__ . '/../_data/mvc_app/controller/',
                 'VIEW_PATH'            =>   __DIR__ . '/../_data/mvc_app/view/'
            ]
        );

        $this->assertTrue($app->handleRequest());
        $this->assertTrue($app->rooter()->controller()->isIncluded);
        $this->assertEquals('Hello', $app->rooter()->controller()->varInIncludedFile);
    }

    
    /**
     * @runInSeparateProcess
     */
    public function testViewLocale_explicit()
    {
         // for testing
        header_remove(); 
        $_GET['url'] = 'testData/test_locale_in_view_explicit';


        $app = new Application();
        $this->assertTrue($app->locales()->registerAutoloader(__DIR__ . '/../_data/locale', ['en-US', 'fr-FR'], 'app.locale.php'));
        $this->assertTrue($app->locales()->setDefault('en-US'));
        $app->setConfig([
                 'CONTROLLER_PATH'      =>   __DIR__ . '/../_data/mvc_app/controller/',
                 'VIEW_PATH'            =>   __DIR__ . '/../_data/mvc_app/view/'
            ]
        );

        $this->assertTrue($app->handleRequest());
        $this->assertTrue($app->rooter()->controller()->isIncluded);
        $this->assertEquals('Bonjour', $app->rooter()->controller()->varInIncludedFile);
    }

    /**
     * @runInSeparateProcess
     */
    public function testViewJson()
    {
        // for testing
        // header_remove();     sould be called by app here
        $_GET['url'] = 'testJson/api';

        $app = new Application();
        $app->setConfig([
                 'CONTROLLER_PATH'      =>   __DIR__ . '/../_data/mvc_app/controller/',
                 'VIEW_PATH'            =>   __DIR__ . '/../_data/mvc_app/view/'
            ]
        );

        $this->assertTrue($app->handleRequest());

        /**
         * use \xdebug_get_headers() to retreive header as headers_list() wont 
         * return statut header (tested and confirmed in comment from 
         * http://www.php.net/headers_list)
         */
        $headers =  print_r(\xdebug_get_headers(), TRUE);

        $this->assertStringContainsString('Content-Type: application/json', $headers);
        $this->assertEquals(201, http_response_code()); 
    }
}