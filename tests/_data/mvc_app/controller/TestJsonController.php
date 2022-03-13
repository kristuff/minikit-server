<?php
use Kristuff\Minikit\Mvc\Controller;

class TestJsonController extends Controller
{
    public function api()
    {
        $this->view->renderJson(['data' => 'value'], 201);
    }
}
