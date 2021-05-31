<?php


namespace AppBundle\Tests\Lib\Customer\Form;


use AppBundle\Lib\Customer\Form\SearchDto;
use AppBundle\Lib\Customer\Form\SearchFormType;
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
                'customer' => '1,2,3',
                'store' => '1,2,3',
                'name' => 'test',
                'country' => 'DE,AT,CH',
                'item_active' => 'apple',
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
                'customer' => [1,2,3],
                'store' => [1,2,3],
                'name' => 'test',
                'country' => ['DE','AT','CH'],
                'item_active' => ['apple'],
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