<?php
// AppBundle/Service/Storage/FlashStorage.php
namespace AppBundle\Service\Storage;

use Symfony\Component\HttpFoundation\Session\Session;

class FlashStorage
{
    const PHASE_NAME              = "phase_name";
    const HONEY_POT               = "honey_pot";
    const STASHED_DATA_PACKAGE_ID = "stashed_data_package_id";
    const CIPHER_FILE_NAME        = "cipher_file_name";
    const CIPHER_CREDENTIALS      = "cipher_credentials";

    private $_session;

    public function setSession(Session $session)
    {
        $this->_session = $session;
    }

    public function rememberPhase($phaseName)
    {
        $this->_session->getFlashBag()->get(self::PHASE_NAME);
        $this->_session->getFlashBag()->add(self::PHASE_NAME, $phaseName);
    }

    public function recallPhase($phaseName)
    {
        if( !$this->_session->getFlashBag()->has(self::PHASE_NAME) )
            return FALSE;

        return ( $this->_session->getFlashBag()->get(self::PHASE_NAME)[0] === $phaseName ) ? TRUE : FALSE;
    }

    public function rememberHoneyPot($honeyPot)
    {
        $this->_session->getFlashBag()->get(self::HONEY_POT);
        $this->_session->getFlashBag()->add(self::HONEY_POT, $honeyPot);
    }

    public function recallHoneyPot()
    {
        return ( $this->_session->getFlashBag()->has(self::HONEY_POT) )
            ? $this->_session->getFlashBag()->get(self::HONEY_POT)[0]
            : FALSE;
    }

    public function rememberStashedDataPackageId($id)
    {
        $this->_session->getFlashBag()->get(self::STASHED_DATA_PACKAGE_ID);
        $this->_session->getFlashBag()->add(self::STASHED_DATA_PACKAGE_ID, $id);
    }

    public function recallStashedDataPackageId()
    {
        return ( $this->_session->getFlashBag()->has(self::STASHED_DATA_PACKAGE_ID) )
            ? $this->_session->getFlashBag()->get(self::STASHED_DATA_PACKAGE_ID)[0]
            : NULL;
    }

    public function rememberCipherFileName($fileName)
    {
        $this->_session->getFlashBag()->get(self::CIPHER_FILE_NAME);
        $this->_session->getFlashBag()->add(self::CIPHER_FILE_NAME, $fileName);
    }

    public function recallCipherFileName()
    {
        return ( $this->_session->getFlashBag()->has(self::CIPHER_FILE_NAME) )
            ? $this->_session->getFlashBag()->get(self::CIPHER_FILE_NAME)[0]
            : NULL;
    }

    public function rememberCipherCredentials($cipherCredentials)
    {
        $this->_session->getFlashBag()->get(self::CIPHER_CREDENTIALS);
        $this->_session->getFlashBag()->add(self::CIPHER_CREDENTIALS, $cipherCredentials);
    }

    public function recallCipherCredentials()
    {
        return ( $this->_session->getFlashBag()->has(self::CIPHER_CREDENTIALS) )
            ? $this->_session->getFlashBag()->get(self::CIPHER_CREDENTIALS)[0]
            : NULL;
    }
}
