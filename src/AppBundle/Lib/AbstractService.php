<?php


namespace AppBundle\Lib;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractService
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    use LoggerAwareTrait;

    public function __construct(EntityManager $manager, EventDispatcherInterface $eventDispatcher, ValidatorInterface $validator)
    {
        $this->entityManager = $manager;
        $this->dispatcher = $eventDispatcher;
        $this->logger = new NullLogger();
        $this->validator = $validator;
    }

    public function search(SearchDtoInterface $search) :QueryBuilder
    {
        $filter = $search->getFilter()->toArray();
        $sort = $search->getSort()->toArray();

        return $this->getEntityManager()->getRepository($this->getRepository())->search($filter, $sort);
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Name of the entity class
     * @return string
     */
    abstract protected function getRepository() :string;


}