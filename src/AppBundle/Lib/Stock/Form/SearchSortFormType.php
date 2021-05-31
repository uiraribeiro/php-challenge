<?php


namespace AppBundle\Lib\Stock\Form;


use AppBundle\Form\AbstractSearchSortFormType;

class SearchSortFormType extends AbstractSearchSortFormType
{

    public function getSortChoices(): array
    {
        $s = ['country', 'item', 'date_at'];

        return array_combine($s, $s);
    }
}