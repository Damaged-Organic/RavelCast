<?php
// AppBundle/Entity/Traits/ErrorHandlerTrait.php
namespace AppBundle\Entity\Traits;

trait ErrorHandlerTrait
{
    private $error;

    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    public function getError()
    {
        return $this->error;
    }
}