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

namespace Kristuff\Minikit\Mvc;

use Kristuff\Minikit\Http;
use Kristuff\Minikit\Http\Server;
use Kristuff\Minikit\Http\Session;
use Kristuff\Minikit\Core\Config;
use Kristuff\Minikit\Core\Locale;
use Kristuff\Minikit\Mvc\Rooter;
use Kristuff\Minikit\Mvc\Factory;

/**
 * Class Application
 *
 * The heart of the MVC application
 */
class Application
{

    /** 
     * @access private
     * @var \Kristuff\Minikit\Core\Config     $conf               The Config instance
     */
    private static $config = null;

    /** 
     * @access private
     * @var \Kristuff\Minikit\Core\Locale     $locale             The Locale instance
     */
    private static $locale = null;

    /** 
     * @access protected
     * @var \Kristuff\Minikit\Mvc\Rooter      $rooter             The Rooter instance
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
        $this->loadConfigFile(__DIR__ . '/../../config/minikit.conf.php');
        
        // Overwrite default config
        $this->loadLocalConfig();

        // create initialize a session
        $session = new Session();
        $session->init();

        // register the session
        Factory::getFactory()->setSession($session);

        // create rooter
        $this->rooter = new Rooter($this, $session);
    }

    /** 
	 * Overwrite default config
     * This method must be Overwriten in real app
     * 
     * @access public
     * @return void
     */
    public function loadLocalConfig(): void
    {        
    }

    /** 
	 * Gets the static Locale instance 
     *
     * @access public
     * @return \Kristuff\Minikit\Core\Locale
     */
    public function locales(): \Kristuff\Minikit\Core\Locale
    {
        return self::$locale;
    }

    /** 
	 * Gets the Rooter instance 
     *
     * @access public
     * @return \Kristuff\Minikit\Mvc\Rooter
     */
    public function rooter(): \Kristuff\Minikit\Mvc\Rooter
    {
        return $this->rooter;
    }
          
    /** 
	 * Sets or overwrites a configuration
	 *
     * Merge the existing config with given configuration.
     * If a key already exists in current config, the value is overwritten.
     * Missing values in current configuartion are added
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
	 * Helper to load a php configuration file.
     * File must return an indexed array 
     * 
     *      <?php
     *      return array( 
     *          'key'  => 'value',
     *          ...
     *      )
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

    /** 
	 * Helper to load INI configuration file.
     * File must be in INI format
     * 
     * @access protected
     * @param string        $path           The config file full path
     * @param bool          $withSection    Load or not INI sections. Default is false
     * 
     * @return void
     * @throws \RuntimeException                                
	 */
    protected function loadIniConfigFile(string $path, bool $withSection = false): void
    {
        if (file_exists($path) && is_file($path)){
            $conf = parse_ini_file($path, $withSection, INI_SCANNER_TYPED);
            if ($conf === false){
                throw new \RuntimeException('Unable to read configuration file ['.$path.'].');
            }
            $this->setConfig($conf);
        }
    }
}