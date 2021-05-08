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
 * @version    0.9.3
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Core;

/**
 * Class Filter
 *
 * This is the place to put filters, usually methods that cleans, sorts and, well, filters stuff.
 */
class Filter
{
    /**
     * The XSS filter: This simply removes "code" from any data, used to prevent Cross-Site Scripting Attacks.
     *
     * A very simple introduction: Let's say an attackers changes its username from "John" to these lines:
     * "<script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>"
     * This means, every user's browser would render "John" anymore, instead interpreting this JavaScript code, calling
     * the delete.php, in this case inside the project, in worse scenarios something like performing a bank transaction
     * or sending your cookie data (containing your remember-me-token) to somebody else.
     *
     * What is XSS ?
     * @see http://phpsecurity.readthedocs.org/en/latest/Cross-Site-Scripting-%28XSS%29.html
     * @see https://www.owasp.org/index.php/XSS_Filter_Evasion_Cheat_Sheet
     *
     * XssFilter expects a value, checks if the value is a string, and if so, encodes typical script tag chars to 
     * harmless HTML (you'll see the code, it wil not be interpreted). Then the method checks if the value is an array, 
     * or an object and if so, makes sure all its string content is encoded (recursive call on its values).
     * Note that this method uses reference to the passed variable and returns and also returns the variable, meaning 
     * you can use this method like this:
     *
     * Filter::XssFilter($myVariable);
     *
     * This works like some other popular PHP functions, for example sort().
     * @see http://php.net/manual/en/function.sort.php
     * @see http://stackoverflow.com/questions/1676897/what-does-it-mean-to-start-a-php-function-with-an-ampersand
     * @see http://php.net/manual/en/language.references.pass.php
     * @see http://php.net/manual/en/control-structures.foreach.php
     *
     * FYI: htmlspecialchars() does this (from PHP docs):
     *
     * '&' (ampersand) becomes '&amp;'
     * '"' (double quote) becomes '&quot;' when ENT_NOQUOTES is not set.
     * "'" (single quote) becomes '&#039;' (or &apos;) only when ENT_QUOTES is set.
     * '<' (less than) becomes '&lt;'
     * '>' (greater than) becomes '&gt;'
     * @see http://www.php.net/manual/en/function.htmlspecialchars.php
     *
     * @param mixed     $value
     * 
     * @return mixed
     */
    public static function XssFilter(&$value)
    {
        // if argument is a string, filters that string
        if (is_string($value)) {
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        
        // if argument is an array or an object, 
        // recursivly filters its content 
        } else if (is_array($value) || is_object($value)) {

            /** 
             * Make sure the element is passed by reference,
             * In PHP 7, foreach does not use the internal array pointer. 
             * In order to be able to directly modify array elements within the loop 
             * precede $value with &. In that case the value will be assigned by reference. 
             * @see http://php.net/manual/en/control-structures.foreach.php
             */
            foreach ($value as &$valueInElement) {
                self::XssFilter($valueInElement);
            }
        }

        // return value
        return $value;
    }
}
