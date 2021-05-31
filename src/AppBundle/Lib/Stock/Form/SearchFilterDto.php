<?php


namespace AppBundle\Lib\Stock\Form;


use AppBundle\Lib\Tools;

class SearchFilterDto
{
    /**
     * @var array|null
     */
    private $item;

    /**
     * @var array|null
     */
    private $country;

    /**
     * @var \DateTime|null
     */
    private $at_date;

    /**
     * @var int|null
     */
    private $quantity;

    /**
     * @var int|null
     */
    private $only_current;

    /**
     * @var array|null
     */
    private $year;

    /**
     * @var array|null
     */
    private $month;

    /**
     * @return array|null
     */
    public function getItem(): ?array
    {
        return $this->item;
    }

    /**
     * @param array|null $item
     * @return SearchFilterDto
     */
    public function setItem(?array $item): SearchFilterDto
    {
        $this->item = $item;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getCountry(): ?array
    {
        return $this->country;
    }

    /**
     * @param array|null $country
     * @return SearchFilterDto
     */
    public function setCountry(?array $country): SearchFilterDto
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getAtDate(): ?\DateTime
    {
        return $this->at_date;
    }

    /**
     * @param \DateTime|null $at_date
     * @return SearchFilterDto
     */
    public function setAtDate(?\DateTime $at_date): SearchFilterDto
    {
        if ($at_date instanceof \DateTime) {
            Tools::getFirstDayDateTimeForMonth($at_date);
        }

        $this->at_date = $at_date;

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
     * @return SearchFilterDto
     */
    public function setQuantity(?int $quantity): SearchFilterDto
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getOnlyCurrent(): ?int
    {
        return $this->only_current;
    }

    /**
     * @param int|null $only_current
     * @return SearchFilterDto
     */
    public function setOnlyCurrent(?int $only_current): SearchFilterDto
    {
        $this->only_current = $only_current;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getYear(): ?array
    {
        return $this->year;
    }

    /**
     * @param array|null $year
     * @return SearchFilterDto
     */
    public function setYear(?array $year): SearchFilterDto
    {
        $this->year = $year;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getMonth(): ?array
    {
        return $this->month;
    }

    /**
     * @param array|null $month
     * @return SearchFilterDto
     */
    public function setMonth(?array $month): SearchFilterDto
    {
        $this->month = $month;
        return $this;
    }



    public function toArray() :array
    {
        $properties = array_keys(get_class_vars(self::class));

        $r = [];
        foreach ($properties AS $key) {
            if ($this->$key === null) {
                continue;
            }

            $r[$key] = $this->$key;
        }

        return $r;
    }

}