<?php
// AppBundle/Entity/TempDataPackage.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Traits\ErrorHandlerTrait;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\TempDataPackageRepository")
 * @ORM\Table(name="temp_data_packages", options={"engine":"MEMORY"})
 */
class TempDataPackage
{
    use ErrorHandlerTrait;

    const MAX_TIME_IN_SECONDS = 10;

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
     * @ORM\Column(type="string", length=40, unique=true)
     */
    protected $sessionId;

    /**
     * @ORM\Column(type="string", length=22)
     */
    protected $saltAlpha;

    /**
     * @ORM\Column(type="string", length=22)
     */
    protected $saltBeta;

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
     * @return TempDataPackage
     */
    public function setTimeOfDying()
    {
        $dateTime = (new \DateTime('NOW'))
            ->add(new \DateInterval("PT" . self::MAX_TIME_IN_SECONDS . "S"));

        $this->timeOfDying = $dateTime;

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
     * Set sessionId
     *
     * @param string $sessionId
     * @return TempDataPackage
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionId
     *
     * @return string 
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set saltAlpha
     *
     * @param string $saltAlpha
     * @return TempDataPackage
     */
    public function setSaltAlpha($saltAlpha)
    {
        $this->saltAlpha = $saltAlpha;

        return $this;
    }

    /**
     * Get saltAlpha
     *
     * @return string 
     */
    public function getSaltAlpha()
    {
        return $this->saltAlpha;
    }

    /**
     * Set saltBeta
     *
     * @param string $saltBeta
     * @return TempDataPackage
     */
    public function setSaltBeta($saltBeta)
    {
        $this->saltBeta = $saltBeta;

        return $this;
    }

    /**
     * Get saltBeta
     *
     * @return string 
     */
    public function getSaltBeta()
    {
        return $this->saltBeta;
    }
}
