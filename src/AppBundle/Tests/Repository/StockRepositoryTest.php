<?php


namespace AppBundle\Tests\Repository;


use AppBundle\Entity\Stock;
use AppBundle\Tests\EntityManagerAwareTestCase;

class StockRepositoryTest extends EntityManagerAwareTestCase
{
    public static function setUpBeforeClass() :void
    {
        static::bootstrapDatabase();
    }

    public function testSearch() :void
    {
        $repo = $this->getEntityManager()->getRepository(Stock::class);

        $filter = [
            'country' => ['DE', 'AT', 'CH'],
            'item' => [1,2,3],
            'at_date' => new \DateTime(),
            'quantity' => 100,
            'only_current' => 1,
            'year' => '2021',
            'month' => 5
        ];

        $qb = $repo->search($filter);

        $sql = $qb->getSQL();

        static::assertEquals("SELECT s.*, IF(SUM(sm.quantity) > 0, SUM(sm.quantity), 0) AS total_ordered, COUNT(sm.id) AS total_orders FROM stock s LEFT JOIN shipments sm ON s.item_id = sm.item_id AND s.country_id = sm.country_id AND s.at_date = sm.shipping_date WHERE (sm.state IN (:state)) AND ((s.country_id = :country_0) OR (s.country_id = :country_1) OR (s.country_id = :country_2)) AND ((s.item_id = :item_0) OR (s.item_id = :item_1) OR (s.item_id = :item_2)) AND (s.at_date = :at_date) AND (s.quantity = :quantity) AND (YEAR(s.at_date) = :year) AND (MONTH(s.at_date) = :month) AND (s.at_date >= :from) GROUP BY s.id ORDER BY s.at_date ASC", $qb->getSQL());
        $qb->execute()->fetchAll();

    }
}