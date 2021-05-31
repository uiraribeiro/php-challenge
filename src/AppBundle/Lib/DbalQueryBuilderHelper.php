<?php


namespace AppBundle\Lib;

use Doctrine\Dbal\Query\QueryBuilder;

/**
 *
 * @package AppBundle\Repository
 */
class DbalQueryBuilderHelper
{
    public static function buildFilters(QueryBuilder $qb, array $filter, array $supported, bool $exact = false, array $allowEmptyValues = []) :QueryBuilder
    {
        $cls = new static();

        return $cls->buildQueryBuilderQuery($qb, $filter, $supported, $exact, $allowEmptyValues);
    }

    /**
     * Uses the Doctrine Query builder to process the given filters and maps
     * them to fields
     *
     * @param QueryBuilder $queryBuilder
     * @param array $filter
     * @param array $mapping
     * @param bool $exact
     * @param array $allowEmptyValues
     * @return QueryBuilder
     */
    public function buildQueryBuilderQuery(QueryBuilder $queryBuilder,
                                           array $filter = [],
                                           array $mapping = [],
                                           bool $exact = false,
                                           array $allowEmptyValues = []
    ) :QueryBuilder
    {

        $filter = $this->preProcessFilters($filter, $mapping, $allowEmptyValues);

        if (0 === count($filter) || 0 === count($mapping)) {
            return $queryBuilder;
        }

        foreach ($filter AS $name => $value) {
            $fields = $mapping[$name];

            if ($name === 'search') {
                $this->processSearchFilter($queryBuilder, $fields, $value, $exact);

                continue;
            }

            if (is_array($value)) {
                $this->processArrayValueFilter($queryBuilder, $name, $fields, $value);

                continue;
            }

            $this->processStandardFilter($queryBuilder, $name, $fields, $value);
        }

        return $queryBuilder;
    }

    /**
     * Processes the given filters and strips all unsupported entries and empty values unless they are in
     * $allowEmptyValues.
     *
     * @param array $filters
     * @param array $supported
     * @param array $allowEmptyValues
     * @return array
     */
    public function preProcessFilters(array $filters, array $supported, array $allowEmptyValues) :array
    {
        $supported = array_keys($supported);

        $f = static function ($v, $k) use ($supported, $allowEmptyValues) {
            if (!in_array($k, $supported, true)) {
                return false;
            }

            if (is_array($v)) {
                return 0 !== count($v);
            }

            if ($v instanceof \DateTime) {
                return true;
            }

            $v = str_replace(['*', '%'], '', $v);
            $v = self::stripOperatorFromString($v);

            return !((null === $v || $v === '') && !in_array($k, $allowEmptyValues, true));
        };

        return array_filter($filters, $f, ARRAY_FILTER_USE_BOTH);
    }

    public function processStandardFilter(QueryBuilder $qb, $name, $fields, $values) :QueryBuilder
    {
        $op = self::getOperatorFromString($values);
        $values = self::stripOperatorFromString($values);

        if ($values instanceof \DateTime) {
            $values = $values->format('Y-m-d H:i:s');
        }

        if (is_array($fields)) {
            if (0 === count($fields)) {
                return $qb;
            }

            $orx = $qb->expr()->orX();

            foreach ($fields AS $field) {
                $orx->add($qb->expr()->comparison($field, $op, ':' . $name));
            }

            $qb->setParameter($name, $values);
            $qb->andWhere($orx);

            return $qb;
        }

        $qb->andWhere($qb->expr()->comparison($fields, $op, ':'.$name));
        $qb->setParameter($name, $values);

        return $qb;
    }

    public function processArrayValueFilter(QueryBuilder $qb, $name, $fields, $values) :QueryBuilder
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }

        $orx = $qb->expr()->orX();
        foreach ($fields AS $field) {
            $idx = 0;
            foreach ($values AS $value) {
                $n = $name.'_'.$idx;
                $orx->add($qb->expr()->eq($field, ':' . $n));
                $qb->setParameter($n, $value);
                $idx++;
            }
        }

        $qb->andWhere($orx);

        return $qb;

    }

    public function processSearchFilter(QueryBuilder $qb, $fields, $values, $exact = false) :QueryBuilder
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }

        $orx = $qb->expr()->orX();
        if (is_array($values)) {
            $idx = 0;

            foreach ($values as $val) {
                $val = self::stripOperatorFromString($val);

                if (!$exact) {
                    $val = str_replace('*', '%', $val);
                    $val = str_replace('%%', '%', '%' . $val . '%');
                }

                foreach ($fields as $field) {
                    if (!$exact) {
                        $orx->add($qb->expr()->like($field, ':search_' . $idx));
                        continue;
                    }

                    $orx->add($qb->expr()->eq($field, ':search_' . $idx));
                }

                $qb->setParameter('search_' . $idx, $val);
                $idx++;
            }

            $qb->andWhere($orx);

            return $qb;
        }

        if (!$exact) {
            $values = str_replace('*', '%', $values);
            $values = str_replace('%%', '%', '%' . $values . '%');
        }

        foreach ($fields as $field) {
            if (!$exact) {
                $orx->add($qb->expr()->like($field, ':search'));
                continue;
            }

            $orx->add($qb->expr()->eq($field, ':search'));
        }
        $qb->setParameter('search', $values);

        $qb->andWhere($orx);

        return $qb;
    }

    public static function buildSort(QueryBuilder $qb, array $sort, array $mapping, array $defaults = []) :QueryBuilder
    {
        $cls = new static();

        return $cls->buildQueryBuilderSort($qb, $sort, $mapping, $defaults);
    }

    public function buildQueryBuilderSort(QueryBuilder $qb, array $sort, array $mapping, array $defaults = []) :QueryBuilder
    {
        $sort = $this->preProcessSort($sort, $mapping);

        if (0 === count($sort)) {
            foreach ($defaults AS $k => $v) {
                $qb->addOrderBy($k, $v);
            }

            return $qb;
        }

        foreach ($sort AS $k => $v) {
            $fields = $mapping[$k];
            $fields = (!is_array($fields))? [$fields]:$fields;
            foreach ($fields AS $field) {
                $qb->addOrderBy($field, $v);
            }
        }

        return $qb;
    }

    public function preProcessSort(array $sort, array $mapping) :array
    {
        $f = static function ($v, $k) use ($mapping) {
            return array_key_exists($k, $mapping);
        };

        $sort = array_filter($sort, $f, ARRAY_FILTER_USE_BOTH);

        $m = static function ($v) {
            if (!is_string($v) || !in_array(strtoupper($v), ['ASC', 'DESC'], true)) {
                return 'ASC';
            }

            return strtoupper($v);
        };

        return array_map($m, $sort);
    }

    /**
     * Checks if the first char of the given string is one of > < ! and return
     * the sql operator for it.
     *
     * @param string $str
     * @return string
     */
    public static function getOperatorFromString($str) :string
    {
        if (!is_string($str) || $str === '') {
            return '=';
        }

        $like = false;
        if (false !== strpos($str, '%') || false !== strpos($str, '*')) {
            $like = true;
            $str = str_replace('*', '%', $str);
        }

        $fc = $str[0];
        $op = $like ? 'LIKE' : '=';
        if (in_array($fc, ['!', '>', '<'], true)) {
            switch ($fc) {
                case '!':
                    $op = $like ? 'NOT LIKE' : '<>';
                    break;
                case '>':
                    $op = '>=';
                    break;
                case '<':
                    $op = '<=';
                    break;
            }
        }

        return $op;
    }

    /**
     * Strips the first char from the string if it is in [<, >, !]
     *
     * @param $str
     * @return mixed
     */
    public static function stripOperatorFromString($str)
    {
        if (!is_string($str) || '' === $str) {
            return $str;
        }

        $fc = $str[0];
        if (in_array($fc, ['!', '>', '<'], true)) {
            $str = substr_replace($str, '', 0, 1);
        }

        if (false !== strpos($str, '%') || false !== strpos($str, '*')) {
            $str = str_replace('*', '%', $str);
        }

        return $str;
    }
}