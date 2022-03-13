<?php

/**
 *        _      _ _   _ _
 *  _ __ (_)_ _ (_) |_(_) |_
 * | '  \| | ' \| | / / |  _|
 * |_|_|_|_|_||_|_|_\_\_|\__|
 * 
 * This file is part of Kristuff/Minikit v0.9.16 
 * Copyright (c) 2017-2022 Christophe Buliard  
 */


namespace Kristuff\Minikit\Auth\Controller;

use Kristuff\Minikit\Mvc\Application;

/**
 * Class AdminController
 *
 * Redirect no logged user to login page (extends PrivateController) 
 * Redirect no admin to home page 
 */
class AdminController extends PrivateController
{
    /**
     * Constructor
     *
     * @access public
     * @param Application $application        The application instance
     */
    public function __construct(Application $application)
    {
        parent::__construct($application);
        $this->redirectNoAdminToHome();
    }
}