<?php

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
 * @version    0.9.5
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Data\Core;

use Kristuff\Patabase\Database;

/** 
 * class DatabaseFactory 
 */
class DatabaseFactory
{
    /**
     * @access private
     * @static var
     * @var DatabaseFactory             factory         The DatabaseFactory instance 
     */
    private static $factory;

    /**
     * @access private
     * @var \Kristuff\Patabase\Database $database       The Database instance
     */
    private $database;

    /**
     * Gets or creates a DatabaseFactory Instance
     *
     * @access public
     * @static method
     *
     * @return DatabaseFactory
     */
    public static function getFactory()
    {
        if (!self::$factory) {
            self::$factory = new DatabaseFactory();
        }
        return self::$factory;
    }

    /**
     * Get or create a Database instance
     *
     * @access public
     * @param array    $parameters     The connection settings
     *
     * @return \Kristuff\Patabase\Database
     */
    public function getConnection(array $parameters) {
        if (!$this->database) {

            // Check DB connection in try/catch block. Also when PDO is not constructed properly,
            // prevent to exposing database host, username and password in plain text as etc. by 
            // throwing custom error message
            try 
            {
                //create Database instance
                $this->database = new Database($parameters);

            } catch (\Exception $e) {
           // } catch (Error $e) {

                // Echo custom message. Echo error code gives you some info.
                // echo 'Database connection can not be estabilished. Please try again later.';
                
                // Stop application :(
                // No connection, reached limit connections etc. so no point to keep it running

                //\Kristuff\Miniweb\Core\SafeExit::exit();
                exit();
            }
        }
        return $this->database;
    }
}