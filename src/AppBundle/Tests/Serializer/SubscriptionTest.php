<?php


namespace AppBundle\Tests\Serializer;


use AppBundle\Entity\Subscription;
use AppBundle\Repository\SubscriptionRepository;
use AppBundle\Tests\EntityManagerAwareTestCase;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;

class SubscriptionTest extends EntityManagerAwareTestCase
{
    public function testSerialize() :void
    {
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->getContainer()->get('serializer');

        /**
         * @var SubscriptionRepository $repo
         */
        $repo = $this->getEntityManager()->getRepository(Subscription::class);

        $subscription = $repo->findOneBy([]);
        if (!$subscription) {
            self::fail('Failed to load test subscription');
        }

        $context = SerializationContext::create()->setGroups(['subscription', 'meta', 'include_shipments', 'shipment']);
        $json = $serializer->serialize($subscription, 'json', $context);
        $data = json_decode($json, true);

        self::assertSubscriptionDataIsValid($data, true);

    }

    public static function assertSubscriptionDataIsValid(array $data, $includeShipments = false, $includeStoreInfo = false) :void
    {
        $required = [
            'product_id', 'item_id', 'name', 'description', 'country', 'id', 'quantity',
            'start_date', 'end_date', 'recurring', 'created_at', 'updated_at', 'runtime'
        ];

        if ($includeStoreInfo) {
            $required[] = 'store_id';
            $required[] = 'store_name';
        }

        if ($includeShipments) {
            $required[] = 'shipments';
        }

        foreach ($required AS $key) {
            static::assertArrayHasKey($key, $data);
        }

        $diff = array_diff(array_keys($data), $required);
        static::assertCount(0, $diff);

        if ($includeShipments && 0 !== count($data['shipments'])) {
            $shipment = $data['shipments'][0];
            ShipmentTest::assertShipmentDataIsValid($shipment);
        }
    }
}