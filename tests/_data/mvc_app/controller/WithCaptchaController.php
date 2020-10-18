<?php
use Kristuff\Miniweb\Mvc\Controller;

class WithCaptchaController extends Controller
{
    public function captchaTest()
    {
        $captcha = \Kristuff\Miniweb\Security\CaptchaModel::captcha();
        $captcha->create('captcha_key');
        $this->session()->set('CAPTCHA_TEST', $captcha->inline());

        //...
    }
}
