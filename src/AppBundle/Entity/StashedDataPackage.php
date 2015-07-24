<?php
# AppBundle/Entity/StashedDataPackage.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

use AppBundle\Validator\Constraints as CustomAssert,
    AppBundle\Entity\Traits\ErrorHandlerTrait;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\StashedDataPackageRepository")
 * @ORM\Table(name="stashed_data_packages")
 *
 * @ORM\EntityListeners({"AppBundle\EventListener\StashedDataPackagePhase"})
 *
 * @Assert\GroupSequence({"StashedDataPackage", "MethodValidators"})
 */
class StashedDataPackage
{
    use ErrorHandlerTrait;

    const DAY    = 'day';
    const HOUR   = 'hour';
    const MINUTE = 'minute';

    const MAX_TIME_IN_SECONDS = 3628800;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    protected $id;

    /**
     * @Assert\NotBlank(message = "stashed_data_package.time_to_live.measure.not_blank")
     * @Assert\Choice(callback = "getTimeToLiveMeasures")
     */
    protected $timeToLiveMeasure;

    /**
     * @Assert\NotBlank(message="stashed_data_package.time_to_live.number.not_blank")
     */
    protected $timeToLiveNumber;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $timeOfDying;

    /**
     * @ORM\Column(type="string", length=22)
     */
    protected $saltAlpha;

    /**
     * @ORM\Column(type="string", length=22)
     */
    protected $saltBeta;

    /**
     * @ORM\Column(type="binary", length=64)
     *
     * @Assert\NotBlank(message="stashed_data_package.hash_beta.not_blank")
     * @CustomAssert\IsHashConstraint
     */
    protected $hashBeta;

    /**
     * @ORM\Column(type="binary", length=144)
     *
     * @Assert\NotBlank(message="stashed_data_package.hash_gamma.not_blank", groups={"unravel"})
     * @CustomAssert\IsHashConstraint(groups={"unravel"})
     */
    protected $hashGamma;

    /**
     * @ORM\Column(type="blob")
     *
     * @Assert\NotBlank(message="stashed_data_package.data.not_blank")
     * @Assert\Length(
     *      max = 9944,
     *      maxMessage = "stashed_data_package.data.length.max"
     * )
     */
    protected $data;

    //TODO: This field should be out of entity
    /**
     * @Assert\Blank(message="stashed_data_package.blank_field.blank", groups={"unravel"})
     */
    protected $blankField;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function setTimeToLiveMeasure($timeToLiveMeasure)
    {
        $this->timeToLiveMeasure = $timeToLiveMeasure;
    }

    public function getTimeToLiveMeasure()
    {
        return $this->timeToLiveMeasure;
    }

    public static function getTimeToLiveMeasures()
    {
        return ['minute', 'hour', 'day'];
    }

    public function setTimeToLiveNumber($timeToLiveNumber)
    {
        $this->timeToLiveNumber = $timeToLiveNumber;
    }

    public function getTimeToLiveNumber()
    {
        return $this->timeToLiveNumber;
    }

    public function getTimeToLiveInSeconds()
    {
        if( !$this->timeToLiveMeasure || !$this->timeToLiveNumber )
            return FALSE;

        switch( $this->timeToLiveMeasure )
        {
            case self::MINUTE:
                return $this->timeToLiveNumber * 60;
            break;

            case self::HOUR:
                return $this->timeToLiveNumber * 60 * 60;
            break;

            case self::DAY:
                return $this->timeToLiveNumber * 24 * 60 * 60;
            break;

            default:
                return FALSE;
            break;
        }
    }

    /**
     * @Assert\True(message = "stashed_data_package.method.is_time_to_live_number_fits", groups={"MethodValidators"})
     */
    public function isTimeToLiveNumberFits()
    {
        $timeToLiveInSeconds = $this->getTimeToLiveInSeconds();

        if( !$timeToLiveInSeconds )
            return FALSE;

        return ( $timeToLiveInSeconds <= self::MAX_TIME_IN_SECONDS ) ? TRUE : FALSE;
    }

    /**
     * Set timeOfDying
     *
     * @return StashedDataPackage
     */
    public function setTimeOfDying()
    {
        $timeToLiveInSeconds = $this->getTimeToLiveInSeconds();

        if( !$timeToLiveInSeconds )
            return FALSE;

        $dateTime = (new \DateTime('NOW'))
            ->add(new \DateInterval("PT{$timeToLiveInSeconds}S"));

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
     * Set saltAlpha
     *
     * @param string $saltAlpha
     * @return StashedDataPackage
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
     * @return StashedDataPackage
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

    /**
     * Set hashBeta
     *
     * @param binary $hashBeta
     * @return StashedDataPackage
     */
    public function setHashBeta($hashBeta)
    {
        $this->hashBeta = $hashBeta;

        return $this;
    }

    /**
     * Get hashBeta
     *
     * @return binary 
     */
    public function getHashBeta()
    {
        return $this->hashBeta;
    }

    /**
     * Set hashGamma
     *
     * @param binary $hashGamma
     * @return StashedDataPackage
     */
    public function setHashGamma($hashGamma)
    {
        $this->hashGamma = $hashGamma;

        return $this;
    }

    /**
     * Get hashGamma
     *
     * @return binary 
     */
    public function getHashGamma()
    {
        return $this->hashGamma;
    }

    /**
     * Set data
     *
     * @param string $data
     * @return StashedDataPackage
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string 
     */
    public function getData()
    {
        return $this->data;
    }

    public function setBlankField($blankField)
    {
        $this->blankField = $blankField;
    }

    public function getBlankField()
    {
        return $this->blankField;
    }
}