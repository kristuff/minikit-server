# Minikit Server

[![Build Status](https://www.travis-ci.com/kristuff/minikit-server.svg?branch=main)](https://www.travis-ci.com/kristuff/minikit-server)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kristuff/minikit-server/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/kristuff/minikit-server/?branch=main)
[![codecov](https://codecov.io/gh/kristuff/minikit-server/branch/main/graph/badge.svg?token=8e9XoS8HnV)](https://codecov.io/gh/kristuff/minikit-server)
[![Latest Stable Version](https://poser.pugx.org/kristuff/minikit-server/v/stable)](https://packagist.org/packages/kristuff/minikit-server)
[![License](https://poser.pugx.org/kristuff/minikit-server/license)](https://packagist.org/packages/kristuff/minikit-server)

> Mini PHP web Framework.

Features
--------

- MVC Rooter system with rewrite url
- Http helper class: Cookie, Redirect, Request, Session, Server, Response
- Misc: Localization, Encryption, Token, Feed creator
- Auth: Login, recovery, registration and invitation

Requirements
------------

- PHP >= 7.3
- activated mod_rewrite on your server

Dependencies
----------

- Mailer extension is using [phpmailer/phpmailer](https://github.com/PHPMailer/PHPMailer)
- Database extension is using [kristuff/patabase](https://github.com/kristuff/patabase)

License
-------

The MIT License (MIT)

Copyright (c) 2017-2022 Kristuff

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
