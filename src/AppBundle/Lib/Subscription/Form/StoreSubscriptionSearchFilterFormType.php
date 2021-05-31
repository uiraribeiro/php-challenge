<?php


namespace AppBundle\Lib\Subscription\Form;


use Symfony\Component\Form\FormBuilderInterface;

class StoreSubscriptionSearchFilterFormType extends SearchFilterFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('country');
        $builder->remove('customer');
        $builder->remove('store');
    }
}