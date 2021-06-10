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
 * @version    0.9.6
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Core;

/**
 * Class Config
 *
 */
class Config
{

    /**
     * @access protected
     * @var array       The key/value configuration parameters.
     */
    protected $parameters = [];

    /** 
     * Constructor
     *
     * @access public
     * 
     */
    public function __construct(array $config = [])
    {
        $this->parameters[] = $config;
    }

    /**
     * Sets a configuration. 
     * 
     * @access public
     * @param  array    $config         The key/value configuration parameters.
     * @param  string   $configName     (optional) The config name.
     *
     * @return void
     */
    public function set(array $config = [], string $configName = ''): void
    {
        $identifer = empty($configName) ? 0 : $configName;
        $this->parameters[$identifer] = $config;
    }

    /**
     * Completes or overwrites a configuration. 
     * 
     * Merge the default config with given configuration.
     * If a key already exists in current config, the value is overwritten.
     * Missing values in default configuartion are added
     * 
     * @access public
     * @param  array    $config         The key/value configuration parameters.
     * @param  string   $configName     (optional) The config name.
     * 
     * @return void
     */
    public function overwriteOrComplete(array $config = [], ?string $configName = null): void
    {
        $identifer = empty($configName) ? 0 : $configName;
        $this->parameters[$identifer] = array_replace($this->parameters[$identifer], $config);
    }

    /**
     * Gets/returns the value of a config variable. 
     * 
     * @access public
     * @param  string   $key            The config key.
     * @param  string   $configName     (optional) The config name.
     * 
     * @return mixed|null               Returns the configration value if the key exists, otherwise null.
     */
    public function get(string $key, ?string $configName = null)
    {
        // named config or first one
        $identifer = empty($configName) ? 0 : $configName;

        // return key value if key exists
        return array_key_exists($key, $this->parameters[$identifer]) ? $this->parameters[$identifer][$key] : null;
    }

}
