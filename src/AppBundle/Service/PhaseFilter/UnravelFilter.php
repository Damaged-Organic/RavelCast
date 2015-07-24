<?php
// AppBundle/Service/PhaseFilter/UnravelFilter.php
namespace AppBundle\Service\PhaseFilter;

use AppBundle\Service\Utility\Hasher,
    AppBundle\Service\Utility\Cipher,
    AppBundle\Service\Utility\Yamler,
    AppBundle\Service\Storage\FlashStorage,
    AppBundle\Entity\Manager\CipherFileManager,
    AppBundle\Entity\StashedDataPackage;

class UnravelFilter
{
    private $_hasher;

    private $_cipher;

    private $_yamler;

    private $_flashStorage;

    private $_cipherFileManager;

    public function setHasher(Hasher $hasher)
    {
        $this->_hasher = $hasher;
    }

    public function setCipher(Cipher $cipher)
    {
        $this->_cipher = $cipher;
    }

    public function setYamler(Yamler $yamler)
    {
        $this->_yamler = $yamler;
    }

    public function setFlashStorage(FlashStorage $flashStorage)
    {
        $this->_flashStorage = $flashStorage;
    }

    public function setCipherFileManager(CipherFileManager $cipherFileManager)
    {
        $this->_cipherFileManager = $cipherFileManager;
    }

    /**
     * Encrypts hash γ same way as during ravel process to find corresponding message
     */
    public function prePhase(StashedDataPackage $stashedDataPackage)
    {
        $stashedDataPackage->setHashGamma(
            $this->_hasher->hashSha($stashedDataPackage->getHashGamma())
        );

        $cipherCredentials = $this->_yamler->readCipherFile($fileName = $stashedDataPackage->getHashGamma());

        if( !$cipherCredentials )
            return $stashedDataPackage->setError('UnravelFilter::prePhase - cipher file not found');

        $this->_flashStorage->rememberCipherFileName($stashedDataPackage->getHashGamma());

        $this->_flashStorage->rememberCipherCredentials($cipherCredentials);

        $stashedDataPackage->setHashGamma(
            $this->_cipher->encrypt($stashedDataPackage->getHashGamma(), $cipherCredentials['key'], $cipherCredentials['iv'])
        );

        return $stashedDataPackage;
    }

    /**
     * Deletes cipher file and database entry
     */
    public function postPhase()
    {
        $fileName = $this->_flashStorage->recallCipherFileName();

        if( !$fileName )
            return FALSE;

        $cipherFile = $this->_cipherFileManager->findByFileName($fileName);

        $this->_cipherFileManager->remove($cipherFile);

        $this->_yamler->deleteCipherFile($fileName);
    }
}
