<?php
use Kristuff\Miniweb\Mvc\Controller;
require_once __DIR__.'/../model/DummyModel.php';

class TestCookieController extends Controller
{
    public function test()
    {
        DummyModel::cookieTestMethod();
    }
}