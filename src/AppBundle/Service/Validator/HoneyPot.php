<?php
// AppBundle/Service/Validator/Validator.php
namespace AppBundle\Service\Validator;

use AppBundle\Service\Storage\FlashStorage;

class HoneyPot
{
    const MIN_SECONDS_TO_REQUEST = 0;

    private $_flashStorage;

    public function setFlashStorage(FlashStorage $flashStorage)
    {
        $this->_flashStorage = $flashStorage;
    }

    public function createHoneyPot()
    {
        $honeyPot = uniqid(mt_rand(), TRUE);

        $this->_flashStorage->rememberHoneyPot($honeyPot);

        return $honeyPot;
    }

    public function verifyHoneyPot($userHoneyPot)
    {
        $honeyPot = $this->_flashStorage->recallHoneyPot();

        return ( !empty($honeyPot) && ($honeyPot === $userHoneyPot) ) ? TRUE : FALSE;
    }
}
