<?php


namespace AppBundle\Tests\Repository;


use AppBundle\Entity\Subscription;
use AppBundle\Lib\Tools;
use AppBundle\Tests\EntityManagerAwareTestCase;

class SubscriptionRepositoryTest extends EntityManagerAwareTestCase
{
    public static function setUpBeforeClass() :void
    {
        static::bootstrapDatabase();
    }

    public function testSearchFilter() :void
    {
        $repo = $this->getEntityManager()->getRepository(Subscription::class);

        $filter= [
            'subscription' => 1,
            'customer' => 1,
            'name' => 'customer_1_store_1',
            'store' => 1,
            'country' => 'DE',
            'runtime' => 12,
            'recurring' => 1,
            'search' => 'test',
            'current' => 1
        ];

        $qb = $repo->search($filter);

        $dql = 'SELECT sub FROM AppBundle\Entity\Subscription sub LEFT JOIN sub.store s LEFT JOIN s.customer c LEFT JOIN sub.product p LEFT JOIN p.item i LEFT JOIN s.country l WHERE sub.id = :subscription AND c.id = :customer AND s.name = :name AND s.id = :store AND l.id = :country AND p.minSubscriptionTime = :runtime AND sub.recurring = :recurring AND (c.name LIKE :search OR s.name LIKE :search OR s.description LIKE :search OR l.name LIKE :search OR i.description LIKE :search) AND sub.startDate <= :start AND sub.endDate >= :end ORDER BY s.name ASC';
        static::assertEquals($dql, $qb->getDQL());

        // Current should have been interpreted as stat and end date
        $skip = ['current'];
        $month = Tools::getFirstAndLastDayOfMonth(new \DateTime(), 'Y-m-d H:i:s');
        $filter['start'] = $month['start_date'];
        $filter['end'] = $month['end_date'];
        static::assertQueryBuilderParametersMatch($qb, $filter, $skip);

        // Check if query compiles
        $qb->getQuery()->getResult();
    }
}