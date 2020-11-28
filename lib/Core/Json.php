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
 * @version    0.9.2
 * @copyright  2017-2020 Kristuff
 */

namespace Kristuff\Miniweb\Core;

Use Kristuff\Miniweb\Core\Path;

/**
 * Class Json
 *
 * Helper class to deal with Json data
 */
class Json
{
    /** 
     * Load and returns decoded Json from given file  
     *
     * @access public
     * @static
	 * @param string    $filePath       The file full path
	 * @param bool      $trowError      Throw error on true or silent process. Default is true.
     *  
	 * @return array|null 
     * @throws \Exception
     * @throws \LogicException
     */
    public static function fromFile(string $filePath, bool $throwError = true): ?array
    {
        // check file exists
        if (!Path::fileExists($filePath)){
           if ($throwError) {
                throw new \Exception('Config file not found');
           }
           return null;  
        }

        // get and parse content
        $content = file_get_contents($filePath);
        $json    = json_decode(utf8_encode($content), true);

        // check for errors
        if ($json == null && json_last_error() != JSON_ERROR_NONE){
            if ($throwError) {
                throw new \LogicException(sprintf("Failed to parse config file Error: '%s'", json_last_error_msg()));
            }
        }

        return $json;        
    }

    /** 
     * Parse JSON data and returns decoded array from given JSON data
     *
     * @access public
     * @static
	 * @param string    $json           The JSON data
     *  
	 * @return mixed
     */
    public static function parse(string $json)
    {
        $json = json_decode(utf8_encode($json), true);
    }
}