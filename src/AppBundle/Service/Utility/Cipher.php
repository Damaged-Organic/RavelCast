<?php
// AppBundle/Service/Utility/Cipher.php
namespace AppBundle\Service\Utility;

class Cipher
{
    const CIPHER = "aes-256-cbc";

    public function encrypt($data, $key, $iv)
    {
        $iv  = substr($iv, -16);
        $key = substr($key, -32);

        return openssl_encrypt($data, self::CIPHER, $key, TRUE, $iv);
    }

    public function decrypt($cipherData, $key, $iv)
    {
        $iv  = substr($iv, -16);
        $key = substr($key, -32);

        return openssl_decrypt($cipherData, self::CIPHER, $key, TRUE, $iv);
    }
}
