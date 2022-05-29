<?php declare(strict_types=1);

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.21 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */

namespace Kristuff\Minikit\Mvc;

use Kristuff\Minikit\Http;
use Kristuff\Minikit\Http\Request;
use Kristuff\Minikit\Http\Response;
use Kristuff\Minikit\Http\Session;
use Kristuff\Minikit\Http\Server;
use Kristuff\Minikit\Mvc\Application;
use Kristuff\Minikit\Mvc\Controller;
use Kristuff\Minikit\Mvc\Factory;

/**
 * Class Rooter
 *
 */
class Rooter
{

    /** 
     * @access protected
     * @var Mvc\Application $application        The application instance
     */
    protected $application;

    /** 
     * @access private
     * @var Controller  $controller         The Controller instance
     */
    private $controller;

    /** 
     * @access private
     * @var string          $controllerName     The Name of the controller 
     */
    private $controllerName;

    /** 
     * @access private
     * @var string          $actionName         The Name of the controller's method
     */
    private $actionName;
 
    /** 
     * @var array           parameters          URL parameters, will be passed to controller's method 
     */
    private $parameters = array();

    /** 
     * @access protected
     * @var Http\Session    $httpSession        The Http Session instance
     */
    protected $httpSession;

    /** 
     * @access private
     * @var bool            $needRegistration    true to force action registration  // TODO
     */
    private $needRegistration = false;

    /** 
	 * Constructor
     *
     * @access public
     * @param  Mvc\Application $application        The application instance
     * @param  Http\Session    $httpSession        The Http Session instance
     */
    public function __construct(Application $application, Session $session)
    {
        // register app
        $this->application = $application;
        $this->httpSession = $session;
    }

    /** 
	 * Gets the Controller instance 
     *
     * @access public
     * @return Controller
     */
    public function controller(): Controller
    {
        return $this->controller;
    }

    /** 
	 * Gets the Session instance 
     *
     * @access public
     * @return Http\Session
     */
    public function session() : Session
    {
        return $this->session;
    }

    /** 
     * Analyze URL elements, retreives the controller and call according 
     * controller/method. 
     *
     * @access public
     * @return bool     true is the controller has handle the request, otherwise false.
	 */
    public function handleRequest(): bool
    {
        // get url and method
        $uri = Http\Request::get('url') ?? '';
        $method = Http\Server::requestMethod();
               
        // create array with URL parts in $url
        $this->parseUrl($uri);

        // if case there is no controller and action define
        // set default values
        $this->checkForNoControllerOrAction();

         // create and register HttpRequest (after knowing the name/action) 
        $request = new Http\Request($uri, $method, $this->controllerName, $this->actionName, $this->parameters);
        Factory::getFactory()->setRequest($request);

        // load controller
        if ($this->loadController()){

            // handle request
           return $this->handleRequestInController($method, $this->actionName, $this->parameters);
        } 

        // not found
        return false;   
    }

    /** 
     * Split the URL 
     *
     * @access private
     * @param string    $url
     *
     * @return void
     */
    private function parseUrl(string $url): void
    {
        // split URL
        $url = trim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode('/', $url);

        // put URL parts into according properties
        $this->controllerName = isset($url[0]) ? $url[0] : null;
        $this->actionName     = isset($url[1]) ? $url[1] : null;

        // remove controller name and action name from the split URL
        unset($url[0], $url[1]);

        // rebase array keys and store the URL parameters
        $this->parameters = array_values($url);
    }

	/**
	 * Checks if controller and action names are given. If not, default values 
     *
     * @access private
     * @return void
	 */
	private function checkForNoControllerOrAction(): void
	{
		// check for controller: no controller given ? then make controller = default controller (from config)
		if (empty($this->controllerName)) {
			$this->controllerName = $this->application->config('CONTROLLER_DEFAULT');
		}

		// check for action: no action given ? then make action = default action (from config)
		if (empty($this->actionName)) {
			$this->actionName = $this->application->config('CONTROLLER_ACTION_DEFAULT');
		}
	}

	/**
	 * Load the given controller.
     *
     * @access private
     * @return bool     true is the controller has been found and loaded, otherwise false.
	 */
    private function loadController(): bool
    {
        // rename controller name to real controller class/file name ("index" to "Index")
        $controllerName = ucwords($this->controllerName);
        
        // get controller params
        $controllerExtension  = $this->application->config('CONTROLLER_EXTENSION');
        $controllerPath       = $this->application->config('CONTROLLER_PATH');
        $controllerNamespace  = $this->application->config('CONTROLLER_NAMESPACE');  

        // construct file name
        $filename = $controllerPath . $controllerName . $controllerExtension.'.php';
        
        // does such a controller exist ?
        if (file_exists($filename)) {

            // load this file if no namespace defined
            if (empty($controllerNamespace)) {
                require_once $filename;
            }
            
            // create an instance of that controller
            $controllerFullName = $controllerNamespace.$controllerName.$controllerExtension;
            $this->controller = new $controllerFullName($this->application);

            return true;
        }

        // not found
        return false;
    }

    /** 
     * Handle request
     *
     * @access public
     * @param string    $requestMethod        The request method (POST, GET...)
     * @param string    $actionName           The action name inside the controller
     * @param array     $parameters           The method's parameters
     *  
     * @return bool
     */
    private function handleRequestInController(string $requestMethod, string $actionName, array $parameters = array()): bool
    {
        // names of reserved actions
        $reservedActions = ['request', 'session', 'cookie', 'token', 'redirect'];

        // get internal method:action key for registered methods
        // $methodKey = $requestMethod.':'.$actionName; 

        // Such method/action exists in controller?
        // is reserved action name?
        // is registered actions? (in case we use registration)
        if (!method_exists($this->controller, $actionName) || in_array($actionName, $reservedActions)){
            
            // no action handled
            return false;
        }

        // call the method and pass arguments to it
        // return false on error or if the method returns false.
        if (call_user_func_array(array($this->controller, $actionName), $parameters) === false){
            return false;
        };
        
        // request handled
        return true;
    }
}