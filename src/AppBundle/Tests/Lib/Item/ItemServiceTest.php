<?php


namespace AppBundle\Tests\Lib\Item;


use AppBundle\Entity\Item;
use AppBundle\Lib\Item\Form\CreateUpdateFormType;
use AppBundle\Lib\Item\Form\SearchDto;
use AppBundle\Lib\Item\Form\SearchFormType;
use AppBundle\Lib\ValidationException;
use AppBundle\Tests\EntityManagerAwareTestCase;
use Doctrine\ORM\QueryBuilder;

class ItemServiceTest extends EntityManagerAwareTestCase
{
    public function testSearch() :void
    {
        $service = $this->getContainer()->get('app.item.service');
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
         * @var SearchDto $dto
         */
        $dto = $form->getData();

        $qb = $service->search($dto);
        static::assertInstanceOf(QueryBuilder::class, $qb);

        $dql = "SELECT i FROM AppBundle\Entity\Item i LEFT JOIN i.products p LEFT JOIN p.country l LEFT JOIN p.subscriptions sub LEFT JOIN sub.store s WHERE i.id IN(:item) AND i.name = :name AND p.id IN(:product) AND p.minSubscriptionTime = :runtime AND s.id IN(:store) AND l.id IN(:country) AND (i.name LIKE :search OR i.description LIKE :search) ORDER BY i.id DESC";
        static::assertEquals($dql, $qb->getDQL());
    }

    public function testCreate() :int
    {
        $item = new Item();
        $service = $this->getContainer()->get('app.item.service');

        try {
            $service->create($item);
            static::fail('This should not have happened');

        } catch (ValidationException $ve) {
            $errors = $ve->getValidationErrors();
            static::assertCount(2, $errors);
        }

        $form = $this->createForm(CreateUpdateFormType::class, $item);
        static::assertTrue($form->has('name'));
        static::assertTrue($form->has('description'));

        $data = [
            'name' => 'COX',
            'description' => 'cox apple'
        ];

        $form->submit($data);
        /**
         * @var Item $item
         */
        $item = $form->getData();

        static::assertEquals('cox', $item->getName());
        static::assertEquals('cox apple', $item->getDescription());

        try {
            $item = $service->create($item);
        } catch (ValidationException $ve) {
            static::fail('This should not have happened');
        }

        return $item->getId();
    }

    /**
     * @param int $itemId
     * @depends testCreate
     */
    public function testUpdate(int $itemId) :void
    {
        $item = $this->getEntityManager()->getRepository(Item::class)->find($itemId);

        if (!$item) {
            static::fail('Cannot find item with id');
        }

        $service = $this->getContainer()->get('app.item.service');

        try {
            $item->setDescription('');
            $service->update($item);
            static::fail('This should not have happened');

        } catch (ValidationException $ve) {
            $errors = $ve->getValidationErrors();
            static::assertCount(1, $errors);
        }

        try {
            $item->setDescription('new description');
            $service->update($item);

        } catch (ValidationException $ve) {
            static::fail('This should not have happened');
        }
    }
}