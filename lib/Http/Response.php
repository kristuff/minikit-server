<?php declare(strict_types=1);

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.18 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */

namespace Kristuff\Minikit\Http;

use Kristuff\Minikit\Http\Server;

/**
 * Class Response
 *
 */
class Response
{
    /**
     * The Http code for: OK (200)
     * 
     * @access public
     * @var int 
     */
    const HTTP_CODE_OK = 200;

    /**
     * The Http code for: Created (201)
     * 
     * @access public
     * @var int 
     */
    const HTTP_CODE_CREATED = 201;

    /**
     * The Http code for: Accepted (202)
     * 
     * @access public
     * @var int 
     */
    const HTTP_CODE_ACCEPTED = 202;

    /**
     * The Http code for: No content (204)
     * 
     * @access public
     * @var int 
     */
    const HTTP_CODE_NO_CONTENT = 204;

    /**
     * The Http code for: Reset content (205)
     * 
     * @access public
     * @var int 
     */
    const HTTP_CODE_RESET_CONTENT = 205;

    /**
     * The Http code for: Partial content (206)
     * 
     * @access public
     * @var int 
     */
    const HTTP_CODE_PARTIAL_CONTENT = 206;

    /**
     * The Http code for: Permanent redirect (302)
     * 
     * @access public
     * @var int 
     */
    const HTTP_CODE_MOVED_PERMANENTLY = 301;

    /**
     * The Http code for: Redirect (302)
     * 
     * @access public
     * @var int 
     */
    const HTTP_CODE_MOVED_TEMPORARILY = 302;
   
    /**
     * The Http code for: Bad request (400)
     * 
     * @access public
     * @var int 
     */
    const HTTP_CODE_BAD_REQUEST = 400;

    /**
     * The Http code for: Unauthorized (401)
     * 
     * @access public
     * @var int 
     */
    const HTTP_CODE_UNAUTHORIZED = 401;

    /**
     * The Http code for: Forbidden (403)
     * 
     * @access public
     * @var int 
     */
    const HTTP_CODE_FORBIDDEN = 403;

    /**
     * The Http code for: Not found (404)
     * 
     * @access public
     * @var int 
     */
    const HTTP_CODE_NOT_FOUND = 404;

    /**
     * The Http code for: Method Not Allowed (405)
     * 
     * @access public
     * @var int 
     */
    const HTTP_CODE_NOT_ALLOWED = 405;

    /**
     * The Http code for: Conflict (409)
     * 
     * @access public
     * @var int 
     */
    const HTTP_CODE_CONFLICT = 409;

    /**
     * The Http code for: Gone (410)
     * 
     * @access public
     * @var int 
     */
    const HTTP_CODE_GONE = 410;

    /**
     * The Http code for: Unprocessable entity  (422)
     * 
     * @access public
     * @var int 
     */
    const HTTP_CODE_UNPROCESSABLE_ENTITY = 422;

    /**
     * The Http code for: Internal Server Error (500)
     * 
     * @access public
     * @var int 
     */
    const HTTP_CODE_INTERNAL_SERVER_ERROR = 500;

    /**
     * @access protected
     * @var array       $status     The array of available statuts code 
     */
    protected static $status = [];

    /** 
     * Defines the HTTP 404 header and status code
     *
     * @access public
     * @static
     *
     * @return void
     */
    public static function setError404(): void
    {
        header(Server::serverProtocol().' 404 Not Found', true, 404);
    }

    /** 
     * Sets the HTTP header for given http status code
     *
     * @access public
     * @static
     * @param int       $statusCode     The Http status code 
     *
     * @return bool     true if the status code is valide and header has been set, otherwise false.
     */
    public static function setStatus(int $statusCode): bool
    {
        // get status for given code
        $status = self::getStatus($statusCode);
        
        if (isset($status)){

             // set the header response code                                                        
            http_response_code($statusCode);                                             
            
            // set header
            header('Status: '.$status);
            return true;
        }
               
        // code not found
        return false;
    }

    /** 
     * Sets the HTTP header status for given http status code
     *
     * @access public
     * @static
     * @param int       $statusCode     The Http status code 
     *
     * @return string|null     The header status if status code is found, otherwise null
     */
    private static function getStatus(int $statusCode): ?string
    {
        // create status array if needed
        if (empty(self::$status)){

            self::$status = [

                /**
                 * Success
                 */
                self::HTTP_CODE_OK              => '200 OK',
                self::HTTP_CODE_CREATED         => '201 Created',
                self::HTTP_CODE_ACCEPTED        => '202 Accepted',
                //?203 => '203 Non-Authoritative Information',
                self::HTTP_CODE_NO_CONTENT      => '204 No Content',
                self::HTTP_CODE_RESET_CONTENT   => '205 Reset Content',
                self::HTTP_CODE_PARTIAL_CONTENT => '206 Partial Content',

                // 3?

                /**
                 * Web client Errors 
                 */
                self::HTTP_CODE_BAD_REQUEST     => '400 Bad Request',
                self::HTTP_CODE_UNAUTHORIZED    => '401 Unauthorized',
                self::HTTP_CODE_FORBIDDEN       => '403 Forbidden',
                self::HTTP_CODE_NOT_FOUND       => '404 Not Found',
                self::HTTP_CODE_NOT_ALLOWED     => '405 Method Not Allowed',

                self::HTTP_CODE_CONFLICT        => '409 Conflict',
                self::HTTP_CODE_GONE            => '410 Gone',

                // no status code in header here
                self::HTTP_CODE_UNPROCESSABLE_ENTITY    => 'Unprocessable Entity',
               
                /**
                 * Server Errors 
                 */
                self::HTTP_CODE_INTERNAL_SERVER_ERROR   => '500 Internal Server Error'

                // TODO ...
            ];
        }

        // return status string if exists 
        return array_key_exists($statusCode, self::$status) ? self::$status[$statusCode] : null;
    }
}