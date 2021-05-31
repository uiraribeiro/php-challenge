<?php


namespace AppBundle\Tests\Controller;

use AppBundle\Tests\Serializer\StoreTest;
use AppBundle\Tests\Serializer\SubscriptionTest;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomerStoresControllerTest extends WebTestCase
{
    public function testIndex() :void
    {
        $client = static::createClient();
        $client->request('GET', '/api/customers/1/stores.json', []);

        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertNotCount(0, $body);

        $store = $body[0];
        StoreTest::assertStoreDataIsValid($store, false);

    }

    public function testShow() :void
    {
        $client = static::createClient();
        $client->request('GET', '/api/customers/1/stores/2.json', []);

        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertNotCount(0, $body);
        StoreTest::assertStoreDataIsValid($body, true);
    }

    public function testCreate() :string
    {
        $client = static::createClient();
        $client->request('POST', '/api/customers/1/stores.json', []);

        $response = $client->getResponse();
        static::assertEquals(400, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertNotCount(0, $body);

        $client->request('POST', '/api/customers/1/stores.json', ['country' => 'NL', 'name' => 'NL test', 'description' => 'dummy']);

        $response = $client->getResponse();
        static::assertEquals(201, $response->getStatusCode());
        static::assertTrue($response->isRedirect());

        $location = $response->headers->get('location');

        return str_replace('http://localhost', '', $location).'.json';
    }

    /**
     * @param $url
     * @depends testCreate
     */
    public function testUpdate(string $url) :string
    {
        $client = static::createClient();
        $client->request('GET', $url, []);

        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $client->request('PUT', $url, ['country' => 'BE', 'name' => 'BE test', 'description' => 'dummy']);

        $response = $client->getResponse();
        static::assertEquals(204, $response->getStatusCode());

        $client->request('PUT', '/api/customers/1/stores/2.json', ['country' => 'BE', 'name' => 'BE test', 'description' => 'dummy']);
        $response = $client->getResponse();

        static::assertEquals(400, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertNotCount(0, $body);

        return $url;
    }

    /**
     * @param string $url
     * @return string
     * @depends testUpdate
     */
    public function testListAvailableProducts(string $url) :void
    {
        $productUrl = str_replace('.json','/products.json',$url);

        $client = static::createClient();
        $client->request('GET', $productUrl, []);

        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        // we don't sell stuff in belgium
        static::assertCount(0, $body);

        $client->request('GET', '/api/customers/1/stores/2/products.json', []);

        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertNotCount(0, $body);
    }

    public function testListSubscriptions() :void
    {
        $client = static::createClient();
        $client->request('GET', '/api/customers/1/stores/2/subscriptions.json', []);

        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertNotCount(0, $body);

        $subscription = $body[0];

        SubscriptionTest::assertSubscriptionDataIsValid($subscription);

        $client->request('GET', '/api/customers/1/stores/2/subscriptions.json', ['filter' => ['runtime' => 22]]);

        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertCount(0, $body);
    }

    public function testCreateSubscription() :string
    {
        $client = static::createClient();
        $client->request('GET', '/api/customers/1/stores/2/products.json', []);

        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertNotCount(0, $body);

        $product = $body[0];

        // Create a new subscription
        // Create a new subscription
        $params = [
            'product' => $product['id'],
            'recurring' => 1,
            'quantity' => 1
        ];

        $client->request('POST', '/api/customers/1/stores/2/subscriptions.json', $params);
        $response = $client->getResponse();

        static::assertEquals(400, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));
        $body = json_decode($response->getContent(), true);
        static::assertNotCount(0, $body);


        $params = [
            'product' => $product['id'],
            'recurring' => 1,
            'quantity' => 1000
        ];

        $client->request('POST', '/api/customers/1/stores/2/subscriptions.json', $params);
        $response = $client->getResponse();

        static::assertEquals(201, $response->getStatusCode());
        static::assertTrue($response->isRedirect());

        // Prepare location and grab newly created
        $location = $response->headers->get('location');
        $location = str_replace('http://localhost', '', $location).'.json';


        $client->request('GET', $location, []);
        $response = $client->getResponse();

        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);
        SubscriptionTest::assertSubscriptionDataIsValid($body, true);

        return $location;
    }

    /**
     * @depends testCreateSubscription
     */
    public function testCancelSubscription($subscription) :string
    {
        $url = str_replace('.json','/cancel.json', $subscription);

        $client = static::createClient();
        $client->request('POST', $url, []);
        $response = $client->getResponse();
        static::assertEquals(400, $response->getStatusCode());

        $client->request('POST', $url, ['reason' => 'bogus']);
        $response = $client->getResponse();
        static::assertEquals(204, $response->getStatusCode());




        return $subscription;
    }


    public function testMatctchingError()
    {
        $client = static::createClient();
        $client->request('GET', '/api/customers/2/stores/2.json', []);
        $response = $client->getResponse();

        static::assertEquals(404, $response->getStatusCode());

        $client->request('GET', '/api/customers/2/stores/4/subscriptions/4.json', []);
        $response = $client->getResponse();
        static::assertEquals(404, $response->getStatusCode());
    }

}