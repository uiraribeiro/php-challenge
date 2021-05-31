<?php


namespace AppBundle\Tests\Serializer;


use AppBundle\Entity\Country;
use AppBundle\Tests\ContainerAwareTestCase;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;

class CountryTest extends ContainerAwareTestCase
{
    public function testSerialize() :void
    {
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->getContainer()->get('serializer');

        $country = new Country();
        $country->setId('DE');
        $country->setName('Germany');

        $context = SerializationContext::create()->setGroups(['country']);
        $json = $serializer->serialize($country, 'json', $context);
        $expected = '{"id":"DE","name":"Germany"}';

        static::assertJsonStringEqualsJsonString($expected, $json);

        $context = SerializationContext::create()->setGroups(['store']);
        $json = $serializer->serialize($country, 'json', $context);
        $expected = '{"id":"DE","name":"Germany"}';

        static::assertJsonStringEqualsJsonString($expected, $json);

        $context = SerializationContext::create()->setGroups(['minimal']);
        $json = $serializer->serialize($country, 'json', $context);
        $expected = '{"id":"DE"}';

        static::assertJsonStringEqualsJsonString($expected, $json);

    }
}