<?php
# AppBundle/Entity/Repository/TempDataPackageRepository.php
namespace AppBundle\Entity\Repository;

use DateTime;

use Doctrine\ORM\EntityRepository;

use AppBundle\Entity\Traits\DataPackageTrait,
    AppBundle\Entity\TempDataPackage;

class TempDataPackageRepository extends EntityRepository
{
    use DataPackageTrait;

    public function findAllSalts()
    {
        $query = $this->createQueryBuilder('tempDataPackage')
            ->select(["PARTIAL tempDataPackage.{id, saltAlpha, saltBeta}"])
            ->getQuery();

        return $this->flattenSaltArrayResult($query->getArrayResult());
    }

    public function findCurrentUserSalts($sessionId)
    {
        $query = $this->createQueryBuilder('tempDataPackage')
            ->select('tempDataPackage')
            ->where('tempDataPackage.sessionId = :sessionId')
            ->setParameter('sessionId', $sessionId)
            ->getQuery();

        return $query->getSingleResult();
    }

    public function persist(TempDataPackage $tempDataPackage)
    {
        $query = $this->getEntityManager()->getConnection()->prepare("
            INSERT INTO temp_data_packages (
                timeOfDying,
                sessionId,
                saltAlpha,
                saltBeta
            )
            VALUES (
                :timeOfDying,
                :sessionId,
                :saltAlpha,
                :saltBeta
            )
            ON DUPLICATE KEY UPDATE
                timeOfDying = VALUES(timeOfDying),
                saltAlpha   = VALUES(saltAlpha),
                saltBeta    = VALUES(saltBeta)
        ");

        $query->execute([
            'timeOfDying' => $tempDataPackage->getTimeOfDying()->format('Y-m-d H:i:s'),
            'sessionId'   => $tempDataPackage->getSessionId(),
            'saltAlpha'   => $tempDataPackage->getSaltAlpha(),
            'saltBeta'    => $tempDataPackage->getSaltBeta()
        ]);
    }

    public function removeHanged()
    {
        $query = $this->createQueryBuilder('tempDataPackage')
            ->delete()
            ->where('tempDataPackage.timeOfDying <= :now')
            ->setParameter('now', new DateTime('NOW'))
            ->getQuery();

        return $query->execute();
    }
}
