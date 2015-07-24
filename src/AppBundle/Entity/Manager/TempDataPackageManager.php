<?php
// AppBundle/Entity/Manager/TempDataPackageManager.php
namespace AppBundle\Entity\Manager;

use Exception;

use AppBundle\Entity\Manager\Contract\AbstractCustomManager,
    AppBundle\Entity\TempDataPackage;

class TempDataPackageManager extends AbstractCustomManager
{
    public function persist($tempDataPackage)
    {
        if( !($tempDataPackage instanceof TempDataPackage) )
            return $this->create()
                ->setError('TempDataPackageManager::persist - object should be an instance of TempDataPackage');

        try {
            $this->_manager->getRepository('AppBundle:TempDataPackage')
                ->persist($tempDataPackage);
        } catch(Exception $EX) {
            $tempDataPackage
                ->setError('TempDataPackageManager::persist - Cannot persist entity');
        }

        return $tempDataPackage;
    }

    public function remove($tempDataPackage)
    {
        if( !($tempDataPackage instanceof TempDataPackage) )
            return $this->create()
                ->setError('TempDataPackageManager::remove - object should be an instance of TempDataPackage');

        $this->_manager->remove($tempDataPackage);
        $this->_manager->flush();
    }

    public function removeHanged()
    {
        $this->_manager->getRepository('AppBundle:TempDataPackage')
            ->removeHanged();
    }

    public function findAllSalts()
    {
        return $this->_manager->getRepository('AppBundle:TempDataPackage')
            ->findAllSalts();
    }

    public function findCurrentUserSalts($sessionId)
    {
        return $this->_manager->getRepository('AppBundle:TempDataPackage')
            ->findCurrentUserSalts($sessionId);
    }
}
