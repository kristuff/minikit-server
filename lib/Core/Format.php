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

/**
 * Class Format
 *
 * 
 */
class Format
{
    /**
     * Split a number of seconds and returns an array with time splited in years, days, hours and minutes. 
     *
     * Eg: for 36545627 seconds => ['year' => 1, 'day' => 57, 'hour' => 23, 'minute' => 33]
     *
     * @access public
     * @static
     * @param int       $seconds        The number of seconds
     * 
     * @return array   
     */
    public static function splitTime(int $seconds): array
    {
        $units = [
            'year'   => 365*86400,
            'month'  => 30*86400,
            'day'    => 86400,
            'hour'   => 3600,
            'minute' => 60
        ];

        $time = [];

        foreach ($units as $name => $divisor){
            $div = floor($seconds / $divisor);
            $time[$name] = $div;

            if ($div > 0){
                $seconds %= $divisor;
            }
        }

        return $time;
    }

    /**
     * Returns human time 
     * 
     * Seconds to human readable text
     * Eg: for 36545627 seconds with minUnit 'minute' (default) =>      1 year, 57 days, 23 hours and 33 minutes
     * Eg: for 36545627 seconds with minUnit 'day'              =>      1 year and 57 days
     *
     * @access public
     * @static
     * @param int       $seconds      Number of seconds
     * @param string    $minUnit      (optional) Minimal output unit. Default is 'minute'.
     * 
     * @return string   Formated time string
     */
    public static function getHumanTime(int $seconds, string $minUnit = 'minute'): string
    {
        $parts = [];
        $units = [
            'year'   => 365*86400,
            'month'  => 30*86400,
            'day'    => 86400,
            'hour'   => 3600,
            'minute' => 60,
            'second' => 1,
        ];
     
        foreach ($units as $name => $divisor){

            // get units number
            $div = floor($seconds / $divisor);

            if ($div > 0){
                // set part
                $parts[] = $div.' '.$name. ($div > 1 ? 's' : '');

                // is last wanted unit?
                if ($name === $minUnit){
                    break;
                }

                // continue with mod
                $seconds %= $divisor;
            }
        }
     
        $last = array_pop($parts);
        return empty($parts) ? $last : join(', ', $parts).' and '.$last;
    }

    /**
     * Returns human size
     *
     * @access public
     * @static
     * @param float     $filesize       File size in bytes
     * @param int       $precision      Number of decimals
     *
     * @return string            
     */
    public static function getSize(float $filesize, int $precision = 2): string
    {
        $units = array('', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y');

        foreach ($units as $idUnit => $unit){
            if ($filesize < 1024){
                break;
            }
            $filesize /= 1024;
        }
        
        return round($filesize, $precision).' '.$units[$idUnit].'B';
    }

    /**
     * Remove all non numeric chars from a string
     * 
     * @access public
     * @static
     * @param string    $value            
     * 
     * @return string                   
     */
    public static function getNumeric(string $value): string
    {
        return preg_replace("/[^0-9,.]/", "", $value);
    }
}