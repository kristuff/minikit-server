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
 * @version    0.9.0
 * @copyright  2017-2020 Kristuff
 */

namespace Kristuff\Miniweb\Mvc;

use Kristuff\Miniweb\Mvc\Model;

/** 
 * TaskResult
 *
 * Represents a normalized response to a request. This is a simple array.
 * Can be used in most internal requests, and/or as a standard for API responses 
 * (still need to be json encoded), 
 *   code:       follow the HTTP codes
 *   success:    response status (dertermined by HTTP code. success is TRUE when code is < 300, 
 *               otherwise FALSE)
 *   message:    message to pass to end user
 *   data:       data to pass to end user    
 *   errors:     errors to pass to end user
 * 
 * 
 *     
 */
class TaskResponse extends Model
{
    /** 
     * @var array       $response
     */
    private $response = [];

    /**
     * Constructor
     * 
     * @access public
	 * @param int       $code           The response code (must matchs a valid http response code)
	 * @param string    $message        (optional) The response main message
	 * @param array     $data           (optional) The response data (if any)
	 * @param array     $errors         (optional) The response data (if any)
     *
     * @return array    
     */
    public function __construct(int $code = 200, ?string $message = null, ?array $data = [], ?array $errors = [])
    {
        $this->response = [
            'code'      => $code,
            'success'   => $code < 300 ? true : false,
            'message'   => !empty($message) ? $message : '',
            'data'      => $data ?? [],
            'errors'    => $errors ?? []
        ];
    }

    /**
     * Creates and returns a new TaskResponse
     * 
     * @access public
     * @static
	 * @param int       $code           The response code (must matchs a valid http response code)
	 * @param string    $message        (optional) The response main message
	 * @param array     $data           (optional) The response data (if any)
	 * @param array     $errors         (optional) The response data (if any)
     *
     * @return TaskResponse                
     */
    public static function create(int $code = 200, string $message = '', array $data = [], array $errors = [])
    {
        return new TaskResponse($code, $message, $data, $errors); 
    }

    /**
     * Gets the response success message
     * 
     * @access public
     * 
	 * @return bool
     */
    public function success(): bool
    {
        return $this->response['success']; 
    }

    /**
     * Gets the response code
     * 
     * @access public
     * 
	 * @return int
     */
    public function code(): int
    {
        return $this->response['code']; 
    }

    /**
     * Set the response success message
     * 
     * @access public
     * @param string    $message        The response message
     * 
	 * @return void
     */
    public function setMessage(?string $message)
    {
        $this->response['message'] = !empty($message) ? $message : '';
    }

    /**
     * Set the response code
     * 
     * @access public
     * @param int       $code          The response code
     * 
	 * @return void
     */
    public function setCode(int $code): void
    {
        $this->response['code'] = $code;
        $this->response['success'] = $code < 300 ? true : false;
    }

    /**
     * Gets the response success message
     * 
     * @access public
     * 
	 * @return string
     */
    public function message(): string
    {
        return $this->response['message'];
    }

    /**
     * Gets the response errors
     * 
     * @access public
     * 
	 * @return array
     */
    public function errors(): array
    {
        return $this->response['errors'];
    }

    /**
     * Set response data
     * 
     * @access public
     * @param array     $data
     * 
	 * @return void
     */
    public function setData(array $data): void
    {
        $this->response['data'] = $data;
    }

    /**
     * Add response data
     * 
     * @access public
     * @param mixed     $data
     * 
	 * @return void
     */
    public function addData($data): void
    {
        $this->response['data'][] = $data;
    }

    /**
     * Gets the response data
     * 
     * @access public
     * 
	 * @return array
     */
    public function data(): array
    {
        return $this->response['data'];
    }

    /**
     * Test a value for false
     * 
     * @access public
     * @param mixed     $result             The tested value
     * @param int       $errorCode          The error code in case of wrong assertion
     * @param mixed     $errorMessage       The error message in case of wrong assertion
     * 
	 * @return bool
     */
    public function assertEquals($expected, $result, int $errorCode, ?string $errorMessage = null): bool
    {
        if ($result !== $expected){
            $this->response['code'] = ($errorCode > $this->response['code']) ? $errorCode : $this->response['code'];
            $this->response['errors'][] = ['code' => $errorCode, 'message' => $errorMessage ?? ''];  
            $this->response['success'] = false;

            return false;
        }
        return true;
    }

    /**
     * Test a value for true
     * 
     * @access public
     * @param mixed     $result             The tested value
     * @param int       $errorCode          The error code in case of wrong assertion
     * @param mixed     $errorMessage       The error message in case of wrong assertion
     * 
	 * @return bool
     */
    public function assertTrue($result, int $errorCode, ?string $errorMessage = null): bool
    {
        return $this->assertEquals(true, $result, $errorCode, $errorMessage);
    }

    /**
     * Test a value for false
     * 
     * @access public
     * @param mixed     $result             The tested value
     * @param int       $errorCode          The error code in case of wrong assertion
     * @param mixed     $errorMessage       The error message in case of wrong assertion
     * 
	 * @return bool
     */
    public function assertFalse($result, int $errorCode, ?string $errorMessage = null): bool
    {
        return $this->assertEquals(false, $result, $errorCode, $errorMessage);
    }
   
    /**
     * Check if a required parameter is not empty 
     *
     * @access public
     * @param string     $parameterName                The parameter name
	 * @param mixed      $parametervalue               The parameter value
	 * @param string     $errorMessageStart            The beginning of the error message. Default is empty string.
     * 
	 * @return bool                
     */
    public function validateNoEmptyParameter(string $parameterName, $parameterValue, $errorMessageStart = ''): bool
    {
        return $this->assertTrue(!empty($parameterValue), 400, 
                $errorMessageStart .                     
                sprintf(self::text('ERROR_PARAM_NULL_OR_EMPTY'), $parameterName) );
    }

    /**
     * Get the response as array
     *
     * @access public
     * 
	 * @return array                
     */
    public function toArray(): array
    {
        return $this->response;
    }

    /**
     * Send the result of the task (message if success, errors otherwise) of in session feedback. 
     * 
     * @access public
     * 
	 * @return void                
     */
    public function toFeedback(): void
    {
        foreach ($this->errors() as $error){
            self::feedback($error['message'], false);
        }
       
        if ($this->success()){
            self::feedback($this->message());
        }
    }
}