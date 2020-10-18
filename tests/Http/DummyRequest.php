<?php
require_once __DIR__.'/../../vendor/autoload.php';

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