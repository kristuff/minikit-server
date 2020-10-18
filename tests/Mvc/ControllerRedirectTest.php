<?php
require_once __DIR__.'/../../vendor/autoload.php';

use Kristuff\Miniweb\Mvc\Application;
use Kristuff\Miniweb\Mvc\Controller;

class ControllerRedirectTest extends \PHPUnit\Framework\TestCase
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
    
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRedirectInController() : void
    {
         // for testing
        header_remove(); 
        $_GET['url'] = 'testRedirect/testRedirectTo';

        $app = new Application();
        $app->setConfig([
                 'CONTROLLER_PATH'      =>   __DIR__ . '/../_data/mvc_app/controller/',
                 'VIEW_PATH'            =>   __DIR__ . '/../_data/mvc_app/view/'
            ]
        );
        $this->assertTrue($app->handleRequest());

        /**
         * use xdebug_get_headers() to retreive header as headers_list() wont 
         * return statut header (tested and confirmed in comment from 
         * http://www.php.net/headers_list)
         */
        $headers =  print_r(xdebug_get_headers(), TRUE);

        $this->assertContains('Location: http://www.example.com/', $headers);
        $this->assertEquals(301, http_response_code()); 
    }


     /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @requires PHPUnit = 6.5
     */
    public function testRedirectWithExitInController() : void
    {
        // at least one 'valide' test in method
        $this->assertTrue(true);


         // for testing
        header_remove(); 
        $_GET['url'] = 'testRedirect/testRedirectWithExit';

        $app = new Application();
        $app->setConfig([
                 'CONTROLLER_PATH'      =>   __DIR__ . '/../_data/mvc_app/controller/',
                 'VIEW_PATH'            =>   __DIR__ . '/../_data/mvc_app/view/'
            ]
        );
        


        // exit() should be called
        $app->handleRequest();

        // so that should pass...
        $this->assertTrue(false);
    }

    
    
 
  	/**
	 * Reset everything
	 */
	public function tearDown() : void
	{
		putenv('APPLICATION_ENV=' . $this->currentEnv);
	}
}