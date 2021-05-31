<?php


namespace AppBundle\Lib\Store;


use AppBundle\Entity\Store;
use Symfony\Component\EventDispatcher\Event;

class StoreCreatedEvent extends Event
{
    public const NAME = 'store.created';

    public static $messages = [];

    /**
     * @var Store
     */
    protected $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
        self::$messages = [];
    }

    /**
     * @return Store
     */
    public function getStore(): Store
    {
        return $this->store;
    }

    public function addMessage(string $message) :void
    {
        self::$messages[] = $message;
    }
}