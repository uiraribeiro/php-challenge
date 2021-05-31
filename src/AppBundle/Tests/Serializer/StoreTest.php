<?php


namespace AppBundle\Tests\Serializer;


use AppBundle\Entity\Store;
use AppBundle\Entity\Subscription;
use AppBundle\Repository\StoreRepository;
use AppBundle\Repository\SubscriptionRepository;
use AppBundle\Tests\EntityManagerAwareTestCase;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;

class StoreTest extends EntityManagerAwareTestCase
{
    public function testSerialize() :void
    {
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->getContainer()->get('serializer');

        /**
         * @var StoreRepository $repo
         */
        $repo = $this->getEntityManager()->getRepository(Store::class);

        $store = $repo->findOneBy(['name' => 'customer_1_store_1']);
        if (!$store) {
            self::fail('Failed to load test store');
        }

        $context = SerializationContext::create()->setGroups(['store', 'meta']);
        $json = $serializer->serialize($store, 'json', $context);
        $data = json_decode($json, true);

        self::assertStoreDataIsValid($data);
    }

    public function testSerializeWithSubscriptions() :void
    {
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->getContainer()->get('serializer');

        /**
         * @var StoreRepository $repo
         */
        $repo = $this->getEntityManager()->getRepository(Store::class);

        $store = $repo->findOneBy(['name' => 'customer_1_store_1']);
        if (!$store) {
            self::fail('Failed to load test store');
        }

        $context = SerializationContext::create()->setGroups(['store', 'store_subscriptions', 'subscription', 'meta']);
        $json = $serializer->serialize($store, 'json', $context);
        $data = json_decode($json, true);

        static::assertStoreDataIsValid($data, true);

        static::assertNotCount(0, $data['subscriptions']);

        $subscription = $data['subscriptions'][0];

        SubscriptionTest::assertSubscriptionDataIsValid($subscription);
    }

    public static function assertStoreDataIsValid(array $data, $includeSubscriptions = false) :void
    {
        $required = [
            'country', 'customer_id', 'id', 'name', 'description', 'start_date', 'created_at', 'updated_at'
        ];

        if ($includeSubscriptions) {
            $required[] = 'subscriptions';
        }

        foreach ($required AS $key) {
            static::assertArrayHasKey($key, $data);
        }

        $diff = array_diff(array_keys($data), $required);
        static::assertCount(0, $diff);

        if ($includeSubscriptions) {
            static::assertIsArray($data['subscriptions']);
        }
    }
}