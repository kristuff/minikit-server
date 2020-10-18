<?php
use Kristuff\Miniweb\Mvc\Controller;

class TestJsonController extends Controller
{
    public function api()
    {
        $this->view->renderJson(['data' => 'value'], 201);
    }
}
