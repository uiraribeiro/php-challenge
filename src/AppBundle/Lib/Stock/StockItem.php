<?php


namespace AppBundle\Lib\Stock;


class StockItem
{
    /**
     * @var int
     */
    private $item_id;

    /**
     * @var string
     */
    private $country_id;

    /**
     * @var \DateTime
     */
    private $at_date;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var int
     */
    private $total_ordered;

    /**
     * @var int
     */
    private $total_orders;

    /**
     * StockItem constructor.
     * @param array $data
     * @throws \Exception
     */
    public function __construct(array $data = [])
    {
        foreach ($data AS $k => $v) {
            if (!is_string($k)) {
                continue;
            }

            if (!property_exists($this, $k)) {
                continue;
            }

            if ($k === 'at_date') {
                $v = new \DateTime($v);
            }

            if (in_array($k, ['item_id', 'quantity', 'total_ordered', 'total_orders'])) {
                $v = (int) $v;
            }

            $this->$k = $v;
        }
    }

    /**
     * @return int
     */
    public function getItemId(): int
    {
        return $this->item_id;
    }

    /**
     * @return string
     */
    public function getCountryId(): string
    {
        return $this->country_id;
    }

    /**
     * @return \DateTime
     */
    public function getAtDate(): \DateTime
    {
        return $this->at_date;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return int
     */
    public function getTotalOrdered(): int
    {
        return $this->total_ordered;
    }

    /**
     * @return int
     */
    public function getTotalOrders(): int
    {
        return $this->total_orders;
    }

    /**
     * @return int
     */
    public function getAvailable() :int
    {
        return $this->quantity - $this->total_ordered;
    }

}