<?php
namespace MyAwesomeApp;
use Kristuff\Miniweb\Mvc\Controller;

class Foo extends Controller
{
    public function bar()
    {
        return 'MyAwesomeApp\Foo::bar() in Foo.php';
    }
}
