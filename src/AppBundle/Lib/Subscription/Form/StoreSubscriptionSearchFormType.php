<?php


namespace AppBundle\Lib\Subscription\Form;


use Symfony\Component\Form\FormBuilderInterface;

class StoreSubscriptionSearchFormType extends SearchFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        parent::buildForm($builder, $options);
        $builder->add('filter', StoreSubscriptionSearchFilterFormType::class);
    }

}