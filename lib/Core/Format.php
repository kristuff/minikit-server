<?php declare(strict_types=1);

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.20 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */

namespace Kristuff\Minikit\Core;

use Kristuff\Minikit\Auth\TextHelper;

/**
 * Class Format
 *
 * 
 */
class Format
{
    /**
     * Format a number according to local 
     * 
     * @access public
     * @static
     * @param int           $number       
     *
     * @return string
     */
    public static function getFormattedCounter(int $number)
    {
        if ($number > 999999) {
            $val = round($number / 1000000, 1, PHP_ROUND_HALF_DOWN);
            return number_format($val,1, TextHelper::text('FORMAT_DECIMAL_SEPARATOR'),TextHelper::text('FORMAT_THOUSANDS_SEPARATOR')).'m';
        }
        if ($number > 999) {
            $val = round($number / 1000, 1, PHP_ROUND_HALF_DOWN);
            return number_format($val,1, TextHelper::text('FORMAT_DECIMAL_SEPARATOR'),TextHelper::text('FORMAT_THOUSANDS_SEPARATOR')).'k';
        }
        return number_format($number, 0, TextHelper::text('FORMAT_DECIMAL_SEPARATOR'),TextHelper::text('FORMAT_THOUSANDS_SEPARATOR'));
    }

    /**
     * Get a formatted date from given timestamp
     * (get format from locale)
     *
     * @access public
     * @static
     * @param int           $timestamp       
     * @param string        $timezone       Default is 'UTC'      
     *
     * @return string
     */
    public static function getFormattedDate(int $timestamp, ?string $timezone = 'UTC')
    {
        $date = new \DateTime(); 
        $date->setTimezone(new \DateTimeZone($timezone ?? 'UTC'));
        $date->setTimestamp($timestamp);
        return $date->format(TextHelper::text('FORMAT_DATE'));
    }

    /**
     * Get a formatted date and time from given timestamp
     * (get format from locale)
     *
     * @access public
     * @static
     * @param int           $timestamp       
     * @param string        $timezone       Default is 'UTC'      
     *
     * @return string
     */
    public static function getFormattedDateTime(int $timestamp, ?string $timezone = 'UTC')
    {
        $date = new \DateTime(); 
        $date->setTimezone(new \DateTimeZone($timezone ?? 'UTC'));
        $date->setTimestamp($timestamp);
        return $date->format(TextHelper::text('FORMAT_DATE_TIME'));
    }

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
     * Get a localized message according to given key.
     *
     * @access public
     * @static
     * @param string     $localKey              The key in local file              
     * @param mixed      $diff                  
     *
     * @return string            
     */
    private static function formatRelativeTime(string $localKey, $diff)
    {
        $mask = TextHelper::text($localKey);
        
        // pluralize with 's' if localized mask doesn't end with 's%s'
        $plur = ($diff > 1 && substr($mask, -3, 1) !== 's') ? 's' : '';
        return sprintf($mask, $diff, $plur);
    }

    /**
     * Returns the relative time compared to the given timestamp.
     *
     * @access public
     * @static
     * @param int       $timestamp              
     * @param string    $fallbackDateFormat     The fallback date format
     *
     * @return string            
     */
    public static function relativeTime(int $timestamp, string $fallbackDateFormat = 'd/m/Y - h:i')
    {
        $diff = time() - $timestamp;
        if ($diff < 0) {
            return date($fallbackDateFormat, $timestamp);
        }

        if ($diff < 60) {
            return self::formatRelativeTime('REL_TIME_SECOND', $diff);
        }

        $diff = floor($diff / 60);
         if ($diff < 60) {
            return self::formatRelativeTime('REL_TIME_MINUTE', $diff);
        }

        $diff = floor($diff / 60);
        if ($diff < 24) {
            return self::formatRelativeTime('REL_TIME_HOUR', $diff);
        }

        $diff = floor($diff / 24);
        if ($diff < 7) {
            return self::formatRelativeTime('REL_TIME_DAY', $diff);
        }

        $diff = floor($diff / 7);
        if ($diff < 4) {
            return self::formatRelativeTime('REL_TIME_WEEK', $diff);
        }

        $diff = floor($diff / 4);
        if ($diff < 12) {
            return self::formatRelativeTime('REL_TIME_MONTH', $diff);
        }

        $diff = floor($diff / 12);
        return self::formatRelativeTime('REL_TIME_YEAR', $diff);
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

    /** 
     * Gets whether the given user email is valid or not
     *
     * @access public
     * @static
     * @param  string       $email  The email's address
     *
     * @return bool         
     */
    public static function isValidEmail($email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    

    /**
     * TODO
     */
    public static function summary($value, $min_length = 5, $max_length = 120, $end = '[...]')
    {
        $length = strlen($value);

        if ($length > $max_length) {
            $max = strpos($value, ' ', $max_length);
            if ($max === false) {
                $max = $max_length;
            }
            return substr($value, 0, $max).' '.$end;
        } elseif ($length < $min_length) {
            return '';
        }

        return $value;
    }
}