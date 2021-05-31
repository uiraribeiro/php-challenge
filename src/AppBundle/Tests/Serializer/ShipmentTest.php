<?php


namespace AppBundle\Tests\Serializer;


use AppBundle\Entity\Shipment;
use AppBundle\Tests\EntityManagerAwareTestCase;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;

class ShipmentTest extends EntityManagerAwareTestCase
{
    public function testShipmentSerialize() :void
    {
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->getSerializer();
        $shipment = $this->getEntityManager()->getRepository(Shipment::class)->findOneBy([]);

        $context = SerializationContext::create()->setGroups(['shipment']);
        $json = $serializer->serialize($shipment, 'json', $context);
        $data = json_decode($json, true);

        static::assertShipmentDataIsValid($data);
    }

    public static function assertShipmentDataIsValid(array $data) :void
    {
        $required = ['id', 'status', 'shipping_date', 'quantity'];

        foreach ($required AS $key) {
            static::assertArrayHasKey($key, $data);
        }

        $diff = array_diff(array_keys($data), $required);
        static::assertCount(0, $diff);
    }
}