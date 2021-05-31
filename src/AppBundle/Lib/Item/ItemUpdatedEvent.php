<?php


namespace AppBundle\Lib\Item;


use AppBundle\Entity\Item;
use Symfony\Component\EventDispatcher\Event;

class ItemUpdatedEvent extends Event
{
    public const NAME = 'item.updated';

    public static $messages = [];

    /**
     * @var Item
     */
    protected $item;

    public function __construct(Item $item)
    {
        $this->item = $item;
        self::$messages = [];
    }

    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * @param Item $item
     * @return ItemUpdatedEvent
     */
    public function setItem(Item $item): ItemUpdatedEvent
    {
        $this->item = $item;
        return $this;
    }

    public function addMessage(string $message) :void
    {
        self::$messages[] = $message;
    }
}