<?php
namespace Kristuff\Minikit\Tests\Http;

use Kristuff\Minikit\Http\Request;

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