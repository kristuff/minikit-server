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

use Kristuff\Minikit\Mvc\Application;
use Kristuff\Minikit\Http\Response;

/** 
 * Class View
 *
 * Handles output and provides some methods to the view
 */
class View
{
    public $title = ''; 
    public $description = ''; 
    public $keywords = ''; 
    protected $baseUrl = '';
    protected $controlerName = '';
    protected $scripts = [];
    protected $styles = [];

    /**
     * @access private
     * @var array       $data       The data to pass to the view
     */
    private $data = [];

    /** 
     *
     */
    public function __construct()
    {
       $this->title =   Application::config('APP_NAME'); 
       $this->baseUrl = Application::getUrl();
    }

    /**
     * Add a public script file (js) to the collection
     *
     * @access protected
     * @param  string       $path
     * 
     * @return void
     */
    public function addScriptFile(string $path): void
    {
        $this->scripts[] = $path;
    }

    /**
     * Add a public style file (css) to the collection
     *
     * @access protected
     * @param  string       $path
     * 
     * @return void
     */
    public function addStyleFile(string $path): void
    {
        $this->styles[] = $path;
    }

    /**
     * Include a file to be rendered
     *
     * @access protected
     * @param  string       $filename       Relative path to the view to include, usually folder/file with extension
     *
     * @return bool         true if the file exists and has been included, otherwise false
     */
    protected function includeFile(string $filename): bool
    {
        $fullPath = Application::config('VIEW_PATH') . $filename ;
        if (!file_exists($fullPath)){
            return false;
        }

        // include file
        require $fullPath;
        return true;
    }

    /**
     * Read and return the content of a file
     *
     * @access protected
     * @param string        $filename       Relative path to the view to include, usually folder/file with extension
     * @param bool          $addNewLine     Add a trailine new line. Default is true
     * @param bool          $strictCheck    If false, return empty string instead of false on error. Default is false
     *  
     * @return string|bool  False if the file does not exist and strictCheck is true, otherwise the content of the file or
     *                      empty string
     */
    public function getFileContent(string $fileName, bool $addNewLine = true, bool $strictCheck = false)
    {
        $content = file_get_contents(Application::config('VIEW_PATH') . $fileName);
        if ($content === false && $strictCheck === true) {
            return false;
        }

        return $content . $addNewLine ? PHP_EOL : '';
    }

    /**
     * Converts characters to HTML entities
     * This is important to avoid XSS attacks, and attempts to inject malicious code in your page.
     *
     * @access public
     * @param string    $str    The string.
     * 
     * @return string
     */
    public function noHtml(string $str): string
    {
        return htmlentities($str, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Gets/returns the data value for the given key
     *
     * @access public
     * @param string    $key        The key
     *
     * @return mixed    The key value is the key exists, otherwise null.
     */
    public function data(string $key)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : null;
    }   
        
    /**
     * Set the data value for the given key
     *
     * @access public
     * @param string    $key        The key
     * @param mixed     $value      The key value
     *
     * @return void
     */
    public function setData(string $key, $value): void
    {
        $this->data[$key] = $value;
    }   
    
    /**
     * Add data 
     *
     * @access public
     * @param array     $data       The key/value array
     *
     * @return void
     */
    public function addData(array $data): void
    {
        $this->data = array_replace($this->data, $data);
    }

    /**
     * Gets/returns the locale value for the given key (localized apps)
     *
     * @access public
     * @param string    $key        The key
     * @param string    $locale     (optional) The locale to use. Default is null so the default one is used.
     *
     * @return mixed    The key value is the key exists, otherwise null.
     */
    public function text(string $key, ?string $locale = null): ?string
    {
        return Application::text($key, $locale);
    }   

    /**
     * Gets/returns the locale value for the given key in given section (localized apps)
     *
     * @access public
     * @param string    $key        The key
     * @param string    $section    The application section
     * @param string    $locale     The locale to use (the default is used if null). (optional)
     *
     * @return mixed    The key value is the key exists, otherwise null.
     */
    public function textSection(string $key, string $section, ?string $locale = null): ?string
    {
        return Application::textSection($key, $section, $locale);
    }         
   
    /**
     * echo a value from locale (->text) 
     *
     * @access public
     * @param string    $key        The key
     * @param string    $locale     The locale to use (the default is used if null). (optional)
     *
     * @return void
     */
    public function echo(string $key, ?string $locale = null): void
    {
        echo $this->text($key, $locale);
    }

    /**
     * echo a value from locale (->text) with trailish field ending 
     *
     * @access public
     * @param string    $key        The key
     * @param string    $locale     The locale to use (the default is used if null). (optional)
     *
     * @return void
     */
    public function echoField(string $key, ?string $locale = null): void
    {
        echo $this->text($key, $locale). $this->text('FIELD_ENDING', $locale);
    }   

    /**
     * Include file(s) to be rendered
     *
     * @access public
     * @param array|string  $files          Path of view(s) to include, usually folder(s)/file(s)
     * @param array         $data           (optional) The data to be passed to the view. Default is an empty array.
     * @param string        $template       (optional) The template to use.
     *
     * @return bool         true if the file(s) exist(s) and has/have been included, otherwise false
     */
    public function renderHtml($files, array $data = [], $template = ''): bool
    {
        $return = true;

        // pass data to the view
        if (!empty($data)){
            $this->addData($data);
        }
        
        // optional header/hero
        $this->includeFile($template.'/header.template.php');
        $this->includeFile($template.'/hero.template.php');

        // content file|files[]
        if (is_string($files)) {
            $return = $this->includeFile($files);

        } else if (is_array($files)) {
            foreach($files as $file){

                // return false if a file is not found
                if (!$this->includeFile($file)){
                    $return = false;   
                }
            }
        }

        // optional footer/dialog template
        $this->includeFile($template.'/dialog.template.php');
        $this->includeFile($template.'/footer.template.php');
        return $return;
    }

    /** 
     * Render JSON data
     *
     * @access public
     * @param  array    $data          The data to render
     * @param  int      $httpCode      (optional) The httpCode associated with response. Default is 200 (OK)
     *
     * @return void
     */
    public function renderJson(array $data = [], int $httpCode = 200): void
    {
        // need to clear the old headers to set status code
        header_remove();
        Response::setStatus($httpCode);                                             
        header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");  
        header('Content-Type: application/json');                               
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
    }
    
    /** 
     * Render jpeg Image 
     *
     * @access public
     * @param resource  $image         The image data.
     * @param int       $quality       (optional) The image quality. Default is 90.
     *
     * @return void
     */
    public function renderJpeg($image, int $quality = 90): void
    {
		header('Content-type: image/jpeg');
        imagejpeg($image, null, $quality);
        imagedestroy($image);
    }

    /** 
     * Render jpeg Image 
     *
     * @access public
     * @param resource  $image         The image data.
     * @param int       $quality       (optional) The image quality. Default is 90.
     *
     * @return void
     */
    public function renderPng($image, int $quality = 90): void
    {
		header('Content-type: image/png');
        imagepng($image, null, $quality);
        imagedestroy($image);
    }

    /** 
     * Render js content
     *
     * @access public
     * @param string    $content       The content to render
     *
     * @return void
     */
    public function renderJs(string $content): void
    {
        header('Content-Type: application/javascript', true);
        echo $content; 
    }

    /** 
     * Render audio file
     *
     * @access public
     * @param string     $filePath       The full path to file to render
     *
     * @return void
     */
    public function renderAudio(string $filePath): void
    {
        header("Content-Type:audio/mpeg"); 
        header("Content-Length:". filesize($filePath)); 
        readfile($filePath); 
    }

    /**
     * Render an image based on extention
     * Support for .png .jpeg .jpg
     * todo cache, gif
     * 
     * @access public
     * @param string    $path   The image file full path
     * 
     * @return void
     */
    public function renderImage(string $path)
    {
        $ext =  pathinfo($path, PATHINFO_EXTENSION);
        switch($ext){
            case 'jpeg': 
            case 'jpg': 
                ob_end_clean();
                header('Content-type: image/jpeg');
                header('Content-Length: ' . filesize($path));
                readfile($path);
                break;
            case 'png':
                ob_end_clean();
                header('Content-type: image/png');
                header('Content-Length: ' . filesize($path));
                readfile($path);
                break;
            default:
                return false;

        }    
    }

    /** 
     * Render rss/xml content
     *
     * @access public
     * @param string    $content       The content to render
     *
     * @return void
     */
    public function renderRssXml($content)
    {
		header('Content-type: application/rss+xml');
        echo $content; 
    }

    /** 
     * Render xml content
     *
     * @access public
     * @param string    $content       The content to render
     *
     * @return void
     */
    public function renderXml($content)
    {
		header('Content-type: text/xml', true);
        echo $content; 
    }

    /** 
     * Render css content
     *
     * @access public
     * @param string    $content       The content to render
     *
     * @return void
     */
    public function renderCss($content)
    {
        header("Content-type: text/css; charset=utf-8", true); 
        echo $content; 
    }

  

    
}