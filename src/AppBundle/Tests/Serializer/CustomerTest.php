<?php


namespace AppBundle\Tests\Serializer;


use AppBundle\Entity\Customer;
use AppBundle\Repository\CustomerRepository;
use AppBundle\Tests\EntityManagerAwareTestCase;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;

class CustomerTest extends EntityManagerAwareTestCase
{
    public function testCustomer() :void
    {
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->getContainer()->get('serializer');

        /**
         * @var CustomerRepository $repo
         */
        $repo = $this->getEntityManager()->getRepository(Customer::class);

        $store = $repo->findOneBy(['name' => 'customer_1']);
        if (!$store) {
            self::fail('Failed to load test customer');
        }

        $context = SerializationContext::create()->setGroups(['customer', 'meta']);
        $json = $serializer->serialize($store, 'json', $context);
        $data = json_decode($json, true);

        self::assertCustomerDataIsValid($data);
    }

    public function testCustomerWithStores() :void
    {
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->getContainer()->get('serializer');

        /**
         * @var CustomerRepository $repo
         */
        $repo = $this->getEntityManager()->getRepository(Customer::class);

        $store = $repo->findOneBy(['name' => 'customer_1']);
        if (!$store) {
            self::fail('Failed to load test customer');
        }

        $context = SerializationContext::create()->setGroups(['customer', 'meta', 'customer_stores', 'store']);
        $json = $serializer->serialize($store, 'json', $context);
        $data = json_decode($json, true);

        self::assertCustomerDataIsValid($data, true);

        static::assertNotCount(0, $data['stores']);

        $store = $data['stores'][0];

        StoreTest::assertStoreDataIsValid($store);
    }

    public static function assertCustomerDataIsValid(array $data, $includeStores = false) :void
    {
        $required = [
            'id', 'name', 'created_at', 'updated_at'
        ];

        if ($includeStores) {
            $required[] = 'stores';
        }

        foreach ($required AS $key) {
            static::assertArrayHasKey($key, $data);
        }

        $diff = array_diff(array_keys($data), $required);
        static::assertCount(0, $diff);

        if ($includeStores) {
            static::assertIsArray($data['stores']);
        }
    }
}