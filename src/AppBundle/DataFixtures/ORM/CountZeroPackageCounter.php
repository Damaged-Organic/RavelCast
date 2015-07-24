<?php
// src/AppBundle/DataFixtures/ORM/CountZeroPackageCounter.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\PackageCounter;

class CountZeroPackageCounter implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $packageCounter = (new PackageCounter)
            ->setSentPackages(0);

        $manager->persist($packageCounter);
        $manager->flush();
    }
}
