<?php
namespace Kristuff\Miniweb\Tests\Http;

use Kristuff\Miniweb\Http\Request;

class DummyRequest extends Request
{
 
    /** 
     * Constructor
     *
     * @access public
     */
    public function __construct($uri = NULL, $method = 'GET')
    {
        parent::__construct($uri);
        $this->method = $method;
        $this->uri = $uri;
    }   
}