<?php declare(strict_types=1);

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.22 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */

namespace Kristuff\Minikit\Auth\Model;

use Kristuff\Minikit\Auth\TextHelper;
use Kristuff\Minikit\Data\Model\DatabaseModel;

/**
 * Class BaseModel 
 * 
 * Base class for all models of this library
 * Extends the DatabaseModel class with custom text function
 */
abstract class BaseModel extends DatabaseModel
{
    /**
     * Gets/returns the locale value for the given key (localized apps)
     * Overides default method by looking for an overide text in the main locale file 
     * then look in the default texts dist with this library  
     * 
     * @access public
     * @static
     * @param string    $key        The key
     * @param string    $locale     The locale to use (the default locale is used if null). 
     *
     * @return string|null    The key value is the key exists, otherwise null.
     */
    public static function text(string $key, ?string $locale = null): ?string
    {
        return TextHelper::text($key, $locale);
    }         
}