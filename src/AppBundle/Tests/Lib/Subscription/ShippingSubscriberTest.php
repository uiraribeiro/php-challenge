<?php


namespace AppBundle\Tests\Lib\Subscription;


use AppBundle\Entity\Subscription;
use AppBundle\Lib\Subscription\Subscriber\ShipmentSubscriber;
use AppBundle\Lib\Subscription\SubscriptionCreatedEvent;
use AppBundle\Lib\Tools;
use AppBundle\Tests\EntityManagerAwareTestCase;

class ShippingSubscriberTest extends EntityManagerAwareTestCase
{
    public function testCreateShipments() :void
    {
        $store = $this->getStoreByName('customer_1_store_1');

        if (!$store) {
            static::fail('Cannot test shipments, store customer_1_store_1 does not exist');
        }

        $item = $this->getItemByName('golden_delicious');

        if (!$item) {
            static::fail('Cannot test shipments, item golden_delicious does not exist');
        }

        $products = $item->getProductsBy($store->getCountryCode(), 12, true);

        if (0 === count($products)) {
            static::fail('Cannot test shipments, no products found');
        }

        $product = current($products);

        $start = Tools::getFirstDayDateTimeForMonth(new \DateTime('2020-04-01'));
        $end = clone $start;
        $end->add(new \DateInterval('P12M'));
        $end = Tools::getLastDayDateTimeForPreviousMonth($end);

        $subscription = new Subscription();
        $subscription->setStore($store);
        $subscription->setProduct($product);
        $subscription->setStartDate($start);
        $subscription->setEndDate($end);
        $subscription->setQuantity(100);

        $em = $this->getEntityManager();
        $em->persist($subscription);
        $em->flush();

        $event = new SubscriptionCreatedEvent($subscription);
        $subscriber = new ShipmentSubscriber($em);

        $subscriber->onSubscriptionCreated($event);
        $em->refresh($subscription);

        $shipments = $subscription->getShipments();
        static::assertCount(12, $shipments);

        static::assertCount(1, $event::$messages);
        static::assertEquals('Init shipments for subscription ('.$subscription->getId().')', $event::$messages[0]);
    }
}