<?php
// src/AppBundle/Service/Validator/BrowsersVersions.php
namespace AppBundle\Service\Validator;

class BrowsersVersions
{
    public function isLesserIE($httpUserAgent)
    {
        $lesserIEPattern = "/(?i)msie [2-8]/";

        return ( preg_match($lesserIEPattern, $httpUserAgent) );
    }
}