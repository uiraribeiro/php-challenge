<?php

namespace AppBundle\Repository;

use AppBundle\Lib\QueryBuilderHelper;
use AppBundle\Lib\Tools;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * CustomerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CustomerRepository extends EntityRepository
{
    /**
     * @param array $filter
     * @param array $sort
     * @return QueryBuilder
     */
    public function search(array $filter = [], array $sort = []) :QueryBuilder
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.stores', 's')
            ->leftJoin('s.country', 'l');

        $filter_map = [
            'customer' => ['c.id'],
            'name' => ['c.name'],
            'store' => ['s.id'],
            'country' => ['l.id'],
            'search' => ['c.name', 's.name', 's.description', 'l.name']
        ];

        $qb = QueryBuilderHelper::buildFilters($qb, $filter, $filter_map);

        // kind of special filter which will return all customers which have a active subscription
        // for an item.
        if (array_key_exists('item_active', $filter) && !empty($filter['item_active'])) {
            $qb
                ->leftJoin('s.subscriptions', 'sub')
                ->leftJoin('sub.product', 'p')
                ->leftJoin('p.item', 'i');
            $custom_map = [
                'item' => ['i.name']
            ];

            $now = new \DateTime();
            $month = Tools::getFirstAndLastDayOfMonth($now, 'Y-m-d H:i:s');

            $qb->andWhere($qb->expr()->lte('sub.startDate', ':start'))
                ->setParameter(':start', $month['start_date']);
            $qb->andWhere($qb->expr()->gte('sub.endDate', ':end'))
                ->setParameter(':end', $month['end_date']);

            $qb = QueryBuilderHelper::buildFilters($qb, ['item' => $filter['item_active']], $custom_map);
        }

        $sort_map = [
            'id' => ['c.id'],
            'name' => ['c.name'],
            'created_at' => ['c.createdAt']
        ];

        $sort_defaults = [
            'c.name' => 'ASC'
        ];

        return QueryBuilderHelper::buildSort($qb, $sort, $sort_map, $sort_defaults);
    }
}
