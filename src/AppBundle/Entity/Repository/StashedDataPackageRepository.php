<?php
// AppBundle/Entity/Repository/StashedDataPackageRepository.php
namespace AppBundle\Entity\Repository;

use DateTime;

use Doctrine\ORM\EntityRepository;

use AppBundle\Entity\Traits\DataPackageTrait;

class StashedDataPackageRepository extends EntityRepository
{
    use DataPackageTrait;

    public function findAllSalts()
    {
        $query = $this->createQueryBuilder('stashedDataPackage')
            ->select(["PARTIAL stashedDataPackage.{id, saltAlpha, saltBeta}"])
            ->getQuery();

        return $this->flattenSaltArrayResult($query->getArrayResult());
    }

    public function removeExpired()
    {
        $query = $this->createQueryBuilder('stashedDataPackage')
            ->delete()
            ->where('stashedDataPackage.timeOfDying <= :now')
            ->setParameter('now', new DateTime('NOW'))
            ->getQuery();

        return $query->execute();
    }
}
