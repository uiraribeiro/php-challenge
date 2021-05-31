<?php


namespace AppBundle\Form;


use Symfony\Component\Validator\Constraints as Assert;

class SearchSortDto
{
    /**
     * @var string
     * @Assert\Expression("this.getSortBy() in this.getValidFields()", message="Sort value not valid")
     */
    protected $sortBy;

    /**
     * @var string
     * @Assert\Choice(choices={"ASC", "DESC"}, message="You must provide a valid sort direction")
     */
    protected $direction = 'ASC';

    protected $validFields = [];

    public function __construct(array $validFields = [], string $defaultField = '')
    {
        if ($defaultField !== '') {
            $this->setSortBy($defaultField);
        }

        $this->setValidFields($validFields);
    }

    /**
     * @return string
     */
    public function getSortBy(): ?string
    {
        return $this->sortBy;
    }

    /**
     * @param string $sortBy
     * @return SearchSortDto
     */
    public function setSortBy(?string $sortBy): SearchSortDto
    {
        $this->sortBy = $sortBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getDirection() :?string
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     * @return SearchSortDto
     */
    public function setDirection(?string $direction): SearchSortDto
    {
        $this->direction = $direction;
        return $this;
    }

    /**
     * @return array
     */
    public function getValidFields(): array
    {
        return $this->validFields;
    }

    /**
     * @param array $validFields
     * @return SearchSortDto
     */
    public function setValidFields(array $validFields): SearchSortDto
    {
        $this->validFields = $validFields;
        return $this;
    }


    /**
     * @return array|string[]
     */
    public function toArray() :array
    {
        if ($this->sortBy === null || !in_array($this->sortBy, $this->validFields, true)) {
            return [];
        }

        $direction = $this->direction;

        if ($direction === null || !in_array(strtoupper($direction), ['ASC', 'DESC'])) {
            $direction = 'ASC';
        }

        return [$this->sortBy => $direction];
    }

}