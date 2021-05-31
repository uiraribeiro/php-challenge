<?php

namespace AppBundle\Tests\Lib;

use AppBundle\Lib\QueryBuilderHelper;
use AppBundle\Tests\EntityManagerAwareTestCase;
use Doctrine\ORM\QueryBuilder;

class QueryBuilderHelperTest extends EntityManagerAwareTestCase
{
    public function testQueryBuilderPreProcessFilter() :void
    {
        $map = [
            'search' => ['f1','f2'],
            'name' => ['f2'],
            'title' => ['f3']
        ];

        $filter = ['search' => 'test', 'name' => '*', 'bogus', 'title' => []];

        $helper = new QueryBuilderHelper();
        $result = $helper->preProcessFilters($filter, $map, []);

        static::assertCount(1, $result);
        static::assertArrayHasKey('search', $result);

        $result = $helper->preProcessFilters($filter, $map, ['name']);
        static::assertCount(2, $result);
        static::assertArrayHasKey('search', $result);
        static::assertArrayHasKey('name', $result);

    }

    public function testQueryBuilderProcessStandardFilter() :void
    {
        $qb = $this->getQueryBuilderPrototype();

        $fields = ['field_1', 'field_2'];
        $values = 1;
        $name = 'test';

        $helper = new QueryBuilderHelper();

        $qb = $helper->processStandardFilter($qb, $name, $fields, $values);

        $dql = "SELECT FROM test t WHERE field_1 = :test OR field_2 = :test";

        static::assertEquals($dql, $qb->getDQL());
        $parameter = $qb->getParameter('test');

        static::assertNotNull($parameter);
        static::assertEquals($name, $parameter->getName());
        static::assertEquals(1, $parameter->getValue());

        $qb = $this->getQueryBuilderPrototype();

        $fields = ['field_1'];

        $qb = $helper->processStandardFilter($qb, $name, $fields, $values);

        $dql = "SELECT FROM test t WHERE field_1 = :test";

        static::assertEquals($dql, $qb->getDQL());
        $parameter = $qb->getParameter('test');

        static::assertNotNull($parameter);
        static::assertEquals($name, $parameter->getName());
        static::assertEquals(1, $parameter->getValue());

        $qb = $this->getQueryBuilderPrototype();

        $fields = 'field_1';

        $qb = $helper->processStandardFilter($qb, $name, $fields, $values);

        $dql = "SELECT FROM test t WHERE field_1 = :test";

        static::assertEquals($dql, $qb->getDQL());
        $parameter = $qb->getParameter('test');

        static::assertNotNull($parameter);
        static::assertEquals($name, $parameter->getName());
        static::assertEquals(1, $parameter->getValue());

        $qb = $this->getQueryBuilderPrototype();

        $fields = [];

        $qb = $helper->processStandardFilter($qb, $name, $fields, $values);

        $dql = "SELECT FROM test t";

        static::assertEquals($dql, $qb->getDQL());
        $parameters = $qb->getParameters();
        static::assertCount(0, $parameters);

    }

    public function testQueryBuilderProcessArrayValueFilter() :void
    {
        $qb = $this->getQueryBuilderPrototype();

        $fields = ['field_1'];
        $values = [1,2,3,4];
        $name = 'test';

        $helper = new QueryBuilderHelper();

        $qb = $helper->processArrayValueFilter($qb, $name, $fields, $values);

        $dql = "SELECT FROM test t WHERE field_1 IN(:test)";

        static::assertEquals($dql, $qb->getDQL());
        $parameter = $qb->getParameter('test');

        static::assertNotNull($parameter);
        static::assertEquals($name, $parameter->getName());
        static::assertEquals($values, $parameter->getValue());

        $qb = $this->getQueryBuilderPrototype();

        $fields = ['field_1'];
        $values = ['a','b','c','d'];

        $qb = $helper->processArrayValueFilter($qb, $name, $fields, $values);

        $dql = "SELECT FROM test t WHERE field_1 IN(:test)";

        static::assertEquals($dql, $qb->getDQL());
        $parameter = $qb->getParameter('test');

        static::assertNotNull($parameter);
        static::assertEquals($name, $parameter->getName());
        static::assertEquals($values, $parameter->getValue());

        $qb = $this->getQueryBuilderPrototype();

        $fields = 'field_1';
        $values = ['a','b','c','d','e'];

        $qb = $helper->processArrayValueFilter($qb, $name, $fields, $values);

        $dql = "SELECT FROM test t WHERE field_1 IN(:test)";

        static::assertEquals($dql, $qb->getDQL());
        $parameter = $qb->getParameter('test');

        static::assertNotNull($parameter);
        static::assertEquals($name, $parameter->getName());
        static::assertEquals($values, $parameter->getValue());
    }

    public function testQueryBuilderProcessSearchFilter() :void
    {
        $qb = $this->getQueryBuilderPrototype();

        $fields = ['field_1'];
        $values = 'test';

        $helper = new QueryBuilderHelper();

        $qb = $helper->processSearchFilter($qb, $fields, $values, false);

        $dql = "SELECT FROM test t WHERE field_1 LIKE :search";

        static::assertEquals($dql, $qb->getDQL());
        $parameter = $qb->getParameter('search');

        static::assertNotNull($parameter);
        static::assertEquals('search', $parameter->getName());
        static::assertEquals('%'.$values.'%', $parameter->getValue());

        $qb = $this->getQueryBuilderPrototype();

        $fields = ['field_1'];
        $values = 'test';

        $qb = $helper->processSearchFilter($qb, $fields, $values, true);

        $dql = "SELECT FROM test t WHERE field_1 = :search";

        static::assertEquals($dql, $qb->getDQL());
        $parameter = $qb->getParameter('search');

        static::assertNotNull($parameter);
        static::assertEquals('search', $parameter->getName());
        static::assertEquals($values, $parameter->getValue());

        $qb = $this->getQueryBuilderPrototype();

        $fields = 'field_1';
        $values = ['test', 'test2'];

        $qb = $helper->processSearchFilter($qb, $fields, $values, false);

        $dql = "SELECT FROM test t WHERE field_1 LIKE :search_0 OR field_1 LIKE :search_1";

        static::assertEquals($dql, $qb->getDQL());
        $parameter = $qb->getParameter('search_0');

        static::assertNotNull($parameter);
        static::assertEquals('search_0', $parameter->getName());
        static::assertEquals('%'.$values[0].'%', $parameter->getValue());

        $parameter = $qb->getParameter('search_1');

        static::assertNotNull($parameter);
        static::assertEquals('search_1', $parameter->getName());
        static::assertEquals('%'.$values[1].'%', $parameter->getValue());

        $qb = $this->getQueryBuilderPrototype();

        $fields = ['field_1'];
        $values = ['test', 'test2'];

        $qb = $helper->processSearchFilter($qb, $fields, $values, true);

        $dql = "SELECT FROM test t WHERE field_1 = :search_0 OR field_1 = :search_1";

        static::assertEquals($dql, $qb->getDQL());
        $parameter = $qb->getParameter('search_0');

        static::assertNotNull($parameter);
        static::assertEquals('search_0', $parameter->getName());
        static::assertEquals($values[0], $parameter->getValue());

        $parameter = $qb->getParameter('search_1');

        static::assertNotNull($parameter);
        static::assertEquals('search_1', $parameter->getName());
        static::assertEquals($values[1], $parameter->getValue());
    }

    public function testBuildFilter() :void
    {
        $filter = [
            'search' => 'test',
            'array_filter' => [1,2,3,4],
            'standard_filter' => 1,
        ];

        $map = [
            'search' => ['search_field'],
            'array_filter' => 'array_field',
            'standard_filter' => 'standard_field'
        ];

        $qb = $this->getQueryBuilderPrototype();

        $res = QueryBuilderHelper::buildFilters($qb, $filter, $map);

        $dql = "SELECT FROM test t WHERE search_field LIKE :search AND array_field IN(:array_filter) AND standard_field = :standard_filter";

        static::assertEquals($dql, $qb->getDQL());

        $parameters = $res->getParameters();
        static::assertCount(3, $parameters);

        foreach ($filter AS $k => $v) {
            $parameter = $res->getParameter($k);
            static::assertNotNull($parameter);
        }

        $filter = [];
        $map = [];

        $qb = $this->getQueryBuilderPrototype();

        $res = QueryBuilderHelper::buildFilters($qb, $filter, $map);

        $dql = "SELECT FROM test t";

        static::assertEquals($dql, $res->getDQL());
        $parameters = $qb->getParameters();
        static::assertCount(0, $parameters);
    }

    public function testPreProcessSort() :void
    {
        $mapping = [
            'foo' => 'field_1',
            'bar' => 'field_2',
        ];

        $sort = [
            'foo' => 'asc',
            'bar' => 'Something else',
            'foobar' => 'ASC',
        ];

        $helper = new QueryBuilderHelper();

        $redacted = $helper->preProcessSort($sort, $mapping);

        static::assertCount(2, $redacted);
        static::assertArrayHasKey('foo', $redacted);
        static::assertArrayHasKey('bar', $redacted);
        static::assertEquals('ASC', $redacted['bar']);
    }

    public function testBuildQueryBuilderSort() :void
    {
        $mapping = [
            'foo' => ['field_1', 'field_2'],
            'bar' => 'field_3',
        ];

        $sort = [
            'foo' => 'DESC',
            'bar' => 'ASC',
        ];

        $helper = new QueryBuilderHelper();
        $qb = $this->getQueryBuilderPrototype();

        $res = $helper->buildQueryBuilderSort($qb, $sort, $mapping);

        $dql = "SELECT FROM test t ORDER BY field_1 DESC, field_2 DESC, field_3 ASC";
        static::assertEquals($dql, $res->getDQL());

        $qb = $this->getQueryBuilderPrototype();
        $res = $helper->buildQueryBuilderSort($qb, [], $mapping, ['field_4' => "ASC", "field_5" => "DESC"]);

        $dql = "SELECT FROM test t ORDER BY field_4 ASC, field_5 DESC";
        static::assertEquals($dql, $res->getDQL());

        $qb = $this->getQueryBuilderPrototype();
        $res = QueryBuilderHelper::buildSort($qb, $sort, $mapping);
        $dql = "SELECT FROM test t ORDER BY field_1 DESC, field_2 DESC, field_3 ASC";
        static::assertEquals($dql, $res->getDQL());
    }

    public function testGetOperatorFromString() :void
    {
        $value = 'SomeString';

        $op = QueryBuilderHelper::getOperatorFromString($value);
        static::assertEquals('=',$op);

        $value = '>SomeString';

        $op = QueryBuilderHelper::getOperatorFromString($value);
        static::assertEquals('>=',$op);

        $value = '<SomeString';

        $op = QueryBuilderHelper::getOperatorFromString($value);
        static::assertEquals('<=',$op);

        $value = '!SomeString';

        $op = QueryBuilderHelper::getOperatorFromString($value);
        static::assertEquals('<>',$op);


        $value = '*SomeString*';
        $op = QueryBuilderHelper::getOperatorFromString($value);
        static::assertEquals('LIKE',$op);

        $value = '!*SomeString*';
        $op = QueryBuilderHelper::getOperatorFromString($value);
        static::assertEquals('NOT LIKE',$op);

        $value = '';

        $op = QueryBuilderHelper::getOperatorFromString($value);
        static::assertEquals('=',$op);
    }

    public function testStripOperatorFromString() :void
    {
        $value = 'SomeString';

        $op = QueryBuilderHelper::stripOperatorFromString($value);
        static::assertEquals('SomeString',$op);

        $value = '>SomeString';

        $op = QueryBuilderHelper::stripOperatorFromString($value);
        static::assertEquals('SomeString',$op);

        $value = '<SomeString';

        $op = QueryBuilderHelper::stripOperatorFromString($value);
        static::assertEquals('SomeString',$op);

        $value = '!SomeString';

        $op = QueryBuilderHelper::stripOperatorFromString($value);
        static::assertEquals('SomeString',$op);

        $value = '!Some*String*';

        $op = QueryBuilderHelper::stripOperatorFromString($value);
        static::assertEquals('Some%String%',$op);

        $value = '';

        $op = QueryBuilderHelper::stripOperatorFromString($value);
        static::assertEquals('',$op);
    }
    
    protected function getQueryBuilderPrototype() :QueryBuilder
    {
        $qb = new QueryBuilder($this->getEntityManager());
        $qb->from('test', 't');

        return $qb;
    }


}
