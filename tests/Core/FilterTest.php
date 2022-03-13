<?php
namespace Kristuff\Minikit\Tests\Core;

use Kristuff\Minikit\Core\Filter;

class FilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * When string argument contains bad code the encoded (and therefore un-dangerous) string should be returned
     */
    public function testXssFilterWithBadCodeInString_byref()
    {
        $codeBefore = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeAfter = 'Hello &lt;script&gt;var http = new XMLHttpRequest(); http.open(&#039;POST&#039;, &#039;example.com/my_account/delete.php&#039;, true);&lt;/script&gt;';

        Filter::XssFilter($codeBefore);
        $this->assertEquals($codeAfter, $codeBefore);
    }

    /**
     * When string argument contains bad code the encoded (and therefore un-dangerous) string should be returned
     */
    public function testXssFilterWithBadCodeInString_return()
    {
        $codeBefore = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeAfter = 'Hello &lt;script&gt;var http = new XMLHttpRequest(); http.open(&#039;POST&#039;, &#039;example.com/my_account/delete.php&#039;, true);&lt;/script&gt;';

        $this->assertEquals($codeAfter, Filter::XssFilter($codeBefore));
    }


    public function testXssFilterWithArrayOfBadCode_byref()
    {
        $codeBefore1 = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeBefore2 = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeAfter = 'Hello &lt;script&gt;var http = new XMLHttpRequest(); http.open(&#039;POST&#039;, &#039;example.com/my_account/delete.php&#039;, true);&lt;/script&gt;';

        $badArray = [$codeBefore1, $codeBefore2];
        Filter::XssFilter($badArray);         

        $this->assertEquals($codeAfter, $badArray[0]);
        $this->assertEquals($codeAfter, $badArray[1]);
    }

    public function testXssFilterWithArrayOfBadCode_return()
    {
        $codeBefore1 = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeBefore2 = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeAfter = 'Hello &lt;script&gt;var http = new XMLHttpRequest(); http.open(&#039;POST&#039;, &#039;example.com/my_account/delete.php&#039;, true);&lt;/script&gt;';

        $badArray = [$codeBefore1, $codeBefore2];

        $this->assertEquals($codeAfter, Filter::XssFilter($badArray)[1]);
    }

    public function testXssFilterWithAssociativeArrayOfBadCode()
    {
        $codeBefore1 = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeBefore2 = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeAfter = 'Hello &lt;script&gt;var http = new XMLHttpRequest(); http.open(&#039;POST&#039;, &#039;example.com/my_account/delete.php&#039;, true);&lt;/script&gt;';

        $badArray = ['foo' => $codeBefore1, 'bar' => $codeBefore2];
        Filter::XssFilter($badArray);         

        $this->assertEquals($codeAfter, $badArray['foo']);
        $this->assertEquals($codeAfter, $badArray['bar']);
    }
  
    public function testXssFilterWithSimpleObject_byref()
    {
        $codeBefore = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeAfter = 'Hello &lt;script&gt;var http = new XMLHttpRequest(); http.open(&#039;POST&#039;, &#039;example.com/my_account/delete.php&#039;, true);&lt;/script&gt;';
        $integerBefore = 123;
        $integerAfter  = 123;

        $object = new \stdClass();
        $object->int = $integerBefore;
        $object->str = 'foo';
        $object->badstr = $codeBefore;

        Filter::XssFilter($object);         

        $this->assertEquals('foo', $object->str);
        $this->assertEquals($integerAfter, $object->int);
        $this->assertEquals($codeAfter, $object->badstr);
    }

    public function testXssFilterWithSimpleObject_return()
    {
        $codeBefore = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeAfter = 'Hello &lt;script&gt;var http = new XMLHttpRequest(); http.open(&#039;POST&#039;, &#039;example.com/my_account/delete.php&#039;, true);&lt;/script&gt;';
        $integerBefore = 123;
        $integerAfter  = 123;

        $object = new \stdClass();
        $object->str = 'foo';
        $object->badstr = $codeBefore;

        $this->assertEquals($codeAfter, Filter::XssFilter($object)->badstr);
    }

    public function testXssFilterWithObjectContainingArray_byref()
    {
        $codeBefore1 = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeBefore2 = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeAfter = 'Hello &lt;script&gt;var http = new XMLHttpRequest(); http.open(&#039;POST&#039;, &#039;example.com/my_account/delete.php&#039;, true);&lt;/script&gt;';

        $badArray = ['foo' => 'bar', 'bad1' => $codeBefore1, 'bad2' => $codeBefore2];
        $object = new \stdClass();
        $object->badArray = $badArray;

        Filter::XssFilter($object);         

        $this->assertEquals('bar', $object->badArray['foo']);
        $this->assertEquals($codeAfter, $object->badArray['bad1']);
        $this->assertEquals($codeAfter, $object->badArray['bad2']);
    }

    public function testXssFilterWithObjectContainingArray_return()
    {
        $codeBefore = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeAfter = 'Hello &lt;script&gt;var http = new XMLHttpRequest(); http.open(&#039;POST&#039;, &#039;example.com/my_account/delete.php&#039;, true);&lt;/script&gt;';

        $badArray = ['foo' => 'bar', 'bad' => $codeBefore];
        $object = new \stdClass();
        $object->badArray = $badArray;

        $this->assertEquals($codeAfter,  Filter::XssFilter($object)->badArray['bad']);
    }

    public function testXssFilterWithObjectContainingObject_byref()
    {
        $codeBefore1 = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeBefore2 = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeAfter = 'Hello &lt;script&gt;var http = new XMLHttpRequest(); http.open(&#039;POST&#039;, &#039;example.com/my_account/delete.php&#039;, true);&lt;/script&gt;';


        $object = new \stdClass();
        $object->badStr = $codeBefore1;

        $childObject = new \stdClass();
        $childObject->badStr = $codeBefore2;

        $object->badObject = $childObject;

        Filter::XssFilter($object);         

        $this->assertEquals($codeAfter, $object->badStr);
        $this->assertEquals($codeAfter, $object->badObject->badStr);
    }

    public function testXssFilterWithObjectContainingObject_return()
    {
        $codeBefore = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeAfter = 'Hello &lt;script&gt;var http = new XMLHttpRequest(); http.open(&#039;POST&#039;, &#039;example.com/my_account/delete.php&#039;, true);&lt;/script&gt;';

        $object = new \stdClass();
        $childObject = new \stdClass();
        $childObject->badStr = $codeBefore;
        $object->badObject = $childObject;

        $this->assertEquals($codeAfter, Filter::XssFilter($object)->badObject->badStr);
    }


    /**
     * For every type other than strings or arrays, the method should return the untouched passed argument
     */
    public function testXssFilterWithNonStringOrArrayArguments()
    {
        $integerBefore = 123;
        $integerAfter  = 123;
        $arrayBefore   = [1, 2, 3];
        $arrayAfter    = [1, 2, 3];
        $floatsBefore  = 17.001;
        $floatsAfter   = 17.001;
        $null = null;

        Filter::XssFilter($integerBefore);         
        Filter::XssFilter($arrayBefore);         
        Filter::XssFilter($floatsBefore);         
        Filter::XssFilter($null);         

        $this->assertEquals($integerAfter, $integerBefore);
        $this->assertEquals($arrayBefore, $arrayAfter);
        $this->assertEquals($floatsBefore, $floatsAfter);
        $this->assertNull($null);
    }   

     /**
     * For every type other than strings or arrays, the method should return the untouched passed argument
     */
    public function testXssFilterWithNonStringOrArrayArguments_return()
    {
        $integerBefore = 123;
        $integerAfter  = 123;
        $arrayBefore   = [1, 2, 3];
        $arrayAfter    = [1, 2, 3];
        $floatsBefore  = 17.001;
        $floatsAfter   = 17.001;
        $null = null;

        $this->assertEquals($integerAfter,  Filter::XssFilter($integerBefore));
        $this->assertEquals($arrayBefore,  Filter::XssFilter($arrayBefore));
        $this->assertEquals($floatsBefore, Filter::XssFilter($floatsBefore));
        $this->assertNull(Filter::XssFilter($null));
    }   

     /**
     * For every type other than strings or arrays, the method should return the untouched passed argument
     */
    public function testXssFilterWithNonStringOrArrayArguments_byref()
    {
        $integerBefore = 123;
        $integerAfter  = 123;
        $arrayBefore   = [1, 2, 3];
        $arrayAfter    = [1, 2, 3];
        $floatsBefore  = 17.001;
        $floatsAfter   = 17.001;
        $null = null;

        Filter::XssFilter($integerBefore);         
        Filter::XssFilter($arrayBefore);         
        Filter::XssFilter($floatsBefore);         
        Filter::XssFilter($null);         

        $this->assertEquals($integerAfter, $integerBefore);
        $this->assertEquals($arrayBefore, $arrayAfter);
        $this->assertEquals($floatsBefore, $floatsAfter);
        $this->assertNull($null);
    }   

    public function testXssFilterWithComplexArrayOfBadCode()
    {
        $codeBefore1 = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeBefore2 = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeBefore3 = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeBefore4 = "Hello <script>var http = new XMLHttpRequest(); http.open('POST', 'example.com/my_account/delete.php', true);</script>";
        $codeAfter = 'Hello &lt;script&gt;var http = new XMLHttpRequest(); http.open(&#039;POST&#039;, &#039;example.com/my_account/delete.php&#039;, true);&lt;/script&gt;';
        
        $badObject = new \stdClass();
        $badObject->badstr = $codeBefore4;

        $badArray = [ 
            'foo', 
            $codeBefore1, 
            'bar', 
            [
                'foo' => $codeBefore2, 
                'bar' => $codeBefore3
            ],
            $badObject
        ];

        Filter::XssFilter($badArray);         

        $this->assertEquals('foo', $badArray[0]);
        $this->assertEquals($codeAfter, $badArray[1]);
        $this->assertEquals('bar', $badArray[2]);
        $this->assertEquals($codeAfter, $badArray[3]['foo']);
        $this->assertEquals($codeAfter, $badArray[3]['bar']);
        $this->assertEquals($codeAfter, $badArray[4]->badstr);
    }

}
