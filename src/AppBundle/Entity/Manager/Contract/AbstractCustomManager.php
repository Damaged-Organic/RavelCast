<?php
// AppBundle/Entity/Manager/AbstractCustomManager.php
namespace AppBundle\Entity\Manager\Contract;

use Doctrine\ORM\EntityManager;

abstract class AbstractCustomManager
{
    protected $_manager;

    protected $_entityClass;

    public function __construct(EntityManager $manager, $entityClass)
    {
        $this->_manager     = $manager;
        $this->_entityClass = $entityClass;
    }

    public function create()
    {
        $entityClass = $this->_entityClass;

        return new $entityClass();
    }

    abstract public function persist($entity);

    abstract public function remove($entity);
}