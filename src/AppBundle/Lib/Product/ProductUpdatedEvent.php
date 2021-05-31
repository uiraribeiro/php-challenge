<?php


namespace AppBundle\Lib\Product;


use AppBundle\Entity\Product;
use AppBundle\Lib\Entity\ChangeList;
use Symfony\Component\EventDispatcher\Event;

class ProductUpdatedEvent extends Event
{
    public const NAME = 'product.updated';

    public static $messages = [];

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var ChangeList $changeList
     */
    protected $changeList;

    public function __construct(Product $product, ChangeList $changeList)
    {
        $this->product = $product;
        $this->changeList = $changeList;
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
     * @return ProductUpdatedEvent
     */
    public function setProduct(Product $product): ProductUpdatedEvent
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return ChangeList
     */
    public function getChangeList(): ChangeList
    {
        return $this->changeList;
    }

    public function addMessage(string $message) :void
    {
        self::$messages[] = $message;
    }
}