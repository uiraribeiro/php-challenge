<?php


namespace AppBundle\Lib\Stock;


use AppBundle\Entity\Country;
use AppBundle\Entity\Item;
use AppBundle\Entity\Stock;
use AppBundle\Lib\SearchDtoInterface;
use AppBundle\Lib\Stock\Form\StockUpRequestDto;
use AppBundle\Lib\Tools;
use AppBundle\Lib\ValidationException;
use AppBundle\Repository\StockRepository;
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

    /**
     * @param SearchDtoInterface $search
     * @return QueryBuilder
     */
    public function search(SearchDtoInterface $search) :QueryBuilder
    {
        $filter = $search->getFilter()->toArray();
        $sort = $search->getSort()->toArray();

        return $this->getEntityManager()->getRepository($this->getRepository())->search($filter, $sort);
    }

    /**
     * @param Item $item
     * @param Country|null $country
     * @param int $month
     * @return StockCollection
     * @throws \Exception
     */
    public function getCurrentStockForItem(Item $item, ?Country $country = null, int $month = 3)
    {
        $from = Tools::getFirstDayDateTimeForMonth(new \DateTime());
        $until = clone $from;
        $until->add(new \DateInterval('P'.$month.'M'));
        $until = Tools::getLastDayDateTimeForMonth($until);

        $filter = [
            'item' => $item->getId(),
            'from' => $from,
            'until' => $until,
        ];

        if ($country instanceof Country) {
            $filter['country'] = $country->getId();
        }

        $res = $this->getEntityManager()->getRepository($this->getRepository())->search($filter, [])->execute()->fetchAll();

        return new StockCollection($res);
    }

    /**
     * @param StockUpRequestDto $request
     * @throws ValidationException
     */
    public function stockUp(StockUpRequestDto $request) :void
    {
        $errors = $this->getValidator()->validate($request);
        if (0 !== count($errors)) {
            throw new ValidationException('Stockup request not valid', $errors);
        }
        /**
         * @var StockRepository $repo
         */
        $repo = $this->getEntityManager()->getRepository($this->getRepository());

        $item = $request->getItem();
        $quantity = $request->getQuantity();
        /**
         * @var \DateTime $from
         * @var \DateTime $until
         */
        $from = $request->getFrom();
        $until = $request->getUntil();
        $mode = $request->getMode();
        foreach ($request->getCountries() AS $country) {
           $repo->stockUp($item->getId(), $country, $quantity, $from, $until, $mode);
           $this->getLogger()->info('Updated stock for item: '.$item->getId().' and country: '.$country);
        }

        $event = new StockUpPerformedEvent($request);
        $this->getDispatcher()->dispatch(StockUpPerformedEvent::NAME, $event);

        return;
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