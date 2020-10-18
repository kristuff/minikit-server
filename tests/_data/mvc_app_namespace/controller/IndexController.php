<?php
namespace MyAwesomeApp;
use Kristuff\Miniweb\Mvc\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return 'MyAwesomeApp\IndexController::index() in IndexController.php';
    }
}
