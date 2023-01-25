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
 * trait Server
 *
 * Abstracts $_SERVER superglobal access.
 *
 */
trait ServerTrait
{
    /**
     * Gets/returns the content of the $_SERVER super global, or the the fallback value 
     * if the key value is null.
     *
     * @access protected
     * @static
     * @param string        $key                The key
     * @param mixed         $fallbackValue      (optional) The fallback value. Default is null.
     *
     * @return mixed        The value of the key in $_SERVER if exists, otherwhise the fallback value
     */
    protected static function getServerValue(string $key, $fallbackValue = null)
    {
         return isset($_SERVER[$key]) ? $_SERVER[$key] : $fallbackValue;
    }
}