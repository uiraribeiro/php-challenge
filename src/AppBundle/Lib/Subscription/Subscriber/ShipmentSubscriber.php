<?php


namespace AppBundle\Lib\Subscription\Subscriber;


use AppBundle\Entity\Shipment;
use AppBundle\Lib\Subscription\SubscriptionCreatedEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class ShipmentSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    protected $manager;

    use LoggerAwareTrait;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
        $this->setLogger(new NullLogger());
    }


    public static function getSubscribedEvents():array
    {
        return [
            SubscriptionCreatedEvent::NAME => 'onSubscriptionCreated'
        ];
    }

    /**
     * Creates entries in the shipments relation for the given subscription.
     *
     * @param SubscriptionCreatedEvent $event
     */
    public function onSubscriptionCreated(SubscriptionCreatedEvent $event) :void
    {
        $repo = $this->manager->getRepository(Shipment::class);
        $subscription = $event->getSubscription();

        try {
            $repo->createShipments($subscription);
            $msg = 'Init shipments for subscription ('.$subscription->getId().')';

            $event->addMessage($msg);
            $this->logger->info($msg);

        } catch (\Exception $e) {
            $this->logger->error('Failed to create shipments for subscription: '.$subscription->getId());
        }
    }
}