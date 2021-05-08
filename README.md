# Miniweb

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kristuff/miniweb/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/kristuff/miniweb/?branch=main)
[![Build Status](https://travis-ci.org/kristuff/miniweb.svg?branch=main)](https://travis-ci.org/kristuff/miniweb)
[![codecov](https://codecov.io/gh/kristuff/miniweb/branch/main/graph/badge.svg?token=8e9XoS8HnV)](https://codecov.io/gh/kristuff/miniweb)
[![Latest Stable Version](https://poser.pugx.org/kristuff/miniweb/v/stable)](https://packagist.org/packages/kristuff/miniweb)
[![License](https://poser.pugx.org/kristuff/miniweb/license)](https://packagist.org/packages/kristuff/miniweb)

> Mini PHP web Framework.

Features
--------

- MVC Rooter system with rewrite url
- Http helper class: Cookie, Redirect, Request, Session, Server, Response
- Misc: Localization, Encryption, Token
- Auth: Login, recovery, registration and invitation

Requirements
------------

- PHP >= 7.3
- activated mod_rewrite on your server

Dependencies
----------

- Captcha extension is using [Gregwar/Captcha](https://github.com/Gregwar/Captcha)
- Mailer extension is using [phpmailer/phpmailer](https://github.com/PHPMailer/PHPMailer)
- Database extension is using [kristuff/patabase](https://github.com/kristuff/patabase)

License
-------

The MIT License (MIT)

Copyright (c) 2017-2021 Kristuff

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
