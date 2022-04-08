<?php

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.19 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */

namespace Kristuff\Minikit\Data\Model;

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