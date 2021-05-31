<?php


namespace AppBundle\Tests\Lib\Subscription;


use AppBundle\Entity\Product;
use AppBundle\Entity\Subscription;
use AppBundle\Lib\Subscription\Form\SubscriptionRequestDto;
use AppBundle\Lib\Tools;
use AppBundle\Lib\ValidationException;
use AppBundle\Tests\EntityManagerAwareTestCase;

class SubscriptionServiceTest extends EntityManagerAwareTestCase
{
    /**
     * Asserts that the earliest start date for a subscription is not before the product starts
     */
    public function testSubscriptionRequestStartDateWithProductStartingInFuture() :void
    {
        $from = Tools::getFirstDayDateTimeForMonth(new \DateTime());
        $from->add(new \DateInterval('P3M'));

        $product = new Product();
        $product->setAvailableFrom($from);
        $product->setMinSubscriptionTime(1);

        $request = new SubscriptionRequestDto();
        $request->setProduct($product);

        $start = $request->getStartDate();

        static::assertEquals($from->format('Y-m-d H:i:s'), $start->format('Y-m-d H:i:s'));
    }

    /**
     * Asserts that the earliest start date for a subscription is not before the product starts
     */
    public function testSubscriptionRequestStartDateWithProductStartingInPast() :void
    {
        $from = Tools::getFirstDayDateTimeForMonth(new \DateTime());
        $from->sub(new \DateInterval('P3M'));

        $product = new Product();
        $product->setAvailableFrom($from);
        $product->setMinSubscriptionTime(12);

        $request = new SubscriptionRequestDto();
        $request->setProduct($product);

        $start = $request->getStartDate();
        $expected = Tools::getFirstDayDateForNextMonth(new \DateTime());

        static::assertEquals($expected->format('Y-m-d H:i:s'), $start->format('Y-m-d H:i:s'));
    }

    /**
     * Asserts that the end date is calculated by the runtime of the product
     * @throws \Exception
     */
    public function testSubscriptionRequestEndDate() :void
    {
        $product = new Product();
        $product->setMinSubscriptionTime(1);

        $start = Tools::getFirstDayDateTimeForMonth(new \DateTime('2021-01-01'));

        $request = new SubscriptionRequestDto();
        $request->setProduct($product);
        $request->setStart($start);

        $end = $request->getEndDate();

        static::assertEquals('2021-01-31 23:59:59', $end->format('Y-m-d H:i:s'));

        $product->setMinSubscriptionTime(12);
        $request = new SubscriptionRequestDto();
        $request->setProduct($product);
        $request->setStart($start);

        $end = $request->getEndDate();

        static::assertEquals('2021-12-31 23:59:59', $end->format('Y-m-d H:i:s'));
    }

    public function testCreate() :void
    {
        $store = $this->getStoreByName('customer_5_store_1');
        if (!$store) {
            static::fail('cannot find store');
        }

        $products = $this->getProductsForItem('golden_delicious',true, $store->getCountryCode(), 12);
        $product = current($products);

        /**
         * @var Product $product
         */
        if (!$product) {
            static::fail('cannot find product');
        }

        $subscriptionRequest = new SubscriptionRequestDto();
        $subscriptionRequest->setProduct($product);
        $subscriptionRequest->setStore($store);
        $subscriptionRequest->setQuantity(100);
        $subscriptionRequest->setRecurring(0);

        $service = $this->getContainer()->get('app.subscription.service');

        try {
            $subscription = $service->create($subscriptionRequest);

            static::assertInstanceOf(Subscription::class, $subscription);
            static::assertEquals($store->getId(), $subscription->getStore()->getId());
            static::assertEquals($product->getId(), $subscription->getProduct()->getId());
            static::assertEquals($subscriptionRequest->getStartDate()->format('Y-m-d H:i:s'), $subscription->getStartDate()->format('Y-m-d H:i:s'));
            static::assertEquals($subscriptionRequest->getEndDate()->format('Y-m-d H:i:s'), $subscription->getEndDate()->format('Y-m-d H:i:s'));
            static::assertEquals(100, $subscription->getQuantity());

        } catch (ValidationException $v) {
            $errors = $v->getValidationErrors();
            static::fail('There should have been any errors');
        }

        $subscriptionRequest->setQuantity(1);

        $service = $this->getContainer()->get('app.subscription.service');

        try {
            $service->create($subscriptionRequest);
            static::fail('There should have been a validation error');

        } catch (ValidationException $v) {
            $errors = $v->getValidationErrors();
            static::assertCount(1, $errors);
        }
    }
}