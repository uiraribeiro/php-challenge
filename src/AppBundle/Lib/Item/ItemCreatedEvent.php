<?php


namespace AppBundle\Lib\Item;


use AppBundle\Entity\Item;
use Symfony\Component\EventDispatcher\Event;

class ItemCreatedEvent extends Event
{
    public const NAME = 'item.created';

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
     * @return ItemCreatedEvent
     */
    public function setItem(Item $item): ItemCreatedEvent
    {
        $this->item = $item;
        return $this;
    }

    public function addMessage(string $message) :void
    {
        self::$messages[] = $message;
    }
}