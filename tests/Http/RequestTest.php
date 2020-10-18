<?php
namespace Kristuff\Miniweb\Tests\Http;

use Kristuff\Miniweb\Http\Request;

class RequestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Testing the get() method of the Request class
     */
    public function testGet()
    {
        unset($_GET);
 
        $_GET["test"] = 33;
        $this->assertEquals(33, Request::get('test'));
        $this->assertNull(Request::get('notExistingKey'));
    }

    /**
     * Testing the post() method of the Request class
     */
    public function testPost()
    {
        unset($_POST);

        $int = 222;
        $string = 'test string';
        $badString = '   <script>alert("yo!");</script>   ';
        $badStringFiltered = 'alert("yo!");';

        $_POST["int"] = $int;
        $_POST["string"] = $string;
        $_POST["badString"] = $badString;

        $this->assertEquals($int, Request::post('int'));
        $this->assertEquals($string, Request::post('string'));
        $this->assertEquals($string, Request::post('string', true));
        $this->assertEquals($badString, Request::post('badString'));
        $this->assertEquals($badString, Request::post('badString', false));
        $this->assertEquals($badStringFiltered, Request::post('badString', true));
        $this->assertNull(Request::post('notExistingKey'));
    }

    /**
     * Testing the arg() method of the Request class
     */
    public function testArgWithPost()
    {
        unset($_POST);
        $request = new Request('/','POST');

        $int = 222;
        $string = 'test string';
        $badString = '   <script>alert("yo!");</script>   ';
        $badStringFiltered = 'alert("yo!");';

        $_POST["int"] = $int;
        $_POST["string"] = $string;
        $_POST["badString"] = $badString;

        $this->assertEquals($int, $request->arg('int'));
        $this->assertEquals($string, $request->arg('string'));
        $this->assertEquals($string, $request->arg('string', '', true));
        $this->assertEquals($badString, $request->arg('badString'));
        $this->assertEquals($badString, $request->arg('badString', '', false));
        $this->assertEquals($badStringFiltered, $request->arg('badString', '', true));
        $this->assertNull($request->arg('notExistingKey'));
        $this->assertEquals($int, $request->arg('notExistingKey', $int)); // with default
    }

    /**
     * Testing the arg() method of the Request class
     */
    public function testArgWithGet()
    {
        unset($_GET);
        $request = new Request('/','GET');

        $int = 222;
        $string = 'test string';

        $_GET["int"] = $int;
        $_GET["string"] = $string;

        $this->assertEquals($int, $request->arg('int'));
        $this->assertEquals($string, $request->arg('string'));
        $this->assertNull($request->arg('notExistingKey'));
    }

    
    /**
     * Testing the arg() method of the Request class
     */
    public function testArgWithPut()
    {
        unset($_GET);
        $request = new Request('/','PUT');

        $int = 222;
        $string = 'test string';

        $_GET["int"] = $int;
        $_GET["string"] = $string;

        $this->assertEquals($int, $request->arg('int'));
        $this->assertEquals($string, $request->arg('string'));
        $this->assertNull($request->arg('notExistingKey'));
    }

    /**
     * Testing the postCheckbox() method of the Request class
     */
    public function testPostCheckbox()
    {
        unset($_POST);

        // Weird side-fact: a checked checkbox that has no manually set value will mostly contain 'on' as the default
        // value in most modern browsers btw, so it makes sense to test this
        $_POST['checkboxName'] = 'on';
        $this->assertEquals(1, Request::postCheckbox('checkboxName'));

        $_POST['checkboxName'] = 1;
        $this->assertEquals(1, Request::postCheckbox('checkboxName'));

        $_POST['checkboxName'] = null;
        $this->assertNull(Request::postCheckbox('checkboxName'));
    }
 
}
