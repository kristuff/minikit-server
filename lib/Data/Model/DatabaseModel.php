<?php

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.22 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */

namespace Kristuff\Minikit\Data\Model;

use Kristuff\Minikit\Auth\TextHelper;
use Kristuff\Minikit\Mvc\Model;
use Kristuff\Minikit\Data\Core\DatabaseFactory;
use Kristuff\Patabase\Database;
use Kristuff\Patabase\Query\Select;

/**
 * Class DatabaseModel
 *
 */
abstract class DatabaseModel extends Model
{
    /**
     * Gets and returns the global Database instance
     *
     * @access public
     * @static
     *
     * @return \kristuff\Patabase\Database
     * @throw  ?
     */
    public static function database()
    {
        return DatabaseFactory::getFactory()->getConnection([
            'driver'    => self::config('DB_DRIVER'),
            'hostname'  => self::config('DB_HOST'), 
            'database'  => self::config('DB_NAME'), 
            'port'      => self::config('DB_PORT'), 
            'charset'   => self::config('DB_CHARSET'),
            'username'  => self::config('DB_USER'), 
            'password'  => self::config('DB_PASSWORD')
        ]);
    }
  
    /** 
     * Gets the number of item in given table
     * 
     * @access public
     * @static
     * @param mixed      $tableName
     * 
     * @return int    
     */
    public static function count(string $tableName): int
    {
        return (int) self::database()->select()
                                     ->count('total')
                                     ->from($tableName)
                                     ->getColumn();
    }

    /**
     * Helper function to return a correct sort direction according to 
     * given value. Supports short and long format and is case insensitive
     *
     * @access public
     * @static
     * @param mixed      $inputValue       The input value
     *
     * @return string
     */
    public static function getSortDirection($inputValue): string
    {
        if (isset($inputValue) && is_string($inputValue)){
            switch(strtolower($inputValue)){
                case 'asc':
                case 'ascending':
                    return Select::SORT_ASC; 
                    break;   
                case 'desc':
                case 'descending':
                    return Select::SORT_DESC; 
                    break;   
            }
        }
        return Select::SORT_ASC;
    }

    /**
     * Helper function to return the time column type according
     * to current driver. Useful when creating table.
     *
     * @access public
     * @static
     * @param Database      $database       The Database instance
     *
     * @return string
     */
    public static function getTimeColumnType(Database $database)
    {
        return ($database->getDriverName() === 'sqlite') ? 'int' : 'timestamp';
    }

    /**
     * Get formatted datetime according to current database driver
     * Timestamp stored ias numeric in sqlite but string mysql and pgsql 
     * 
     * @access public
     * @static
     * @param mixed     $timeValue
     * @param int       $dateFormatter
     * @param int       $dateFormatter
     * 
     * @return string
     */
    protected static function getFormattedDateTime($timeValue, int $dateFormatter, int $timeFormatter): string
    {
        $currentLocal = TextHelper::text('LOCAL_CODE');
        if (empty($timeValue) || empty($currentLocal)) {
            return '';
        }

        $fmt = datefmt_create(
            $currentLocal,
            $dateFormatter,
            $timeFormatter,
            null,
            \IntlDateFormatter::GREGORIAN
        );

        switch (self::database()->getDriverName()){
            case 'mysql':
            case 'pgsql':
                return  datefmt_format($fmt, strtotime($timeValue));
            case 'sqlite':
                return  datefmt_format($fmt, (int) $timeValue);
            default:
                return '';
        }
    }

    /**
     * Get formatted datetime according to current database driver
     * Timestamp stored in numeric in sqlite but string mysql and pgsql 
     * 
     * @access public
     * @static
     * @param mixed     $timeValue
     *
     * @return string
     */
    public static function getFormattedDateTimeShort($timeValue): string
    {
       return self::getFormattedDateTime($timeValue, \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
    }

    /**
     * Get formatted date according to current database driver
     * Timestamp stored in numeric in sqlite but string mysql and pgsql 
     * 
     * @access public
     * @static
     * @param mixed     $timeValue
     *
     * @return string
     */
    public static function getFormattedDateShort($timeValue): string
    {
        return self::getFormattedDateTime($timeValue, \IntlDateFormatter::SHORT, \IntlDateFormatter::NONE);
    }

    /**
     * Get formatted date according to current database driver
     * Timestamp stored in numeric in sqlite but string mysql and pgsql 
     * 
     * @access public
     * @static
     * @param mixed     $timeValue
     *
     * @return string
     */
    public static function getFormattedDateLong($timeValue): string
    {
        return self::getFormattedDateTime($timeValue, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);
    }

    /**
     * Get formatted date according to current database driver
     * Timestamp stored in numeric in sqlite but string mysql and pgsql 
     * 
     * @access public
     * @static
     * @param mixed     $timeValue
     *
     * @return string
     */
    public static function getFormattedDateMedium($timeValue): string
    {
        return self::getFormattedDateTime($timeValue, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE);
    }
    
    /**
     * Get formatted date according to current database driver
     * Timestamp stored in numeric in sqlite but string mysql and pgsql 
     * 
     * @access public
     * @static
     * @param mixed     $timeValue
     *
     * @return string
     */
    public static function getFormattedDateTimeLong($timeValue): string
    {
        return self::getFormattedDateTime($timeValue, \IntlDateFormatter::LONG, \IntlDateFormatter::SHORT);
    }

    /**
     * Get a timestamp according to current database driver
     * Timestamp stored in numeric in sqlite but string mysql and pgsql 
     * 
     * @access public
     * @static
     * @param mixed     $timeValue
     *
     * @return int|false
     */
    public static function getTimestamp($timeValue)
    {
        if (empty($timeValue)) {
            return time();
        }

        switch (self::database()->getDriverName()){
            case 'mysql':
            case 'pgsql':
                return  strtotime($timeValue);
            case 'sqlite':
                return (int) $timeValue;
            default:
                return time();
        }
    }

    /**
     * Get a formatted 'datetime' to insert into database from a DateTimeLocal HTML input. 
     * Sqlite stores timestamp as numeric while mysql as string.
     * DateTimeLocal HTML input use the following format yyyy-MM-ddThh:mm
     * 
     * TODO pgsql
     * 
     * @access public
     * @static
     * @param string        $dateInput      The date time to convert. 
     * 
     * @return mixed
     */
    public static function getSqlDateTimeFromDateInput(?string $dateInput)
    {
        if (!empty($dateInput)){
            $time = \DateTime::createFromFormat("Y-m-d\TH:i", $dateInput);
            
            if ($time === false) return null;

            switch(self::database()->getDriverName()){
                case 'sqlite':
                    return $time->getTimestamp();
                case 'mysql':
                    return $time->format('Y-m-d h:i:s');
            }     
        }
        return null;
    }

    /**
     * Get a 'formatted' timestamp to insert into database. 
     * Sqlite stores timestamp as numeric while mysql as string.
     * 
     * TODO pgsql
     * 
     * @access public
     * @static
     * @param int           $timestamp      The timestamp to convert. Default is null (current timestamp)
     * @param Database      $database       The Database instance. Default us null (default database) 
     * 
     * @return mixed
     */
    public static function getFormattedTimestamp(?int $timestamp = null, ?Database $database = null)
    {
        $time = empty($timestamp) ? time() : $timestamp;
        $db   = empty($database) ? self::database() : $database;

        return ($db->getDriverName() === 'mysql') ? date('Y-m-d H:i:s', $time) : $time;
    }

    /**
     * TODO move
     * 
     * @access public
     * @static
     * @param mixed     $timeValue
     *
     * @return string
     */
    public static function getDatabaseDateTimeInput($timeValue)
    {
        if (empty($timeValue)) {
            return date(TextHelper::text('FORMAT_DATE_TIME'), time());
        }

        switch (self::database()->getDriverName()){
            case 'mysql':
            case 'pgsql':
                return $timeValue;
            case 'sqlite':
                return date(TextHelper::text('FORMAT_DATE_TIME'), $timeValue);
            default:
                return date(TextHelper::text('FORMAT_DATE_TIME'), time());
        }
    }

    /**
     * Helper function to return the text column type according
     * to current driver. Useful when creating table.
     *
     * @access public
     * @static
     * @param Database      $database       The Database instance
     *
     * @return string
     */
    public static function getTextColumnType(Database $database)
    {
        return ($database->getDriverName() === 'mysql')  ? 'longtext' : 'text';
    }

    /** 
     * Checks for duplicate in database
     *
     * @access public
     * @static
     * @param string        $table           The database table
     * @param string        $column          The database table
     * @param mixed         $value           The value to search
     *
     * @return bool         True if the search value already exists in database, otherwise False.
     */
    public static function isDuplicateItemExists(string $table, string $column, $value): bool
    {
         return self::database()->select()
                                ->count('num')
                                ->from($table)
                                ->whereEqual($column, $value)
                                ->getColumn() > 0;
    }

    /**
     * Define the Select query ORDER BY, according to input value and
     * allowed values 
     * 
     * @access public
     * @static
     * 
     * @param Select    $query          The Select query instance
     * @param array     $allowedValues  The possible values
     * @param string    $value          The input value
     * @param string    $direction      The sort direction
     *
     * @return bool      true if Order has been set
     */
    public static function setSelectQueryOrder(Select &$query, array $allowedValues, ?string $value = '', ?string $direction = ''): bool
    {
       if (in_array($value, $allowedValues)){
           if ($value === 'random'){
               $query->orderRand();
               return true;
           }
           $query->orderBy($value, self::getSortDirection($direction));
           return true;
       }
       return false;
    }
}