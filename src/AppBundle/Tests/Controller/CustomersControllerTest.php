<?php


namespace AppBundle\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomersControllerTest extends WebTestCase
{
    public function testIndex() :void
    {
        $client = static::createClient();
        $client->request('GET', '/api/customers.json', ['filter' => ['customer' => 2]]);

        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertCount(1, $body);
    }

    public function testCreate() :void
    {
        $client = static::createClient();
        $client->request('POST', '/api/customers.json', []);

        $response = $client->getResponse();
        static::assertEquals(400, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertCount(1, $body);

        $client->request('POST', '/api/customers.json', ['name' => 'test123']);

        $response = $client->getResponse();
        static::assertEquals(201, $response->getStatusCode());
        static::assertTrue($response->isRedirect());
    }

    public function testShow() :void
    {
        $client = static::createClient();
        $client->request('GET', '/api/customers/2.json', []);

        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertCount(5, $body);
        static::assertEquals(2, $body['id']);
    }

    public function testUpdate() :void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/customers/2.json', ['name' => '']);

        $response = $client->getResponse();
        static::assertEquals(400, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertCount(1, $body);

        $client->request('PUT', '/api/customers/2.json', ['name' => 'new name']);

        $response = $client->getResponse();
        static::assertEquals(204, $response->getStatusCode());

    }









}