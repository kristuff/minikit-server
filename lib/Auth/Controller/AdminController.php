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
 * @version    0.9.15
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Auth\Controller;

use Kristuff\Miniweb\Mvc\Application;

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