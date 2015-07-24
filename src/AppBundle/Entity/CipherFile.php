<?php
// AppBundle/Entity/CipherFile.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Traits\ErrorHandlerTrait;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\CipherFileRepository")
 * @ORM\Table(name="cipher_files")
 */
class CipherFile
{
    use ErrorHandlerTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $timeOfDying;

    /**
     * @ORM\Column(type="string", length=128)
     */
    protected $fileName;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set timeOfDying
     *
     * @param \DateTime $timeOfDying
     * @return CipherFile
     */
    public function setTimeOfDying($timeOfDying)
    {
        $this->timeOfDying = $timeOfDying;

        return $this;
    }

    /**
     * Get timeOfDying
     *
     * @return \DateTime 
     */
    public function getTimeOfDying()
    {
        return $this->timeOfDying;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     * @return CipherFile
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string 
     */
    public function getFileName()
    {
        return $this->fileName;
    }
}
