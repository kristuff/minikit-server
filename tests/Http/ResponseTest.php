<?php

require_once __DIR__.'/../../vendor/autoload.php';

use Kristuff\Miniweb\Http\Response;
use Kristuff\Miniweb\Http\Server;

class ResponseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function TODO__test404Header()
    {
        // for testing
        header_remove(); 

        Response::setError404();

        /**
         * use xdebug_get_headers() to retreive header as headers_list() wont 
         * return statut header (tested and confirmed in comment from 
         * http://www.php.net/headers_list)
         */
        $headers =  print_r(xdebug_get_headers(), TRUE);

        //$this->assertContains('HTTP/1.0 404 Not Found', $headers);
        //$this->assertEquals(404, http_response_code()); 
    }

    private function internalStatusCodeTest($statusCode, $expectedHeader, $isWrong = false)
    {
        // for testing
        header_remove(); 
        
        $this->assertEquals(!$isWrong, Response::setStatus($statusCode));

        /**
         * use xdebug_get_headers() to retreive header as headers_list() wont 
         * return statut header (tested and confirmed in comment from 
         * http://www.php.net/headers_list)
         */
        $headers =  print_r(xdebug_get_headers(), TRUE);

        if (!empty($expectedHeader)){
            $this->assertContains($expectedHeader, $headers);
        }

        // response code not testable like this
        // @see http://www.php.net/http-response-code
        // FALSE will be returned if response_code is not provided and it is not invoked in a web server
        // environment (such as from a CLI application). TRUE will be returned if response_code is provided 
        // and it is not invoked in a web server environment (but only when no previous response status has been set). 
        
        //if (!$isWrong){
        //    $this->assertEquals($statusCode, http_response_code()); 
        //} else {
        //    $this->assertNotEquals($statusCode, http_response_code()); 
        // }
    }

    /**
     * @runInSeparateProcess
     */
    public function testStatusCodeWrong()
    {
        $this->internalStatusCodeTest(999, '', true);
    }

    /**
     * @runInSeparateProcess
     */
    public function testStatusCode200()
    {
        $this->internalStatusCodeTest(200, '200 OK');
    }

    /**
     * @runInSeparateProcess
     */
    public function testStatusCode201()
    {
        $this->internalStatusCodeTest(201, '201 Created');
    }

    /**
     * @runInSeparateProcess
     */
    public function testStatusCode204()
    {
        $this->internalStatusCodeTest(204, '204 No Content');
    }

    /**
     * @runInSeparateProcess
     */
    public function testStatusCode205()
    {
        $this->internalStatusCodeTest(205, '205 Reset Content');
    }

    /**
     * @runInSeparateProcess
     */
    public function testStatusCode206()
    {
        $this->internalStatusCodeTest(206, '206 Partial Content');
    }

    //TODO...
}