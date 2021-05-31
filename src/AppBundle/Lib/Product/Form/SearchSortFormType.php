<?php


namespace AppBundle\Lib\Product\Form;


use AppBundle\Form\AbstractSearchSortFormType;

class SearchSortFormType extends AbstractSearchSortFormType
{

    public function getSortChoices(): array
    {
        $s = ['name', 'id', 'created_at'];

        return array_combine($s, $s);
    }
}