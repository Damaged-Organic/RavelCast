<?php
// AppBundle/Entity/Repository/PackageCounterRepository.php
namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class PackageCounterRepository extends EntityRepository
{
    public function getSentPackages()
    {
        $query = $this->createQueryBuilder('packageCounter')
            ->select('packageCounter.sentPackages')
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    public function incrementSentPackages()
    {
        $query = $this->createQueryBuilder('packageCounter')
            ->update()
            ->set('packageCounter.sentPackages', 'packageCounter.sentPackages + 1')
            ->getQuery();

        return $query->execute();
    }
}
