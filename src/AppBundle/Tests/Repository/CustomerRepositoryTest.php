<?php


namespace AppBundle\Tests\Repository;


use AppBundle\Entity\Customer;
use AppBundle\Lib\Tools;
use AppBundle\Tests\EntityManagerAwareTestCase;
use Doctrine\ORM\Query\Parameter;

class CustomerRepositoryTest extends EntityManagerAwareTestCase
{

    public static function setUpBeforeClass() :void
    {
        static::bootstrapDatabase();
    }


    public function testSearchWithoutParameters() :void
    {
        $repo = $this->getEntityManager()->getRepository(Customer::class);

        $qb = $repo->search();
        $dql = 'SELECT c FROM AppBundle\Entity\Customer c LEFT JOIN c.stores s LEFT JOIN s.country l ORDER BY c.name ASC';
        static::assertEquals($dql, $qb->getDQL());

        // Check if query compiles
        $qb->getQuery()->getResult();
    }

    public function testSearchForName() :void
    {
        $repo = $this->getEntityManager()->getRepository(Customer::class);

        $filter['name'] = 'customer_1';

        $qb = $repo->search($filter);
        $dql = 'SELECT c FROM AppBundle\Entity\Customer c LEFT JOIN c.stores s LEFT JOIN s.country l WHERE c.name = :name ORDER BY c.name ASC';
        static::assertEquals($dql, $qb->getDQL());
        $parameter = $qb->getParameter('name');
        static::assertInstanceOf(Parameter::class, $parameter);
        static::assertEquals('customer_1', $parameter->getValue());

        // Check if query compiles
        $qb->getQuery()->getResult();
    }

    public function testSearchForCustomer() :void
    {
        $repo = $this->getEntityManager()->getRepository(Customer::class);
        $res = $repo->findOneBy([]);

        $filter['customer'] = $res->getId();
        $qb = $repo->search($filter);
        $dql = 'SELECT c FROM AppBundle\Entity\Customer c LEFT JOIN c.stores s LEFT JOIN s.country l WHERE c.id = :customer ORDER BY c.name ASC';

        static::assertEquals($dql, $qb->getDQL());
        $parameter = $qb->getParameter('customer');
        static::assertInstanceOf(Parameter::class, $parameter);
        static::assertEquals($filter['customer'], $parameter->getValue());

        // Check if query compiles
        $qb->getQuery()->getResult();
    }

    public function testSearchForCountry() :int
    {
        $repo = $this->getEntityManager()->getRepository(Customer::class);

        $filter['country'] = 'CH';
        $qb = $repo->search($filter);
        $qb->addSelect('s');

        $dql = 'SELECT c, s FROM AppBundle\Entity\Customer c LEFT JOIN c.stores s LEFT JOIN s.country l WHERE l.id = :country ORDER BY c.name ASC';

        static::assertEquals($dql, $qb->getDQL());
        $parameter = $qb->getParameter('country');
        static::assertInstanceOf(Parameter::class, $parameter);
        static::assertEquals($filter['country'], $parameter->getValue());

        // Check if query compiles
        $res = $qb->getQuery()->getResult();

        static::assertNotCount(0, $res);

        /**
         * @var Customer $customer
         */
        foreach ($res AS $customer) {
            $stores = $customer->getStores();
            foreach ($stores AS $store) {
                static::assertEquals('CH', $store->getCountry()->getId());
            }
        }

        return $store->getId();
    }

    /**
     * @param $storeId
     * @depends testSearchForCountry
     */
    public function testSearchForStore($storeId) :void
    {
        $repo = $this->getEntityManager()->getRepository(Customer::class);

        $filter['store'] = $storeId;
        $qb = $repo->search($filter);
        $qb->addSelect('s');

        $dql = 'SELECT c, s FROM AppBundle\Entity\Customer c LEFT JOIN c.stores s LEFT JOIN s.country l WHERE s.id = :store ORDER BY c.name ASC';

        static::assertEquals($dql, $qb->getDQL());
        $parameter = $qb->getParameter('store');
        static::assertInstanceOf(Parameter::class, $parameter);
        static::assertEquals($filter['store'], $parameter->getValue());

        // Check if query compiles
        $res = $qb->getQuery()->getResult();

        static::assertCount(1, $res);

        /**
         * @var Customer $customer
         */
        foreach ($res AS $customer) {
            $stores = $customer->getStores();
            foreach ($stores AS $store) {
                static::assertEquals($storeId, $store->getId());
            }
        }
    }

    public function testSearchForText() :void
    {
        $repo = $this->getEntityManager()->getRepository(Customer::class);

        $filter['search'] = 'customer_1';
        $qb = $repo->search($filter);
        $dql = 'SELECT c FROM AppBundle\Entity\Customer c LEFT JOIN c.stores s LEFT JOIN s.country l WHERE c.name LIKE :search OR s.name LIKE :search OR s.description LIKE :search OR l.name LIKE :search ORDER BY c.name ASC';

        static::assertEquals($dql, $qb->getDQL());
        $parameter = $qb->getParameter('search');
        static::assertInstanceOf(Parameter::class, $parameter);
        static::assertEquals('%'.$filter['search'].'%', $parameter->getValue());

        // Check if query compiles
        $qb->getQuery()->getResult();
    }

    public function testSearchForActiveItem() :void
    {
        $repo = $this->getEntityManager()->getRepository(Customer::class);

        $filter['item_active'] = 'early_joe';
        $qb = $repo->search($filter);
        $dql = 'SELECT c FROM AppBundle\Entity\Customer c LEFT JOIN c.stores s LEFT JOIN s.country l LEFT JOIN s.subscriptions sub LEFT JOIN sub.product p LEFT JOIN p.item i WHERE sub.startDate <= :start AND sub.endDate >= :end AND i.name = :item ORDER BY c.name ASC';

        $month = Tools::getFirstAndLastDayOfMonth(new \DateTime(), 'Y-m-d H:i:s');

        static::assertEquals($dql, $qb->getDQL());
        $parameter = $qb->getParameter('item');
        static::assertInstanceOf(Parameter::class, $parameter);
        static::assertEquals($filter['item_active'], $parameter->getValue());

        $parameter = $qb->getParameter('start');
        static::assertInstanceOf(Parameter::class, $parameter);
        static::assertEquals($month['start_date'], $parameter->getValue());

        $parameter = $qb->getParameter('end');
        static::assertInstanceOf(Parameter::class, $parameter);
        static::assertEquals($month['end_date'], $parameter->getValue());

        // Check if query compiles
        $res = $qb->getQuery()->getResult();

        static::assertNotCount(0, $res);
    }
}