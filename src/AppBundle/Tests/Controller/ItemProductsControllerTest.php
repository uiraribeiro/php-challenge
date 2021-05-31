<?php


namespace AppBundle\Tests\Controller;


use AppBundle\Lib\Tools;
use AppBundle\Tests\EntityManagerAwareTestCase;
use AppBundle\Tests\Serializer\ItemTest;
use AppBundle\Tests\Serializer\ProductTest;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ItemProductsControllerTest extends WebTestCase
{
    public static function setUpBeforeClass() :void
    {
        EntityManagerAwareTestCase::bootstrapDatabase();
    }

    public function testIndex() :void
    {
        $client = static::createClient();
        $client->request('GET', '/api/products/items/1/products.json', []);

        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertNotCount(0, $body);

        ProductTest::assertProductDataIsValid($body[0]);
    }

    public function testCreate() :string
    {
        $client = static::createClient();
        $from = Tools::getFirstDayDateForNextMonth(new \DateTime());
        $until = clone $from;
        $until->add(new \DateInterval('P24M'));
        $until = Tools::getLastDayDateTimeForMonth($until);

        $data = [
            'item' => 1,
            'country' => 'DE',
            'price' => 100,
            'available_from' => $from->format('Y-m-d'),
            'available_until' => $until->format('Y-m-d'),
            'min_quantity' => 100,
            'min_subscription_time' => 12,
            'vat' => 7,
        ];

        $client->request('POST', '/api/products/items/1/products.json', $data);

        $response = $client->getResponse();
        static::assertEquals(400, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertNotCount(0, $body);

        $data['notes'] = 'Some intelligent note';
        $data['shipping_costs'] = [
           ['from' => 0, 'costs' => 100], ['from' => 10, 'costs' => 99],

        ];

        $client->request('POST', '/api/products/items/1/products.json', $data);
        $response = $client->getResponse();
        static::assertEquals(201, $response->getStatusCode());
        static::assertTrue($response->isRedirect());
        static::assertTrue($response->headers->has('location'));

        $location = $response->headers->get('location');

        return str_replace('http://localhost','', $location).'.json';
    }

    /**
     * @param string $url
     * @depends testCreate
     */
    public function testGet(string $url) :string
    {
        $client = static::createClient();
        $client->request('GET', $url, []);

        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        ProductTest::assertProductDataIsValid($body);

        return $url;
    }

    /**
     * @param string $url
     * @depends testGet
     */
    public function testUpdate(string $url) :void
    {
        $data = [
            'notes' => ''
        ];
        $client = static::createClient();
        $client->request('PATCH', $url, $data);

        $response = $client->getResponse();
        static::assertEquals(400, $response->getStatusCode());

        $client->request('GET', $url, []);

        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $data = json_decode($response->getContent(), true);
        $data['min_quantity'] = 99;

        $client->request('PATCH', $url, $data);

        $response = $client->getResponse();
        static::assertEquals(204, $response->getStatusCode());

        $client->request('GET', $url, []);

        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertArrayHasKey('min_quantity', $body);
        static::assertEquals(99, $body['min_quantity']);
    }
}