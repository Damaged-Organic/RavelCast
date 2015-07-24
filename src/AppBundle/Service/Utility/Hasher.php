<?php
// AppBundle/Service/Utility/Hasher.php
namespace AppBundle\Service\Utility;

class Hasher
{
    public function hashBcrypt($string)
    {
        $options = [
            'cost' => 10,
        ];

        return password_hash((string)$string, PASSWORD_DEFAULT, $options);
    }

    public function verifyHashBcrypt($string, $hash)
    {
        return password_verify($string, $hash);
    }

    public function hashSha($string)
    {
        return hash('sha512', $string);
    }
}
