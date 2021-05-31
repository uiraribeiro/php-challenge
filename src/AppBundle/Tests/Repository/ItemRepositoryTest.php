<?php


namespace AppBundle\Tests\Repository;


use AppBundle\Entity\Item;
use AppBundle\Tests\EntityManagerAwareTestCase;

class ItemRepositoryTest extends EntityManagerAwareTestCase
{
    public static function setUpBeforeClass() :void
    {
        static::bootstrapDatabase();
    }

    public function testSearchFilter() :void
    {
        $repo = $this->getEntityManager()->getRepository(Item::class);

        $filter = [
            'item' => 1,
            'name' => 'boskop',
            'product' => 1,
            'runtime' => 12,
            'store' => 1,
            'country' => 'DE',
            'search' => 'test'
        ];

        $qb = $repo->search($filter);

        $dql = 'SELECT i FROM AppBundle\Entity\Item i LEFT JOIN i.products p LEFT JOIN p.country l LEFT JOIN p.subscriptions sub LEFT JOIN sub.store s WHERE i.id = :item AND i.name = :name AND p.id = :product AND p.minSubscriptionTime = :runtime AND s.id = :store AND l.id = :country AND (i.name LIKE :search OR i.description LIKE :search) ORDER BY i.name ASC';
        static::assertEquals($dql, $qb->getDQL());

        static::assertQueryBuilderParametersMatch($qb, $filter);

        // Check if query compiles
        $qb->getQuery()->getResult();
    }
}