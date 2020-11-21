<?php
use Kristuff\Miniweb\Mvc\Controller;

class TestRedirectController extends Controller
{
    public function testRedirectTo()
    {
        $this->redirect('http://www.example.com/', 301, false);
    }

    public function testRedirectWithExit()
    {
        $this->redirect('/home', 301, true);
    }
}