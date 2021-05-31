<?php


namespace AppBundle\Tests\Repository;


use AppBundle\Entity\Store;
use AppBundle\Tests\EntityManagerAwareTestCase;

class StoreRepositoryTest extends EntityManagerAwareTestCase
{
    public static function setUpBeforeClass() :void
    {
        static::bootstrapDatabase();
    }

    public function testSearchFilter() :void
    {
        $repo = $this->getEntityManager()->getRepository(Store::class);

        $filter = [
            'customer' => 1,
            'store' => 1,
            'name' => 'customer_1_store_1',
            'country' => 'DE',
            'runtime' => 12,
            'recurring' => 1,
            'search' => 'test'
        ];

        $qb = $repo->search($filter);

        $dql = 'SELECT s FROM AppBundle\Entity\Store s LEFT JOIN s.customer c LEFT JOIN s.country l LEFT JOIN s.subscriptions sub LEFT JOIN sub.product p WHERE c.id = :customer AND s.id = :store AND s.name = :name AND l.id = :country AND p.minSubscriptionTime = :runtime AND sub.recurring = :recurring AND (c.name LIKE :search OR s.name LIKE :search OR s.description LIKE :search OR l.name LIKE :search) ORDER BY s.name ASC';
        static::assertEquals($dql, $qb->getDQL());

        static::assertQueryBuilderParametersMatch($qb, $filter);

        // Check if query compiles
        $qb->getQuery()->getResult();
    }
}