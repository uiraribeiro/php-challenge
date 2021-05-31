<?php


namespace AppBundle\Tests\Controller;


use AppBundle\Tests\EntityManagerAwareTestCase;
use AppBundle\Tests\Serializer\ItemTest;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ItemControllerTest extends WebTestCase
{
    public static function setUpBeforeClass() :void
    {
        EntityManagerAwareTestCase::bootstrapDatabase();
    }

    public function testIndex() :void
    {
        $client = static::createClient();
        $client->request('GET', '/api/products/items.json', []);

        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertNotCount(0, $body);

        ItemTest::assertItemDataIsValid($body[0]);
    }

    public function testCreate() :string
    {
        $client = static::createClient();
        $client->request('POST', '/api/products/items.json', []);

        $response = $client->getResponse();
        static::assertEquals(400, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertCount(2, $body);

        $data = [
            'name' => 'Granny_Smith',
            'description' => 'Granny Smith'
        ];

        $client->request('POST', '/api/products/items.json', $data);

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

        static::assertArrayHasKey('name', $body);
        static::assertEquals('granny_smith', $body['name']);

        return $url;
    }

    /**
     * @param string $url
     * @depends testGet
     */
    public function testUpdate(string $url) :void
    {

        $data = [
            'description' => 'Granny Smith Updated description'
        ];
        $client = static::createClient();
        $client->request('PUT', $url, $data);

        $response = $client->getResponse();
        static::assertEquals(400, $response->getStatusCode());


        $data = [
            'name' => 'granny_smith',
            'description' => 'Granny Smith Updated description'
        ];

        $client->request('PUT', $url, $data);

        $response = $client->getResponse();
        static::assertEquals(204, $response->getStatusCode());

        $client->request('GET', $url, []);

        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertArrayHasKey('name', $body);
        static::assertEquals('Granny Smith Updated description', $body['description']);
    }
}