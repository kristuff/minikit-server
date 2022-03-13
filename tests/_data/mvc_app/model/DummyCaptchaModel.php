<?php
use Kristuff\Minikit\Security\CaptchaModel;

class DummyModel extends CaptchaModel
{
    public static function createCaptchaAndGetPhrase($identifer) 
    {
        self::captcha()->create($identifer);
    }
}