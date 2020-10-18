<?php
use Kristuff\Miniweb\Mvc\Controller;

class TestRedirectController extends Controller
{
    public function testRedirectTo()
    {
        $this->redirect('http://www.example.com/', true);
    }

    public function testRedirectWithExit()
    {
        $this->redirect('/home', true, true);
    }
}