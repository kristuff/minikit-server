<?php
use Kristuff\Minikit\Mvc\Model;

class DummyFeedbackModel extends Model
{
    
    public static function registerSomeErrors()
    {
        self::feedback()->addNegative('something is wrong');   
        self::feedback()->addNegative('something is wrong again');   
    }

    public static function registerSomeMessages()
    {
        self::feedback()->addPositive('hello');   
        self::feedback()->addPositive('hello again');   
    }

    public static function registerSomeMessages_direct()
    {
        self::feedback('hello direct');   
        self::feedback('hello direct again');   
    }

    public static function registerSomeErrors_direct()
    {
        self::feedback('something is wrong direct', false);   
        self::feedback('something is wrong direct again', false);   
    }


}