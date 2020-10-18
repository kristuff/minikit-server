<?php
use Kristuff\Miniweb\Mvc\Controller;

class Foo extends Controller
{
    public $isIncluded = false;
    public $varInIncludedFile = null;

    public function bar()
    {
        return 'Foo::bar() in Foo.php';
    }

    public function test_include_file_in_main_folder()
    {
        $this->isIncluded = $this->view->renderHtml('test_include.php');
        $this->varInIncludedFile = $this->view->varInIncludeFile;
    }

    public function test_include_file_in_sub_folder()
    {
        $this->isIncluded = $this->view->renderHtml('foo/test_include.php');
        $this->varInIncludedFile = $this->view->varInIncludeFile;
    }   

    public function test_include_file_that_does_not_exists()
    {
        $this->isIncluded = $this->view->renderHtml('foo/test_include_wrrrrrrrrrrrrrrrrrrrrong_file.php');
    }

    public function test_include_multi_files()
    {
        $this->isIncluded = $this->view->renderHtml(['test_include.php', 'foo/test_include.php']);
        $this->varInIncludedFile = $this->view->varInIncludeFile;
   }
}