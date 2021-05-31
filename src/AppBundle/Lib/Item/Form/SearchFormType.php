<?php


namespace AppBundle\Lib\Item\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
            ->add('filter', SearchFilterFormType::class)
            ->add('sort', SearchSortFormType::class);
    }

    public function configureOptions(OptionsResolver $resolver) :void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(['csrf_protection' => false, 'data_class' => SearchDto::class]);
    }

    public function getBlockPrefix() :string
    {
        return '';
    }
}