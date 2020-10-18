<?php
namespace MyAwesomeApp;
use Kristuff\Miniweb\Mvc\Controller;

class BarController extends Controller
{
    public function foo()
    {
        return 'MyAwesomeApp\BarController::foo() in BarController.php';
    }
}
