<?php
// AppBundle/Entity/Manager/StashedDataPackageManager.php
namespace AppBundle\Entity\Manager;

use Exception;

use AppBundle\Entity\Manager\Contract\AbstractCustomManager,
    AppBundle\Entity\StashedDataPackage;

class StashedDataPackageManager extends AbstractCustomManager
{
    private $clientHashBeta;

    public function persist($stashedDataPackage)
    {
        if( !($stashedDataPackage instanceof StashedDataPackage) )
            return $this->create()
                ->setError('StashedDataPackageManager::persist - object should be an instance of StashedDataPackage');

        try {
            $this->_manager->persist($stashedDataPackage);
            $this->_manager->flush();
        } catch(Exception $EX) {
            $stashedDataPackage->setError('StashedDataPackageManager::persist - Cannot persist entity');
        }

        return $stashedDataPackage;
    }

    public function remove($stashedDataPackage)
    {
        if( !($stashedDataPackage instanceof StashedDataPackage) )
            return $this->create()
                ->setError('StashedDataPackageManager::remove - object should be an instance of StashedDataPackage');

        $this->_manager->remove($stashedDataPackage);
        $this->_manager->flush();
    }

    public function removeExpired()
    {
        $this->_manager->getRepository('AppBundle:StashedDataPackage')
            ->removeExpired();
    }

    public function findAllSalts()
    {
        return $this->_manager->getRepository('AppBundle:StashedDataPackage')
            ->findAllSalts();
    }

    public function findOneById($id)
    {
        $stashedDataPackage = $this->_manager->getRepository('AppBundle:StashedDataPackage')
            ->findOneById($id);

        if( !$stashedDataPackage )
            return $this->create()
                ->setError('StashedDataPackageManager::findOneById - no record found');

        return $stashedDataPackage;
    }

    public function findOneByHashGamma($hashGamma)
    {
        $stashedDataPackage = $this->_manager->getRepository('AppBundle:StashedDataPackage')
            ->findOneBy(['hashGamma' => $hashGamma]);

        if( !$stashedDataPackage )
            return $this->create()
                ->setError('StashedDataPackageManager::findOneByHashGamma - no record found');

        return $stashedDataPackage;
    }

    public function setClientHashBeta($clientHashBeta)
    {
        $this->clientHashBeta = $clientHashBeta;
    }

    public function getClientHashBeta()
    {
        return $this->clientHashBeta;
    }
}
