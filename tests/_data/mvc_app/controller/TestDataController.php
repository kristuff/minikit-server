<?php
use Kristuff\Miniweb\Mvc\Controller;

class TestDataController extends Controller
{
    public $isIncluded = false;
    public $varInIncludedFile = null;

    public function test_include_file_with_data()
    {
        $this->isIncluded = $this->view->renderHtml('test_data/required_file.php', ['test_data' => 'I am the data from TestDataController']);
        $this->varInIncludedFile = $this->view->varInIncludeFile;
    }

    public function test_locale_in_view_default()
    {
        $this->isIncluded = $this->view->renderHtml('test_data/file_with_locale_default.php');
        $this->varInIncludedFile = $this->view->varInIncludeFile;
    }

    public function test_locale_in_view_explicit()
    {
        $this->isIncluded = $this->view->renderHtml('test_data/file_with_locale_explicit.php');
        $this->varInIncludedFile = $this->view->varInIncludeFile;
    }

   //  public function test_locale_in_controller()
   // {
   //     $expexted = $this->text()
   //     $this->isIncluded = $this->view->renderHtml('test_data/file_with_locale_from_controller', ['locale_from_controller' => $expected]);
   //     $this->varInIncludedFile = $this->view->varInIncludeFile;
   // }
}