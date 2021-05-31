<?php


namespace AppBundle\Tests\Controller;


use AppBundle\Tests\Serializer\SubscriptionTest;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomerSubscriptionControllerTest extends WebTestCase
{
    public function testListSubscriptions() :void
    {
        $client = static::createClient();
        $client->request('GET', '/api/customers/1/subscriptions.json', []);

        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));

        $body = json_decode($response->getContent(), true);

        static::assertNotCount(0, $body);

        $subscription = $body[0];

        SubscriptionTest::assertSubscriptionDataIsValid($subscription, false, true);

        $client->request('GET', '/api/customers/1/subscriptions.json', ['filter' => ['runtime' => 22]]);

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

        // Create a new subscription, this should fails since no store is set and quantity is beyond the
        // minimum order quantity
        $params = [

            'product' => $product['id'],
            'recurring' => 1,
            'quantity' => 1
        ];

        $client->request('POST', '/api/customers/1/subscriptions.json', $params);
        $response = $client->getResponse();

        static::assertEquals(400, $response->getStatusCode());
        static::assertEquals('UTF-8', $response->getCharset());
        static::assertEquals('application/json', $response->headers->get('content-type'));
        $body = json_decode($response->getContent(), true);
        static::assertNotCount(0, $body);


        $params = [
            'store' => 2,
            'product' => $product['id'],
            'recurring' => 1,
            'quantity' => 1000
        ];

        $client->request('POST', '/api/customers/1/subscriptions.json', $params);
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
        SubscriptionTest::assertSubscriptionDataIsValid($body, true, true);

        return $location;
    }

    /**
     * @depends testCreateSubscription
     */
    public function testCancelSubscription($subscription) :void
    {
        $url = str_replace('.json','/cancel.json', $subscription);

        $client = static::createClient();
        $client->request('POST', $url, []);
        $response = $client->getResponse();
        static::assertEquals(400, $response->getStatusCode());

        $client->request('POST', $url, ['reason' => 'bogus']);
        $response = $client->getResponse();
        static::assertEquals(204, $response->getStatusCode());
    }
}