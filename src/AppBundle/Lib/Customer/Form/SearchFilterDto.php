<?php


namespace AppBundle\Lib\Customer\Form;


class SearchFilterDto
{
    /**
     * @var integer[]
     */
    private $customer;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer[]
     */
    private $store;

    /**
     * @var string[]
     */
    private $country;

    /**
     * @var string
     */
    private $search;

    /**
     * @var string[]
     */
    private $itemActive;

    /**
     * @return integer[]
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param integer[] $customer
     * @return SearchFilterDto
     */
    public function setCustomer($customer): SearchFilterDto
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return SearchFilterDto
     */
    public function setName(?string $name): SearchFilterDto
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return integer[]
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @param integer[] $store
     * @return SearchFilterDto
     */
    public function setStore($store): SearchFilterDto
    {
        $this->store = $store;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string[] $country
     * @return SearchFilterDto
     */
    public function setCountry($country): SearchFilterDto
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param string $search
     * @return SearchFilterDto
     */
    public function setSearch($search): SearchFilterDto
    {
        $this->search = $search;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getItemActive()
    {
        return $this->itemActive;
    }

    /**
     * @param string[] $itemActive
     * @return SearchFilterDto
     */
    public function setItemActive($itemActive): SearchFilterDto
    {
        $this->itemActive = $itemActive;

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

            if ($key === 'itemActive') {
                $r['item_active'] = $this->$key;
                continue;
            }

            $r[$key] = $this->$key;
        }

        return $r;
    }
}