<?php


namespace AppBundle\Lib\Subscription;

use AppBundle\Entity\Store;
use AppBundle\Entity\Subscription;
use Symfony\Component\EventDispatcher\Event;

class SubscriptionCreatedEvent extends Event
{
    public const NAME = 'subscription.created';

    public static $messages = [];

    /**
     * @var Subscription
     */
    protected $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
        self::$messages = [];
    }

    /**
     * @return Subscription
     */
    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    public function addMessage(string $message) :void
    {
        self::$messages[] = $message;
    }
}