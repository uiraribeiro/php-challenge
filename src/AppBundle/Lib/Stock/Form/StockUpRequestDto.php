<?php


namespace AppBundle\Lib\Stock\Form;

use AppBundle\Entity\Item;
use AppBundle\Lib\Stock\Validator\StockUpRequestValid;
use AppBundle\Lib\Tools;

/**
 * Class StockUpRequestDto
 * @package AppBundle\Lib\Item\Form
 * @StockUpRequestValid()
 */
class StockUpRequestDto
{
    public const MODE_REPLACE = 0;
    public const MODE_UPDATE = 1;

    /**
     * @var Item|null
     */
    private $item;

    /**
     * @var string[]|null
     */
    protected $countries;

    /**
     * @var int|null
     */
    protected $quantity;

    /**
     * @var \DateTime|null
     */
    protected $from;

    /**
     * @var \DateTime|null
     */
    protected $until;

    /**
     * @var string|null
     */
    protected $mode;

    /**
     * @return Item|null
     */
    public function getItem(): ?Item
    {
        return $this->item;
    }

    /**
     * @param Item|null $item
     * @return StockUpRequestDto
     */
    public function setItem(?Item $item): StockUpRequestDto
    {
        $this->item = $item;
        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getCountries(): ?array
    {
        return $this->countries;
    }

    /**
     * @param string[]|null $countries
     * @return StockUpRequestDto
     */
    public function setCountries(?array $countries): StockUpRequestDto
    {
        $this->countries = $countries;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param int|null $quantity
     * @return StockUpRequestDto
     */
    public function setQuantity(?int $quantity): StockUpRequestDto
    {
        if ($quantity !== null) {
            $quantity = (int) abs($quantity);
        }

        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getFrom(): ?\DateTime
    {
        return $this->from;
    }

    /**
     * @param \DateTime|null $from
     * @return StockUpRequestDto
     */
    public function setFrom(?\DateTime $from): StockUpRequestDto
    {
        if ($from instanceof \DateTime) {
            $from = Tools::getFirstDayDateTimeForMonth($from);
        }

        $this->from = $from;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getUntil(): ?\DateTime
    {
        return $this->until;
    }

    /**
     * @param \DateTime|null $until
     * @return StockUpRequestDto
     */
    public function setUntil(?\DateTime $until): StockUpRequestDto
    {
        if ($until instanceof \DateTime) {
            $until = Tools::getLastDayDateTimeForMonth($until);
        }

        $this->until = $until;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMode(): ?string
    {
        return $this->mode;
    }

    /**
     * @param string|null $mode
     * @return StockUpRequestDto
     */
    public function setMode(?string $mode): StockUpRequestDto
    {
        $this->mode = $mode;

        return $this;
    }
}