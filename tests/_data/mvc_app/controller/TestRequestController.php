<?php
use Kristuff\Minikit\Mvc\Controller;
require_once __DIR__.'/../model/DummyModel.php';

class TestRequestController extends Controller
{
    public function test()
    {
        DummyModel::requestTestMethod();
    }
}