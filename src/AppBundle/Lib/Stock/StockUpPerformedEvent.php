<?php


namespace AppBundle\Lib\Stock;


use AppBundle\Lib\Stock\Form\StockUpRequestDto;
use Symfony\Component\EventDispatcher\Event;

class StockUpPerformedEvent extends Event
{
    public const NAME = 'stock.stockup_performed';

    public static $messages = [];

    /**
     * @var StockUpRequestDto
     */
    protected $stockUpRequest;

    public function __construct(StockUpRequestDto $stockUpRequest)
    {
        self::$messages = [];
        $this->stockUpRequest = $stockUpRequest;
    }

    public function addMessage(string $message) :void
    {
        self::$messages[] = $message;
    }

    /**
     * @return StockUpRequestDto
     */
    public function getStockUpRequest(): StockUpRequestDto
    {
        return $this->stockUpRequest;
    }

    /**
     * @param StockUpRequestDto $stockUpRequest
     * @return StockUpPerformedEvent
     */
    public function setStockUpRequest(StockUpRequestDto $stockUpRequest): StockUpPerformedEvent
    {
        $this->stockUpRequest = $stockUpRequest;
        return $this;
    }
}