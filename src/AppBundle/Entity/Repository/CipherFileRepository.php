<?php
// AppBundle/Entity/Repository/CipherFileRepository.php
namespace AppBundle\Entity\Repository;

use DateTime;

use Doctrine\ORM\EntityRepository;

class CipherFileRepository extends EntityRepository
{
    public function findExpired()
    {
        $query = $this->createQueryBuilder('cipherFile')
            ->select('cipherFile')
            ->where('cipherFile.timeOfDying <= :now')
            ->setParameter('now', new DateTime('NOW'))
            ->getQuery();

        return $query->getResult();
    }
}
