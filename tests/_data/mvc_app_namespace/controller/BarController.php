<?php
namespace MyAwesomeApp;
use Kristuff\Minikit\Mvc\Controller;

class BarController extends Controller
{
    public function foo()
    {
        return 'MyAwesomeApp\BarController::foo() in BarController.php';
    }
}
