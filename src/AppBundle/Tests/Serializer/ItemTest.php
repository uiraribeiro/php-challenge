<?php


namespace AppBundle\Tests\Serializer;

use AppBundle\Entity\Item;
use AppBundle\Repository\ItemRepository;
use AppBundle\Tests\EntityManagerAwareTestCase;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;

class ItemTest extends EntityManagerAwareTestCase
{

    public function testSerialize() :void
    {
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->getContainer()->get('serializer');

        /**
         * @var ItemRepository $repo
         */
        $repo = $this->getEntityManager()->getRepository(Item::class);

        $item = $repo->findOneBy(['name' => 'boskop']);

        if (!$item) {
            self::fail('Failed to load test item');
        }

        $context = SerializationContext::create()->setGroups(['item', 'meta']);

        $json = $serializer->serialize($item, 'json', $context);
        $data = json_decode($json, true);

        $required = [
            'id', 'name', 'description', 'created_at', 'updated_at'
        ];

        foreach ($required AS $key) {
            static::assertArrayHasKey($key, $data);
        }
    }

    public function testSerializeMinimal() :void
    {
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->getContainer()->get('serializer');

        /**
         * @var ItemRepository $repo
         */
        $repo = $this->getEntityManager()->getRepository(Item::class);

        $item = $repo->findOneBy(['name' => 'boskop']);

        if (!$item) {
            self::fail('Failed to load test item');
        }

        $context = SerializationContext::create()->setGroups(['item_minimal']);

        $json = $serializer->serialize($item, 'json', $context);
        $data = json_decode($json, true);

        $required = [
            'id', 'name'
        ];

        foreach ($required AS $key) {
            static::assertArrayHasKey($key, $data);
        }
    }

    public function testSerializeWithProducts() :void
    {
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->getContainer()->get('serializer');

        /**
         * @var ItemRepository $repo
         */
        $repo = $this->getEntityManager()->getRepository(Item::class);

        $item = $repo->findOneBy(['name' => 'boskop']);

        if (!$item) {
            self::fail('Failed to load test item');
        }

        $context = SerializationContext::create()->setGroups(['item', 'item_products', 'product_minimal', 'meta']);

        $json = $serializer->serialize($item, 'json', $context);
        $data = json_decode($json, true);

        static::assertItemDataIsValid($data, true);
    }

    public static function assertItemDataIsValid(array $data, $includeProducts = false) :void
    {
        $required = [
            'id', 'name', 'description', 'created_at', 'updated_at'
        ];
        if ($includeProducts) {
            $required[] = 'products';
        }

        foreach ($required AS $k) {
            static::assertArrayHasKey($k, $data);
        }

        $diff = array_diff(array_keys($data), $required);
        static::assertCount(0, $diff);

        if ($includeProducts && 0 !== count($data['products'])) {
            ProductTest::assertProductDataIsValid($data['products'][0], true, true);
        }
    }
}