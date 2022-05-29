<?php

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.21 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */

namespace Kristuff\Minikit\Core;

// todo doc
class SafeExit
{
    // todo doc
    public static function exit(int $code = 0)
    {
        //if (strtoupper(Environment::app()) !== 'TESTING_PHPUNIT') {
              exit($code); 
        //}
    }
}