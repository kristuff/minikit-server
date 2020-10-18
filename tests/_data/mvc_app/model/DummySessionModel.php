<?php
use Kristuff\Miniweb\Mvc\Model;

class DummySessionModel extends Model
{
    
    public static function registerSomethingInSession()
    {
        self::session()->set('data_from_model', 'value_from_model');   
    }


}