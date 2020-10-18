<?php

declare(strict_types=1);

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
 * @version    0.9.0
 * @copyright  2017-2020 Kristuff
 */

namespace Kristuff\Miniweb\Auth\Model;

use Kristuff\Miniweb\Auth\TextHelper;
use Kristuff\Miniweb\Data\Model\DatabaseModel;

/**
 * Class BaseModel 
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