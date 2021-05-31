<?php


namespace AppBundle\Lib\Customer\Form;


use AppBundle\Form\Types\IntArrayType;
use AppBundle\Form\Types\TextArrayType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $intArrayDescription = 'Accepts either a single integer value or a comma separated list of values';
        $countryDescription = 'Limits the search to customers with stores in country, accepts either a single country code or a comma separated list of countries';
        $itemActiveDescription = 'Limits the search to customers which have item booked, accepts either a single item name or a comma separated list of items';

        $builder
            ->add('customer', IntArrayType::class, ['label' => 'customer', 'description' => $intArrayDescription])
            ->add('store', IntArrayType::class, ['label' => 'store', 'description' => $intArrayDescription])
            ->add('country', TextArrayType::class, ['label' => 'country', 'description' => $countryDescription])
            ->add('item_active', TextArrayType::class, ['label' => 'country', 'description' => $itemActiveDescription])
            ->add('name', TextType::class, ['label' => 'name', 'description' => 'Form name'])
            ->add('search', TextType::class, ['label' => 'search', 'description' => 'Wildcard search']);

    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults(['csrf_protection' => false, 'data_class' => SearchFilterDto::class]);

    }
}