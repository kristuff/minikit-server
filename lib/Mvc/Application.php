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
 * @version    1.0.0
 * @copyright  2017-2020 Kristuff
 */

namespace Kristuff\Miniweb\Mvc;

use Kristuff\Miniweb\Http;
use Kristuff\Miniweb\Http\Server;
use Kristuff\Miniweb\Http\Session;
use Kristuff\Miniweb\Core\Config;
use Kristuff\Miniweb\Core\Locale;
use Kristuff\Miniweb\Mvc\Rooter;
use Kristuff\Miniweb\Mvc\Factory;

/**
 * Class Application
 *
 * The heart of the MVC application
 */
class Application
{

    /** 
     * @access private
     * @var \Kristuff\Miniweb\Core\Config     $conf               The Config instance
     */
    private static $config = null;

    /** 
     * @access private
     * @var \Kristuff\Miniweb\Core\Locale     $locale             The Locale instance
     */
    private static $locale = null;

    /** 
     * @access protected
     * @var \Kristuff\Miniweb\Mvc\Rooter      $rooter             The Rooter instance
     */
    protected $rooter;

    /** 
	 * Constructor
     *
     * @access protected
     */
    public function __construct()
    {
        self::$locale = new Locale();
        self::$config = new Config();
        
        // load default config
        $this->loadConfigFile(__DIR__ . '/../../config/miniweb-core.conf.php');
        $this->loadConfigFile(__DIR__ . '/../../config/miniweb-auth.conf.php');
        $this->loadConfigFile(__DIR__ . '/../../config/miniweb-data.conf.php');
        $this->loadConfigFile(__DIR__ . '/../../config/miniweb-captcha.conf.php');
        $this->loadConfigFile(__DIR__ . '/../../config/miniweb-mailer.conf.php');
        
        // create initialize a session
        $session = new Session();
        $session->init();

        // register the session
        Factory::getFactory()->setSession($session);

        // create rooter
        $this->rooter = new Rooter($this, $session);
    }
    
    /** 
	 * Gets the static Locale instance 
     *
     * @access public
     * @return \Kristuff\Miniweb\Core\Locale
     */
    public function locales(): \Kristuff\Miniweb\Core\Locale
    {
        return self::$locale;
    }

    /** 
	 * Gets the Rooter instance 
     *
     * @access public
     * @return \Kristuff\Miniweb\Mvc\Rooter
     */
    public function rooter(): \Kristuff\Miniweb\Mvc\Rooter
    {
        return $this->rooter;
    }
          
    /** 
	 * Sets a configuration
	 *
     * @access public
     * @param array     $parameters     The keys/values parameters
     * @param string    $configName     (optional) The name of the configuration
     *
     * @return void
     */
    public function setConfig(array $parameters, ?string $configName = null): void
    {
        self::$config->overwriteOrComplete($parameters, $configName);
    }

    /**
     * Gets/returns the configuration value for the given key
     *
     * @access public
     * @static
     * @param string    $key            The key
     * @param string    $configName     (optional) The name of the configuration
     *
     * @return mixed    The key value is the key exists, otherwise null.
     */
    public static function config(string $key, ?string $configName = null)
    {
        return isset(self::$config) ? self::$config->get($key, $configName) : null;
    }   

    /**
     * Gets/returns the locale value for the given key (localized apps)
     *
     * @access public
     * @static
     * @param string    $key        The key
     * @param string    $locale     (optional) The locale to use (the default is used if empty).
     *
     * @return string|null    The key value is the key exists, otherwise null.
     */
    public static function text(string $key, ?string $locale = null): ?string
    {
        return isset(self::$locale) ? self::$locale->get($key, $locale) : null;
    }  

    /**
     * Gets/returns the locale value for the given key (localized apps)
     *
     * @access public
     * @static
     * @param string    $key        The key
     * @param string    $section    The application section
     * @param string    $locale     (optional) The locale to use (the default is used if empty).
     *
     * @return string|null    The key value is the key exists, otherwise null.
     */
    public static function textSection(string $key, string $section, ?string $locale = null): ?string
    {
        return isset(self::$locale) ? self::$locale->getFromSection($key, $section, $locale) : null;
    }   
  
    /** 
	 * Handles the current request. 
     * 
     * @access public
     * @return mixed
	 */
    public function handleRequest()
    {
        if ($this->rooter->handleRequest()){
            return true;
        }

        // set 404 header
        Http\Response::setError404();

        $this->handleError404();
        return false;
    }

    /** 
	 * Handles the 404 not found
     *
     * @access public
     * @return mixed
	 */
    public function handleError404()
    {
        // TODO
        // for now handle error must be implemented in inherited app class
        // default view? with template ? json api???... 
        return false;
    }

    /** 
	 * Gets/returns the root url of this application  
     * 
     * @access public
     * @static
     *
     * @return string
	 */
    public static function getUrl(): string
    {
        return (Server::isHttps() ? 'https' : 'http') . '://' . 
                Server::httpHost() . dirname(Server::scriptName());
    }

    /** 
	 * helper to load conf file
     * 
     * @access protected
     * @param string        $path           The config file full path
     * 
     * @return void
	 */
    protected function loadConfigFile(string $path): void
    {
        if (file_exists($path)) {
            $conf = require($path); 
            $this->setConfig($conf);
        }
    }
}