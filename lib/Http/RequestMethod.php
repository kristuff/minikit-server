<?php declare(strict_types=1);

/** 
 *        _      _            _
 *  _ __ (_)_ _ (_)_ __ _____| |__
 * | '  \| | ' \| \ V  V / -_) '_ \
 * |_|_|_|_|_||_|_|\_/\_/\___|_.__/
 *
 * This file is part of Kristuff\MiniWeb.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @version    0.9.8
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Http;

/**
 * Class RequestMethod
 *
 * Simple abstraction for http requests method
 * Base class for Request 
 */
abstract class RequestMethod
{
    // The GET method requests a representation of the specified resource. Requests using GET 
    // should only retrieve data.
    const METHOD_GET = 'GET' ;    

    // The HEAD method asks for a response identical to that of a GET request, but without the 
    // response body.
    const METHOD_HEAD = 'HEAD';    

    // The POST method is used to submit an entity to the specified resource, often causing a 
    // change in state or side effects on the server.
    const METHOD_POST = 'POST';    

    // The PUT method replaces all current representations of the target resource with the 
    // request payload.
    const METHOD_PUT = 'PUT';     

    // The DELETE method deletes the specified resource.
    const METHOD_DELETE = 'DELETE';  

    // The CONNECT method establishes a tunnel to the server identified by the target resource.
    const METHOD_CONNECT = 'CONNECT'; 

    // The OPTIONS method is used to describe the communication options for the target resource.
    const METHOD_OPTIONS = 'OPTIONS'; 

    // The TRACE method performs a message loop-back test along the path to the target resource.
    const METHOD_TRACE = 'TRACE';   

    // The PATCH method is used to apply partial modifications to a resource. 
    const METHOD_PATCH = 'PATCH' ;    
}