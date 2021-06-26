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
 * @version    0.9.8
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Mvc;

use Kristuff\Miniweb\Mvc\Application;
use Kristuff\Miniweb\Http\Response;

/** 
 * Class View
 *
 * Handles output and provides some methods to the view
 */
class View
{
    /**
     * @access private
     * @var array       $data       The data to pass to the view
     */
    private $data = [];

    /**
     * Include a file to be rendered
     *
     * @access protected
     * @param  string       $filename       Path of view to include, usually folder/file with extension
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
     * Converts characters to HTML entities
     * This is important to avoid XSS attacks, and attempts to inject malicious code in your page.
     *
     * @access public
     * @param  string   $str    The string.
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
     * @param  string   $key        The key
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
     * @param  string   $key        The key
     * @param  mixed    $value      The key value
     *
     * @return void
     */
    public function setData(string $key, $value)
    {
        $this->data[$key] = $value;
    }   
    
    /**
     * Add data 
     *
     * @access public
     * @param  array    $data       The key/value array
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
     * @param  string   $key        The key
     * @param  string   $locale     (optional) The locale to use. Default is null so the default one is used.
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
     * @param  string   $key        The key
     * @param  string   $section    The application section
     * @param  string   $locale     The locale to use (the default is used if null). (optional)
     *
     * @return mixed    The key value is the key exists, otherwise null.
     */
    public function textSection(string $key, string $section, ?string $locale = null): ?string
    {
        return Application::textSection($key, $section, $locale);
    }         

   
    /**
     * Includes file(s) to be rendered
     *
     * @access public
     * @param  array|string     $files          Path of view(s) to include, usually folder(s)/file(s)
     * @param  array            $data           (optional) The data to be passed to the view. Default is an empty array.
     * @param  string           $template       (optional) The template to use.
     *
     * @return bool         true if the files exist and have been included, otherwise false
     */
    public function renderHtml($files, array $data = [], $template = ''): bool
    {
        $return = true;

        // pass data to the view
        if (!empty($data)){
            $this->addData($data);
        }
        
        // optional header
        $this->includeFile($template.'/header.template.php');
        
        // if argument is a string
        if (is_string($files)) {
            $return = $this->includeFile($files);

        // if argument is an array 
        } else if (is_array($files)) {

            // load all files
            foreach($files as $file){

                // return file if a file is not found
                if (!$this->includeFile($file)){
                    $return = false;   
                }
            }
        }

        // optional footer
        $this->includeFile($template.'/footer.template.php');
        return $return;
    }

    /** 
     * Renders JSON data
     *
     * @access public
     * @param  array    $data          The data to render
     * @param  int      $httpCode      (optional) The httpCode associated with response. Default is 200 (OK)
     *
     * @return void
     */
    public function renderJson(array $data = [], int $httpCode = 200)
    {
        // clear the old headers
        header_remove();
        
         // set the header response code                                                        
        Response::setStatus($httpCode);                                             
        
        // make sure cache is forced
        header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");  
        
        // set content-type
        header('Content-Type: application/json');                               
       
        // echo the encoded json data
        echo json_encode($data, JSON_UNESCAPED_UNICODE | 
                                JSON_UNESCAPED_SLASHES | 
                                JSON_NUMERIC_CHECK | 
                                JSON_PRETTY_PRINT);
    }
    
    /** 
     * Renders jpeg Image 
     *
     * @access public
     * @param  array    $image         The image data.
     * @param  int      $quality       (optional) The image quality. Default is 90.
     *
     * @return void
     */
    public function renderJpeg($image, $quality = 90)
    {
        // define header
		header('Content-type: image/jpeg');

        //render image
        imagejpeg($image, null, $quality);
        
        // Free up memory
        imagedestroy($image);
    }

    /** 
     * Renders jpeg Image 
     *
     * @access public
     * @param  array    $image         The image data.
     * @param  int      $quality       (optional) The image quality. Default is 90.
     *
     * @return void
     */
    public function renderPng(string $image, int $quality = 90)
    {
        // define header
		header('Content-type: image/png');

        //render image
        imagepng($image, null, $quality);

        // Free up memory
        imagedestroy($image);
    }

   /** 
    * Renders js file
    *
    * @access public
    * @param  string    $filePath       The full path file to render
    *
    * @return void
    */
    public function renderJs(string $filePath)
    {
        // set content-type
        header('Content-Type: application/javascript', true);
        
        // Render file
        readfile($filePath); 
    }

   /** 
    * Renders audio file
    *
    * @access public
    * @param  string    $filePath       The full file path to render
    *
    * @return void
    */
    public function renderAudio(string $filePath)
    {
        // set content-type
        header("Content-Type:audio/mpeg"); 
        header("Content-LEngth:". filesize($filePath)); 
        
        // Render file
        readfile($filePath); 
    }

    
}