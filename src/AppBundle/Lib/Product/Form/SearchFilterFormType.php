<?php


namespace AppBundle\Lib\Product\Form;


use AppBundle\Form\Types\IntArrayType;
use AppBundle\Form\Types\TextArrayType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $intArrayDescription = 'Accepts either a single integer value or a comma separated list of values';
        $countryDescription = 'Limits the search to product in country, accepts either a single country code or a comma separated list of countries';

        $builder
            ->add('product', IntArrayType::class, ['label' => 'product', 'description' => $intArrayDescription])
            ->add('item', IntArrayType::class, ['label' => 'item', 'description' => $intArrayDescription])
            ->add('store', IntArrayType::class, ['label' => 'store', 'description' => $intArrayDescription])
            ->add('country', TextArrayType::class, ['label' => 'country', 'description' => $countryDescription])
            ->add('runtime', IntegerType::class, ['label' => 'country', 'description' => 'Return only products with minimum subscription time'])
            ->add('name', TextType::class, ['label' => 'name', 'description' => 'Form name'])
            ->add('search', TextType::class, ['label' => 'search', 'description' => 'Wildcard search']);

    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults(['csrf_protection' => false, 'data_class' => SearchFilterDto::class]);
    }
}