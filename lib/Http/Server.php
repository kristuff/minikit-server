<?php declare(strict_types=1);

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.23 
 * Copyright (c) 2017-2023 Christophe Buliard  
 */

namespace Kristuff\Minikit\Http;

/**
 * Class Server
 *
 * Abstracts $_SERVER superglobal access (see also Request class that implements request related properties).
 * 
 */
class Server
{
    use ServerTrait;

    /**
     * Gets/returns the host name
     *
     * @access public
     * @static
     *
     * @return string       The value of HTTP_HOST in $_SERVER if exists, otherwhise 'localhost'
     */
    public static function httpHost(): string
    {
        return self::getServerValue('HTTP_HOST', 'localhost');
    }

    /**
     * Indicates whether the script was queried through the HTTPS protocol. 
     *
     * @access public
     * @static
     *
     * @return bool        true if the script was queried through the HTTPS protocol, otherwhise false. 
     */
    public static function isHttps(): bool
    {
        $https = self::getServerValue('HTTPS');
        return !empty($https) && $https != 'off';
    }

    /**
     * Gets/returns the request method
     *
     * @access public
     * @static
     *
     * @return string       The value of REQUEST_METHOD in $_SERVER if exists, otherwhise 'GET'.
     */
    public static function requestMethod(): string
    {
        return self::getServerValue('REQUEST_METHOD', 'GET');
    }
      
    /**
     * Gets/returns the request uri.
     *
     * @access public
     * @static
     *
     * @return string|null  The value of REQUEST_URI in $_SERVER if exists, otherwhise null.
     */
    public static function requestUri(): ?string
    {
        return self::getServerValue('REQUEST_URI');
    }

    /**
     * Gets/returns the server protocol. 
     *
     * @access public
     * @static
     *
     * @return string       The value of SERVER_PROTOCOL in $_SERVER if exists, otherwhise 'HTTP/1.0'.
     */
    public static function serverProtocol(): string
    {
        return self::getServerValue('SERVER_PROTOCOL', 'HTTP/1.0') ;
    }

    /**
     * Gets/returns the script name. 
     *
     * @access public
     * @static
     *
     * @return string       The value of SCRIPT_NAME in $_SERVER.
     */
    public static function scriptName(): ?string
    {
        return self::getServerValue('SCRIPT_NAME');
    }

    /**
     * Gets/returns the server identification string, given in the headers when responding to requests.
     *
     * @access public
     * @static
     *
     * @return string       The server identification string.
     */
    public static function serverSofware(): ?string
    {
        return self::getServerValue('SERVER_SOFTWARE');
    }

    /**
     * Gets/returns The name of the server host under which the current script is executing. If the 
     * script is running on a virtual host, this will be the value defined for that virtual host. 
     *
     * @access public
     * @static
     *
     * @return string
     */
    public static function serverName(): ?string
    {
        return self::getServerValue('SERVER_NAME');
    }

    /**
     * Gets/returns The value given to the SERVER_ADMIN (for Apache) directive in the web server 
     * configuration file. If the script is running on a virtual host, this will be the value defined 
     * for that virtual host. 
     *
     * @access public
     * @static
     *
     * @return string
     */
    public static function serverAdmin(): ?string
    {
        return self::getServerValue('SERVER_ADMIN');
    }    

}