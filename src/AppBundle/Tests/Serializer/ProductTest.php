<?php
namespace AppBundle\Tests\Serializer;

use AppBundle\Entity\Product;
use AppBundle\Repository\ProductRepository;
use AppBundle\Tests\EntityManagerAwareTestCase;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;

class ProductTest extends EntityManagerAwareTestCase
{

    public function testSerialize() :void
    {
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->getContainer()->get('serializer');

        /**
         * @var ProductRepository $repo
         */
        $repo = $this->getEntityManager()->getRepository(Product::class);

        $product = $repo->findOneBy([]);

        if (!$product) {
            self::fail('Failed to load test product');
        }

        $context = SerializationContext::create()->setGroups(['product', 'meta']);

        $json = $serializer->serialize($product, 'json', $context);

        $data = json_decode($json, true);

        static::assertProductDataIsValid($data);
    }

    public function testSerializeMinimal() :void
    {
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->getContainer()->get('serializer');

        /**
         * @var ProductRepository $repo
         */
        $repo = $this->getEntityManager()->getRepository(Product::class);

        $product = $repo->findOneBy([]);

        if (!$product) {
            self::fail('Failed to load test product');
        }

        $context = SerializationContext::create()->setGroups(['product_minimal']);

        $json = $serializer->serialize($product, 'json', $context);
        $data = json_decode($json, true);

        static::assertProductDataIsValid($data, true);
    }

    public static function assertProductDataIsValid(array $data, $minimal = false, $meta = false) :void
    {
        if ($minimal) {
            $required = [
                'id', 'item', "country", "price", "vat", "available_from", "available_until",
                "min_subscription_time", "min_quantity"
            ];

            if ($meta === true) {
                $required[] = 'updated_at';
                $required[] = 'created_at';
            }

        } else {
            $required = [
                'id', 'item', 'name', 'description', "country", "price", "vat", "available_from", "available_until",
                "min_subscription_time", 'min_quantity', "shipping_costs", "notes", 'created_at', 'updated_at'
            ];
        }

        foreach ($required AS $k) {
            static::assertArrayHasKey($k, $data);
        }

        $diff = array_diff(array_keys($data), $required);
        static::assertCount(0, $diff);
    }
}