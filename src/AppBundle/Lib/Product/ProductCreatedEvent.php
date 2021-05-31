<?php


namespace AppBundle\Lib\Product;


use AppBundle\Entity\Product;
use Symfony\Component\EventDispatcher\Event;

class ProductCreatedEvent extends Event
{
    public const NAME = 'product.created';

    public static $messages = [];

    /**
     * @var Product
     */
    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
        self::$messages = [];
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return ProductCreatedEvent
     */
    public function setProduct(Product $product): ProductCreatedEvent
    {
        $this->product = $product;
        return $this;
    }

    public function addMessage(string $message) :void
    {
        self::$messages[] = $message;
    }
}