<?php
use Kristuff\Minikit\Mvc\Controller;
require_once __DIR__.'/../model/DummySessionModel.php';

class TestSessionController extends Controller
{
    public function test()
    {
        DummySessionModel::registerSomethingInSession();
    }
}