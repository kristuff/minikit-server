<?php
use Kristuff\Minikit\Mvc\Controller;

class WithCaptchaController extends Controller
{
    public function captchaTest()
    {
        $captcha = \Kristuff\Minikit\Security\CaptchaModel::captcha();
        $captcha->create('captcha_key');
        $this->session()->set('CAPTCHA_TEST', $captcha->inline());

        //...
    }
}
