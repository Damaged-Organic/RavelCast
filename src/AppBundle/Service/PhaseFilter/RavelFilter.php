<?php
// AppBundle/Service/PhaseFilter/RavelFilter.php
namespace AppBundle\Service\PhaseFilter;

use AppBundle\Service\Utility\Salter,
    AppBundle\Service\Utility\Yamler,
    AppBundle\Entity\StashedDataPackage,
    AppBundle\Entity\Manager\CipherFileManager,
    AppBundle\Entity\Manager\PackageCounterManager;

class RavelFilter
{
    private $_salter;

    private $_yamler;

    private $_cipherFileManager;

    private $_packageCounterManager;

    public function setSalter(Salter $salter)
    {
        $this->_salter = $salter;
    }

    public function setYamler(Yamler $yamler)
    {
        $this->_yamler = $yamler;
    }

    public function setCipherFileManager(CipherFileManager $cipherFileManager)
    {
        $this->_cipherFileManager = $cipherFileManager;
    }

    public function setPackageCounterManager(PackageCounterManager $packageCounterManager)
    {
        $this->_packageCounterManager = $packageCounterManager;
    }

    /**
     * Generates α, β and γ CSPRNG's required for raveling operations
     */
    public function prePhase($tempSalts, $stashedSalts)
    {
        $saltsBlank = [
            'alpha' => NULL,
            'beta'  => NULL,
            'gamma' => NULL
        ];

        $existingSalts = array_merge(
            $tempSalts,
            $stashedSalts
        );

        $salts = $this->_salter->generateUniqueRequiredSalts(
            $saltsBlank,
            $existingSalts
        );

        return $salts;
    }

    /**
     * Saves file name and TTL for further garbage collection, increments counter of sent packages
     */
    public function postPhase(StashedDataPackage $stashedDataPackage)
    {
        $cipherFile = $this->_cipherFileManager->create();

        $fileName = $this->_yamler->getCipherFileName();

        $cipherFile
            ->setTimeOfDying($stashedDataPackage->getTimeOfDying())
            ->setFileName($fileName);

        $this->_cipherFileManager->persist($cipherFile);

        $this->_packageCounterManager->incrementSentPackages();
    }
}