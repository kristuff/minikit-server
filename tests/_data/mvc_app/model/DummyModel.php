<?php
use Kristuff\Minikit\Mvc\Model;
use Kristuff\Minikit\Mvc\TaskResponse;


class DummyModel extends Model
{
    public static function configTestMethod()
    {
        $value = self::config('APP_NAME');   
        self::session()->set('data_from_model', $value);   
    }

    public static function localeTestMethod()
    {
        $value = self::text('HELLO');   
        self::session()->set('data_from_model', $value);   
    }

    public static function responseTestMethod()
    {
        $response = TaskResponse::create(409, 'hello :)' , ['the_key' => 'the_value']);
        self::session()->set('data_from_model', $response->toArray()); 
        $response = self::createResponse(200, 'hello2 :)' , ['the_key' => 'the_value2']);
        self::session()->set('data_from_model_2', $response->toArray()); 
    
    }

    public static function requestTestMethod()
    {
        $arg = self::request()->get('test_arg');
        self::session()->set('test_request_in_model', $arg);   
    }

    public static function cookieTestMethod()
    {
        self::cookie()->set('test', 'value');
    }
}