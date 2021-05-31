<?php

namespace AppBundle\Repository;

use AppBundle\Lib\QueryBuilderHelper;
use AppBundle\Lib\Tools;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ItemRepository
 * @package AppBundle\Repository
 */
class ItemRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param array $filter
     * @param array $sort
     * @return QueryBuilder
     */
    public function search(array $filter = [], array $sort = []) :QueryBuilder
    {
        $qb = $this->createQueryBuilder('i')
            ->leftJoin('i.products', 'p')
            ->leftJoin('p.country', 'l')
            ->leftJoin('p.subscriptions', 'sub')
            ->leftJoin('sub.store', 's');

        $filter_map = [
            'item' => ['i.id'],
            'name' => ['i.name'],
            'product' => ['p.id'],
            'runtime' => ['p.minSubscriptionTime'],
            'store' => ['s.id'],
            'country' => ['l.id'],
            'search' => ['i.name', 'i.description']
        ];

        $qb = QueryBuilderHelper::buildFilters($qb, $filter, $filter_map);



        $sort_map = [
            'id' => ['i.id'],
            'name' => ['i.name'],
            'created_at' => ['i.createdAt']
        ];

        $sort_defaults = [
            'i.name' => 'ASC'
        ];

        return QueryBuilderHelper::buildSort($qb, $sort, $sort_map, $sort_defaults);
    }
}
