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
 * @version    0.9.4
 * @copyright  2017-2021 Kristuff
 */
    
namespace Kristuff\Miniweb\Core;

/**
 * Class Path
 *
 * Provides directory/file related helper functions
 */
class Path
{
    /** 
     * Checks if a path exists and is a directory
     *
     * @access public
     * @static
     * @param string    $path           The directory's full path
     *  
     * @return bool     true if the given path exists and is a directory, otherwise false.
     */
    public static function exists(string $path): bool 
    {
        return file_exists($path) && is_dir($path);
    }

    /** 
     * Checks if the path exists and is a file 
     *
     * @access public
     * @static
     * @param string    $path           The file's full path
     *  
     * @return bool     true if the given path exists and is a file, otherwise false.
     */
    public static function fileExists(string $path): bool 
    {
        return file_exists($path) && is_file($path);
    }

    /** 
     * Checks if a directory is writable
     *
     * @access public
     * @static
     * @param string    $path           The directory full path
     *  
     * @return bool     true if the given path exists and is writable, otherwise false
     */
    public static function isWritable(string $path): bool 
    {
        return file_exists($path) && is_dir($path) && is_writable($path);
    }

    /** 
     * Checks if a file is writable
     *
     * @access public
     * @static
     * @param string    $path           The file full path
     *  
     * @return bool     true if the given path exists and is writable, otherwise false
     */
    public static function isFileWritable(string $path): bool 
    {
        return file_exists($path) && is_file($path) && is_writable($path);
    }

    /** 
     * Checks if a directory is readable
     *
     * @access public
     * @static
     * @param string    $path           The directory full path
     *  
     * @return bool     true if the given path exists and is readable, otherwise false.
     */
    public static function isReadable(string $path): bool 
    {
        return file_exists($path) && is_dir($path) && is_readable($path);
    }

    /** 
     * Checks if a file is readable
     *
     * @access public
     * @static
     * @param string    $path           The file full path
     *  
     * @return bool     true if the given path exists and is readable, otherwise false.
     */
    public static function isFileReadable(string $path): bool 
    {
        return file_exists($path) && is_file($path) && is_readable($path);
    }

    /** 
     * Get extension 
     *
     * @access public
     * @static
     * @param string    $path           The file or directory full path 
     * 
     * @return string   extension without dot
     */
    public static function getExtension(string $path): string 
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /** 
     * Get basename 
     *
     * @access public
     * @static
     * @param string    $path           The file or directory full path 
     * 
     * @return string   basename
     */
    public static function getBaseName(string $path): string 
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /** 
     * Get filename 
     *
     * @access public
     * @static
     * @param string    $path           The file or directory full path 
     * 
     * @return string   filename
     */
    public static function getFileName(string $path): string 
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /** 
     * Get dirname 
     *
     * @access public
     * @static
     * @param string    $path           The file or directory full path 
     * 
     * @return string   dirname
     */
    public static function getDirName(string $path): string 
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }
}