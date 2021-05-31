<?php


namespace AppBundle\Tests\Lib\Product\Form;


use AppBundle\Lib\Product\Form\SearchDto;
use AppBundle\Lib\Product\Form\SearchFormType;
use AppBundle\Tests\ContainerAwareTestCase;

class SearchFormTest extends ContainerAwareTestCase
{
    public function testSearchForm() :void
    {
        $dto = new SearchDto();
        $dto->getSort()->setSortBy('name');


        $form = $this->createForm(SearchFormType::class, $dto);

        $data = [
            'filter' => [
                'product' => '1,2,3',
                'store' => '1,2,3',
                'item' => '1,2,3',
                'name' => 'test',
                'country' => 'DE,AT,CH',
                'runtime' => 12,
                'search' => 'search'
            ],
            'sort' => [
                'sort_by' => 'id',
                'direction' => 'DESC'
            ]
        ];

        $form->submit($data);

        /**
         * @var SearchDto $submitted
         */
        $submitted = $form->getData();

        $filter = $submitted->getFilter()->toArray();
        $sort = $submitted->getSort()->toArray();

        $expected = [
            'product' => [1,2,3],
            'store' => [1,2,3],
            'item' => [1,2,3],
            'name' => 'test',
            'country' => ['DE','AT','CH'],
            'runtime' => 12,
            'search' => 'search'];

        foreach ($expected AS $k => $v) {
            static::assertArrayHasKey($k, $filter);
            static::assertEquals($v, $filter[$k]);
        }

        $expected = ['id' => 'DESC'];
        static::assertEquals($expected, $sort);

        $form = $this->createForm(SearchFormType::class, $dto);

        $data = [
            'filter' => [
                'search' => 'search'
            ],
            'sort' => [
                'sort_by' => 'id',
                'direction' => 'DESC'
            ]
        ];

        $form->submit($data);

        /**
         * @var SearchDto $submitted
         */
        $submitted = $form->getData();

        $filter = $submitted->getFilter()->toArray();
        $sort = $submitted->getSort()->toArray();

        static::assertCount(1, $filter);

        $expected = [
            'search' => 'search'];

        foreach ($expected AS $k => $v) {
            static::assertArrayHasKey($k, $filter);
            static::assertEquals($v, $filter[$k]);
        }

        $expected = ['id' => 'DESC'];
        static::assertEquals($expected, $sort);
    }
}