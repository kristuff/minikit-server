<?php

namespace Kristuff\Minikit\Tests\Security;

use Kristuff\Minikit\Security\Encryption;

class EncryptionTest extends \PHPUnit\Framework\TestCase
{

    public function testEncryptDecrypt()
    {
        $encryptionKey = 'hé#use_Another@Key_122^|^';
        $salt          = 'hé#use_Another@Salt_123^|^';
        $plaintext      = 'This is a message.' ;    

        $encrypted = Encryption::encrypt($plaintext, $encryptionKey, $salt);
        $decrypted = Encryption::decrypt($encrypted, $encryptionKey, $salt);

        $this->assertEquals($plaintext, $decrypted);
    }
}