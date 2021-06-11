<?php declare(strict_types=1);

/** 
 *        _      _            _
 *  _ __ (_)_ _ (_)_ __ _____| |__
 * | '  \| | ' \| \ V  V / -_) '_ \
 * |_|_|_|_|_||_|_|\_/\_/\___|_.__/
 *
 * This file is part of Kristuff\MiniWeb.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @version    0.9.7
 * @copyright  2017-2021 Kristuff
 */

namespace Kristuff\Miniweb\Security;

/**
 * Encryption and Decryption Class
 *
 */
class Encryption
{

    /**
     * Cipher algorithm
     *
     * @var string
     */
    const CIPHER = 'aes-256-cbc';

    /**
     * Hash function
     *
     * @var string
     */
    const HASH_FUNCTION = 'sha256';

    /**
     * Encrypt a string.
     *
     * @access public
     * @static
     * @param string    $plain
     * @param string    $encryptionKey
     * @param string    $salt
     *
     * @return string
     * @throws \Exception If functions don't exists
     */
    public static function encrypt(string $plain, string $encryptionKey, string $salt): string
    {
        if (!function_exists('openssl_cipher_iv_length') ||
            !function_exists('openssl_random_pseudo_bytes') ||
            !function_exists('openssl_encrypt')) {
            throw new \Exception("Encryption function don't exists");
        }

        // generate initialization vector,
        // this will make $iv different every time,
        // so, encrypted string will be also different.
        $iv_size = openssl_cipher_iv_length(self::CIPHER);
        $iv = openssl_random_pseudo_bytes($iv_size);

        // generate key for authentication using ENCRYPTION_KEY & HMAC_SALT
        $key = mb_substr(hash(self::HASH_FUNCTION, $encryptionKey . $salt), 0, 32, '8bit');

        // append initialization vector
        $encrypted_string = openssl_encrypt($plain, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv);
        $ciphertext = $iv . $encrypted_string;

        // apply the HMAC
        $hmac = hash_hmac(self::HASH_FUNCTION, $ciphertext, $key);

        return $hmac . $ciphertext;
    }

    /**
     * Decrypted a string.
     *
     * @access public
     * @static
     * @param string    $ciphertext
     * @param string    $encryptionKey
     * @param string    $salt
     *
     * @return string|false
     * @throws \Exception If $ciphertext is empty, or If functions don't exists
     */
    public static function decrypt(string $ciphertext, string $encryptionKey, string $salt)
    {
        if (empty($ciphertext)) {
            throw new \Exception("the string to decrypt can't be empty");
        }

        if (!function_exists('openssl_cipher_iv_length') ||
            !function_exists('openssl_decrypt')
        ) {
            throw new \Exception("Encryption function don't exists");
        }

        // generate key used for authentication using ENCRYPTION_KEY & HMAC_SALT
        $key = mb_substr(hash(self::HASH_FUNCTION, $encryptionKey . $salt), 0, 32, '8bit');

        // split cipher into: hmac, cipher & iv
        $macSize = 64;
        $hmac       = mb_substr($ciphertext, 0, $macSize, '8bit');
        $iv_cipher  = mb_substr($ciphertext, $macSize, null, '8bit');

        // generate original hmac & compare it with the one in $ciphertext
        // use hash_equals PHP>=5.6
        $originalHmac = hash_hmac(self::HASH_FUNCTION, $iv_cipher, $key);
        if (!hash_equals($hmac, $originalHmac)) {
            return false;
        }

        // split out the initialization vector and cipher
        $iv_size = openssl_cipher_iv_length(self::CIPHER);
        $iv = mb_substr($iv_cipher, 0, $iv_size, '8bit');
        $cipher = mb_substr($iv_cipher, $iv_size, null, '8bit');

        return openssl_decrypt($cipher, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv);
    }
}
