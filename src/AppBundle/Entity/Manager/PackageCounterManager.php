<?php
// AppBundle/Entity/Manager/PackageCounterManager.php
namespace AppBundle\Entity\Manager;

use Exception;

use AppBundle\Entity\Manager\Contract\AbstractCustomManager,
    AppBundle\Entity\PackageCounter;

class PackageCounterManager extends AbstractCustomManager
{
    public function persist($packageCounter)
    {
        if( !($packageCounter instanceof PackageCounter) )
            return $this->create()
                ->setError("PackageCounterManager::persist - object should be an instance of PackageCounter");

        try {
            $this->_manager->persist($packageCounter);
            $this->_manager->flush();
        } catch(Exception $EX) {
            $packageCounter
                ->setError("PackageCounterManager::persist - Cannot persist entity");
        }

        return $packageCounter;
    }

    public function remove($packageCounter)
    {
        if( !($packageCounter instanceof PackageCounter) )
            return $this->create()
                ->setError('PackageCounterManager::remove - object should be an instance of PackageCounter');

        $this->_manager->remove($packageCounter);
        $this->_manager->flush();
    }

    public function getSentPackages()
    {
        return $this->_manager->getRepository('AppBundle:PackageCounter')
            ->getSentPackages();
    }

    public function incrementSentPackages()
    {
        return $this->_manager->getRepository('AppBundle:PackageCounter')
            ->incrementSentPackages();
    }
}
