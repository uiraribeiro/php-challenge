<?php


namespace AppBundle\Tests\Lib\Stock\Form;


use AppBundle\Lib\Stock\Form\SearchDto;
use AppBundle\Lib\Stock\Form\SearchFormType;
use AppBundle\Tests\EntityManagerAwareTestCase;

class SearchFormTest extends EntityManagerAwareTestCase
{
    public function testSearch()
    {
        $search = new SearchDto();
        $form = $this->createForm(SearchFormType::class, $search);

        $params = [
            'filter' => [
                'country' => 'DE,AT,CH',
                'item' => '1,2,3',
                'at_date' => '2021-01-01',
                'quantity' => 100,
                'only_current' => 1,
                'year' => '2021',
                'month' => 5]
        ];

        $form->submit($params);

        /**
         * @var SearchDto $data
         */
        $data = $form->getData();
        static::assertInstanceOf(SearchDto::class, $data);

        $filter = $data->getFilter()->toArray();

        foreach ($params['filter'] AS $k => $v) {
            static::assertArrayHasKey($k, $filter);

            if (in_array($k, ['country', 'item', 'year', 'month'])) {
                static::assertIsArray($filter[$k]);
                continue;
            }

            if ($k === 'at_date') {
                static::assertInstanceOf(\DateTime::class, $filter[$k]);
                continue;
            }

            static::assertEquals($v, $filter[$k]);
        }



    }
}