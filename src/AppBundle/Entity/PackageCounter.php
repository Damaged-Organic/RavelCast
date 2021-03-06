<?php
// AppBundle/Entity/PackageCounter.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\PackageCounterRepository")
 * @ORM\Table(name="package_counter")
 */
class PackageCounter
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     */
    protected $sentPackages;

    /**
     * Set sentPackages
     *
     * @param integer $sentPackages
     * @return PackageCounter
     */
    public function setSentPackages($sentPackages)
    {
        $this->sentPackages = $sentPackages;

        return $this;
    }

    /**
     * Get sentPackages
     *
     * @return integer 
     */
    public function getSentPackages()
    {
        return $this->sentPackages;
    }
}
