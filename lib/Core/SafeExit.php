<?php

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
 * @version    0.9.10
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Core;

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