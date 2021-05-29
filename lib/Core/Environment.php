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
 * @version    0.9.4
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Core;

/**
 * Class Environment
 *
 * Extremely simple way to get environment variables, everywhere inside your application.
 */
class Environment
{
    
    /**
     * The default value for application environment 
     * 
     * @access public
     * @var string    APP_ENV_DEFAULT is (currently) 'development'
     */
    const APP_ENV_DEFAULT = 'development';
    
    /**
     * Gets/returns the application environment 
     * 
     * Returns APPLICATION_ENV constant exists (set in Apache configs), otherwise
     * returns the constant APP_ENV_DEFAULT (currently) 'development'
     *
     * @access public
     * @static 
     
     * @return string   The content of APPLICATION_ENV if exists, otherwise the 
     *                  constant Environment::APP_ENV_DEFAULT.
     */
    public static function app(): string
    {
        return self::key('APPLICATION_ENV') ? self::key('APPLICATION_ENV') : self::APP_ENV_DEFAULT;
    }

    /**
     * Gets/returns the value of an environment variable. 
     * 
     * @access public
     * @static 
     * @param string   $varName                The name of the variable.
     
     * @return string|null      Returns the value of the environment variable is exists, otherwise Null.
     */
    public static function key($key): ?string
    {
        return getenv($key) ? getenv($key) : NULL;
    }
}
