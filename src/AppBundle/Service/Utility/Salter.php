<?php
// AppBundle/Service/Utility/Salter.php
namespace AppBundle\Service\Utility;

class Salter
{
    const BASE64_STANDARD = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    const BASE64_BCRYPT   = "./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    public function getCSPRNG($length)
    {
        return openssl_random_pseudo_bytes($length);
    }

    public function generateBcryptSalt()
    {
        $base64BcryptSalt = strtr(
            base64_encode($this->getCSPRNG(16)),
            self::BASE64_STANDARD,
            self::BASE64_BCRYPT
        );

        return substr($base64BcryptSalt, 0, 22);
    }

    public function generateUniqueBcryptSalt(array $existingSalts)
    {
        $salt = $this->generateBcryptSalt();

        if( in_array($salt, $existingSalts) ) {
            $existingSalts[] = $salt;
            return $this->generateUniqueBcryptSalt($existingSalts);
        } else {
            return $salt;
        }
    }

    public function generateUniqueRequiredSalts(array $requiredSalts, array $existingSalts)
    {
        foreach($requiredSalts as &$salt) {
            $salt = $this->generateUniqueBcryptSalt($existingSalts);
        }

        return $requiredSalts;
    }
}
