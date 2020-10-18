<?php

namespace Kristuff\Miniweb\Tests\Http;

use Kristuff\Miniweb\Http\Redirect;

class RedirectTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @runInSeparateProcess
     * @requires PHPUnit = 6.5
     */
    public function testRedirectWithExit()
    {
        // add at least one factice test because the second should not be executed as this call exit()
        // (this is the test). We can then make other tests without exiting app, in order to test the header
        $factice = TRUE;
        $this->assertTrue($factice);

        // redirect
        Redirect::url('http://www.example.com/', false, true);

        /**
         * use xdebug_get_headers() to retreive header as headers_list() wont 
         * return statut header (tested and confirmed in comment from 
         * http://www.php.net/headers_list)
         */
        $headers =  print_r(xdebug_get_headers(), TRUE);

        // this test should failed, 
        // but won't be reported as exit() should be called by Redirect::url() 
        $this->assertEquals('DEBUG', $headers);
    }


    /**
     * @runInSeparateProcess
     */
    public function testRedirect()
    {
        // for testing
        header_remove(); 

        // redirect (do no exit for testing header)
        Redirect::url('http://www.example.com/', false);

        /**
         * use xdebug_get_headers() to retreive header as headers_list() wont 
         * return statut header (tested and confirmed in comment from 
         * http://www.php.net/headers_list)
         */
        $headers =  print_r(xdebug_get_headers(), TRUE);

        $this->assertContains('Location: http://www.example.com/', $headers);
        $this->assertEquals(302, http_response_code()); 
    }

    /**
     * @runInSeparateProcess
     */
    public function testRedirectPermament()
    {
        // for testing
        header_remove(); 

        // redirect (do no exit for testing header)
        Redirect::url('http://www.example.com/', true);

        /**
         * use xdebug_get_headers() to retreive header as headers_list() wont 
         * return statut header (tested and confirmed in comment from 
         * http://www.php.net/headers_list)
         */
        $headers =  print_r(xdebug_get_headers(), TRUE);

        $this->assertContains('Location: http://www.example.com/', $headers);
        $this->assertEquals(301, http_response_code()); 
    }

}