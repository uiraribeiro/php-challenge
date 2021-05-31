<?php


namespace AppBundle\Lib\Stock;


use AppBundle\Entity\Stock;
use AppBundle\Lib\SearchDtoInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StockService
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

    public function stockUp()
    {

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
    protected function getRepository() :string
    {
        return Stock::class;
    }
}