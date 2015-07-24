<?php
// AppBundle/EventListener/StashedDataPackagePhase.php
namespace AppBundle\EventListener;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\ORM\Event\LifecycleEventArgs;

use AppBundle\Entity\StashedDataPackage,
    AppBundle\Service\Utility\Salter,
    AppBundle\Service\Utility\Hasher,
    AppBundle\Service\Utility\Cipher,
    AppBundle\Service\Utility\Yamler,
    AppBundle\Service\Storage\FlashStorage,
    AppBundle\Entity\Manager\StashedDataPackageManager,
    AppBundle\Entity\Manager\CipherFileManager;

class StashedDataPackagePhase
{
    private $_salter;

    private $_hasher;

    private $_cipher;

    private $_yamler;

    private $_flashStorage;

    private $_stashedDataPackageManager;

    private $_cipherFileManager;

    public function __construct(
        Salter $salter,
        Hasher $hasher,
        Cipher $cipher,
        Yamler $yamler,
        FlashStorage $flashStorage,
        StashedDataPackageManager $stashedDataPackageManager,
        CipherFileManager $cipherFileManager
    ) {
        $this->_salter = $salter;
        $this->_hasher = $hasher;
        $this->_cipher = $cipher;
        $this->_yamler = $yamler;

        $this->_flashStorage = $flashStorage;

        $this->_stashedDataPackageManager = $stashedDataPackageManager;
        $this->_cipherFileManager         = $cipherFileManager;
    }

    /**
     * @ORM\PrePersist
     *
     * Hashing and encrypting before persisting an entity
     */
    public function RavelPhase(StashedDataPackage $stashedDataPackage, LifecycleEventArgs  $event)
    {
        $stashedDataPackage->setTimeOfDying();

        $stashedDataPackage->setData(
            $this->_cipher->encrypt($stashedDataPackage->getData(), $stashedDataPackage->getHashBeta(), $stashedDataPackage->getSaltBeta())
        );

        $stashedDataPackage->setHashBeta(
            $this->_hasher->hashBcrypt($stashedDataPackage->getHashBeta())
        );

        $stashedDataPackage->setHashGamma(
            $this->_hasher->hashSha($stashedDataPackage->getHashGamma())
        );

        //Generate CSPRNG key and IV for cipher
        $cipherCredentials = [
            'key' => $this->_salter->getCSPRNG(32),
            'iv'  => $this->_salter->getCSPRNG(16)
        ];

        //Write key and IV to a file named by sha512() from hash γ
        $this->_yamler->writeCipherFile(
            $fileName = $stashedDataPackage->getHashGamma(),
            $cipherCredentials['key'],
            $cipherCredentials['iv']
        );

        $this->_yamler->setCipherFileName($fileName);

        $stashedDataPackage->setHashBeta(
            $this->_cipher->encrypt($stashedDataPackage->getHashBeta(), $cipherCredentials['key'], $cipherCredentials['iv'])
        );

        $stashedDataPackage->setHashGamma(
            $this->_cipher->encrypt($stashedDataPackage->getHashGamma(), $cipherCredentials['key'], $cipherCredentials['iv'])
        );
    }

    /**
     * @ORM\PostLoad
     *
     * Decrypting data after finding one in DB
     */
    public function UnravelPhase(StashedDataPackage $stashedDataPackage, LifecycleEventArgs $event)
    {
        //Event triggers only if hash β is present in current request
        $clientHashBeta = $this->_stashedDataPackageManager->getClientHashBeta();

        if( $clientHashBeta )
        {
            $cipherCredentials = $this->_flashStorage->recallCipherCredentials();

            $hashServerBeta = $this->_cipher->decrypt(
                stream_get_contents($stashedDataPackage->getHashBeta()), $cipherCredentials['key'], $cipherCredentials['iv']
            );

            if( $this->_hasher->verifyHashBcrypt($clientHashBeta, $hashServerBeta) )
            {
                $cipherData = stream_get_contents($stashedDataPackage->getData());

                $stashedDataPackage->setData(
                    $this->_cipher->decrypt($cipherData, $clientHashBeta, $stashedDataPackage->getSaltBeta())
                );
            } else {
                $stashedDataPackage->setError("StashedDataPackagePhase::UnravelPhase - no record found");
            }
        }
    }
}
