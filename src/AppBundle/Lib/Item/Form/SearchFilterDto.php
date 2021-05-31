<?php


namespace AppBundle\Lib\Item\Form;


class SearchFilterDto
{
    /**
     * @var integer[]
     */
    private $item;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer[]
     */
    private $product;

    /**
     * @var integer
     */
    private $runtime;

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
     * @return integer[]
     */
    public function getItem(): ?array
    {
        return $this->item;
    }

    /**
     * @param integer[] $item
     * @return SearchFilterDto
     */
    public function setItem(?array $item): SearchFilterDto
    {
        $this->item = $item;
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
    public function getProduct(): ?array
    {
        return $this->product;
    }

    /**
     * @param integer[] $product
     * @return SearchFilterDto
     */
    public function setProduct(?array $product): SearchFilterDto
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return int
     */
    public function getRuntime(): ?int
    {
        return $this->runtime;
    }

    /**
     * @param int $runtime
     * @return SearchFilterDto
     */
    public function setRuntime(?int $runtime): SearchFilterDto
    {
        $this->runtime = $runtime;
        return $this;
    }

    /**
     * @return integer[]
     */
    public function getStore(): ?array
    {
        return $this->store;
    }

    /**
     * @param integer[] $store
     * @return SearchFilterDto
     */
    public function setStore(?array $store): SearchFilterDto
    {
        $this->store = $store;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getCountry(): ?array
    {
        return $this->country;
    }

    /**
     * @param string[] $country
     * @return SearchFilterDto
     */
    public function setCountry(?array $country): SearchFilterDto
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getSearch(): ?string
    {
        return $this->search;
    }

    /**
     * @param string $search
     * @return SearchFilterDto
     */
    public function setSearch(?string $search): SearchFilterDto
    {
        $this->search = $search;
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