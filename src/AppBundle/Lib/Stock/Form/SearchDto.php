<?php


namespace AppBundle\Lib\Stock\Form;


use AppBundle\Form\SearchSortDto;
use AppBundle\Lib\SearchDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SearchDto implements SearchDtoInterface
{

    /**
     * @var SearchFilterDto
     *
     * @Assert\Valid()
     */
    private $filter;

    /**
     * @Assert\Valid
     *
     * @var SearchSortDto
     */
    private $sort;

    public function __construct()
    {
        $this->filter = new SearchFilterDto();
        $this->sort = new SearchSortDto(['name', 'id', 'country', 'at_date']);
    }

    /**
     * @return SearchFilterDto
     */
    public function getFilter(): SearchFilterDto
    {
        return $this->filter;
    }

    /**
     * @param SearchFilterDto $filter
     * @return SearchDto
     */
    public function setFilter(SearchFilterDto $filter): SearchDto
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * @return SearchSortDto
     */
    public function getSort(): SearchSortDto
    {
        return $this->sort;
    }

    /**
     * @param SearchSortDto $sort
     * @return SearchDto
     */
    public function setSort(SearchSortDto $sort): SearchDto
    {
        $this->sort = $sort;
        return $this;
    }
}