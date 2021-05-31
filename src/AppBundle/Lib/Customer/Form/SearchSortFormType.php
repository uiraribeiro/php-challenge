<?php


namespace AppBundle\Lib\Customer\Form;


use AppBundle\Form\AbstractSearchSortFormType;

class SearchSortFormType extends AbstractSearchSortFormType
{

    public function getSortChoices(): array
    {
        $s = ['name', 'id', 'created_at'];

        return array_combine($s, $s);
    }
}