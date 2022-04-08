<?php declare(strict_types=1);

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.19 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */

namespace Kristuff\Minikit\Core;

use Kristuff\Minikit\Core\Path;

/**
 * Class Locale
 *
 */
class Locale
{

    /** 
     * The locales for localized app
     *
     * @access protected
     * @var array
     */
    protected $locales = [];

    /** 
     * The default locale for localized app
     *
     * @access protected
     * @var string
     */
    protected $defaultLocale = null;

    /** 
     * The available locales
     *
     * @access protected
     * @var array
     */
    protected $registeredLocales = [];

    /** 
     * The relative path of registered locales
     *
     * @access protected
     * @var array
     */
    protected $registeredPath = null;

    /** 
     * The locale file name
     *
     * @access protected
     * @var string
     */
    protected $registeredFileName = 'locale.php';
    
    /** 
     * Register an autoloader
     *
     * Create an autoloader for locales in given directory. Each available locale
     * must match to a sub folder in main directory (Be carrefull this is case 
     * sensitive). The specified filename must matchs to a locale file name in
     * each sub directory.
     *
     * Example:
     * suppose the following files structure (two locales availables)
     * /home/myapp/locales/en-US/locale.php
     * /home/myapp/locales/fr-FR/locale.php
     *
     *    <?php
     *    use Kristuff\Minikit\Core\Locale;
     *    $local = new Locale(); 
     *    $locale->registerAutoloader('/home/myapp/locales', ['en-US', 'fr-FR'], 'locale.php'); 
     *
     * @access public
     * @param string    $relativePath           The relative path where are stored locales
     * @param array     $localesAvailables      The array of availables locales
     * @param string    $fileName               (optional) The file name. default is 'locale.php'
     * 
     * @return bool     true if the given path exists and is readable, otherwise false
     */
    public function registerAutoLoader(string $relativePath, array $locales = [], string $fileName = 'locale.php'): bool
    {
        // path must exists, be readable and locales must contain at least one locale
        $relativePath = realpath($relativePath);
        if ($relativePath === false || !Path::isReadable($relativePath) || empty($locales)){
            return false;
        }

        // register locales and path
        $this->registeredPath = $relativePath . DIRECTORY_SEPARATOR;
        $this->registeredLocales = $locales; 
        $this->registeredFileName = $fileName;
        return true;
    }

    /** 
     * Set the default locale
     *
     * Specify the locale to use in case there is no locale parameter in get() method.
     * If no default locale is defined, then the first registered locale will be used.
     *
     * @access public
     * @param string    $name           The name of the locale
     * 
     * @return bool     true if the locale is available and has been set as default, otherwise false.
     */
    public function setDefault(string $name): bool
    {
        // locale must be registered
        if (!$this->isRegistered($name)){
            return false;
        }

        // set as default
        $this->defaultLocale = $name;
        return true;
    }

    /** 
     * Load a locale and set it as default
     *
     * @access public
     * @param string    $name           The name of the locale
     * @param array     $values         The key/values locales. 
     * 
     * @return void
     */
    public function load(string $name, array $values = []): void
    {
        $this->registeredLocales[] = $name; 
        $this->locales[$name] = $values; 
        $this->defaultLocale = $name;
    }

    /** 
     * Gets/Returns the locale availables
     *
     * @access public
     * @return array   The availables locales.
     */
    public function getAvailables(): array
    {
        return $this->registeredLocales;
    }

    /** 
     * Checks if a locale is registered
     *
     * @access public
     * @param  string   $locale      The locale name.
     * 
     * @return bool     true if the given locale is registered, otherwise false.
     */
    public function isRegistered(string $locale): bool
    {
        return in_array($locale, $this->getAvailables());
    }

    /** 
     * Checks if a locale is loaded
     *
     * @access public
     * @param string    $locale      The locale name.
     * @param string    $section     The application section (optional).
     * 
     * @return bool     true if the given locale is loaded, otherwise false.
     */
    public function isLoaded(string $locale, ?string $section = null): bool
    {
        // if section is set, check if the locale and section keys exist
        if (!empty($section)){
            return array_key_exists($locale, $this->locales) && array_key_exists($section, $this->locales[$locale]);
        }

        // otherwise, check if the locale key exists
        return array_key_exists($locale, $this->locales);
    }

    /** 
     * Gets/Returns the locale value for the given key.
     *
     * $locale parameter allow to select the desired locale.
     * if no locale
     *
     * @access public
     * @param string    $key            The key.
     * @param string    $locale         The locale to use (optional).
     * @access public
     * 
     * @return string|null   The key value if key exists, otherwise null.
     */
    public function get(string $key, ?string $locale = null): ?string
    {
        // get locale
        $locale = !empty($locale) ? $locale : $this->getDefaultLocale();
        
        // loaded?
        if ($this->checkForLoadingLocale($locale)) {
                            
            // for simple        
            return array_key_exists($key, $this->locales[$locale]) ? strval($this->locales[$locale][$key]) : null;
        }

        return null;
    }

    /** 
     * Gets/Returns the locale value for the given key.
     *
     * @access public
     * @param string    $key            The key.
     * @param string    $section        The section inside application.
     * @param string    $locale         The locale to use (optional). If locale is not speciefied, the default one is used.
     * @access public
     * 
     * @return string|null   The key value if key exists, otherwise null.
     */
    public function getFromSection(string $key, string $section, ?string $locale = null): ?string
    {
        // get locale
        $locale = !empty($locale) ? $locale : $this->getDefaultLocale();
        
        // loaded?
        if ($this->checkForLoadingLocaleSection($locale, $section)) {
            
            // return the key value (as string) if key exists    
            return array_key_exists($key, $this->locales[$locale][$section]) ? strval($this->locales[$locale][$section][$key]) : null;
        }

        return null;
    }

    /** 
     * Check if a locale is loaded, otherwise try to load it.
     *
     * @access public
     * @param string    $locale      The locale name.
     * @param string    $section     The application section (optional).
     *
     * @return bool     true if locale exists or has been successfuly loaded, otherwise false.
     */
    protected function checkForLoadingLocale(string $locale): bool
    {
        // nothing to do if locale already loaded
        if (array_key_exists($locale, $this->locales)) {
            return true;
        }

        // note: registeredPath already contains DIRECTORY_SEPARATOR
        $fullPath = $this->registeredPath. $locale . DIRECTORY_SEPARATOR . $this->registeredFileName;

        // load from file
        if (file_exists($fullPath)) {

            // extract content
            $content = require $fullPath;

            // make sure it is an array
            if (is_array($content)) {

                // define locale
                $this->locales[$locale] = $content;
                return true;
            }
        }

        // loading fail
        return false;   
    }

    /** 
     * Check if a locale is loaded, otherwise try to load it .
     *
     * @access public
     * @param string    $locale      The locale name.
     * @param string    $section     The application section (optional).
     *
     * @return bool     true if locale exists or has been successfuly loaded, otherwise false.
     */
    protected function checkForLoadingLocaleSection(string $locale, string $section): bool
    {
        // nothing to do if locale already loaded
        if ($this->isLoaded($locale, $section)) {
            return true;
        }

        // note: registeredPath already contains DIRECTORY_SEPARATOR
        $fullPath = $this->registeredPath. $locale . DIRECTORY_SEPARATOR . $section .'.'. $this->registeredFileName;

        // load from file
        if (file_exists($fullPath)) {

            // extract content
            $content = require $fullPath;

            // make sure it is an array
            if (is_array($content)) {

                // define locale in case it is not already loaded
                if (!array_key_exists($locale, $this->locales)){
                    $this->locales[$locale] = [];
                }

                // load its content
                $this->locales[$locale][$section] = $content;
                return true;
            }
        }

        // loading fail
        return false;   
    }

    /**
     * Gets/returns the default locale 
     *
     * Returns the default locale if defined, otherwise the first one. 
     *
     * @access protected
     * @static
     *
     * @return string|null    
     */
    protected function getDefaultLocale(): ?string
    {
        // get the default locale if defined
        if (isset($this->defaultLocale)) {
            return $this->defaultLocale;
        } 

        // otherwise the first one
        if (count($this->getAvailables()) > 0){
            return  $this->getAvailables()[0];
        }

        return null;
    }         
}