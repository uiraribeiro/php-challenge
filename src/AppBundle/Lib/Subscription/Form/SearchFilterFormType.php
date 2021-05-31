<?php


namespace AppBundle\Lib\Subscription\Form;


use AppBundle\Form\Types\IntArrayType;
use AppBundle\Form\Types\TextArrayType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $intArrayDescription = 'Accepts either a single integer value or a comma separated list of values';
        $countryDescription = 'Limits the search to stores in country, accepts either a single country code or a comma separated list of countries';

        $builder
            ->add('customer', IntArrayType::class, ['label' => 'customer', 'description' => $intArrayDescription])
            ->add('store', IntArrayType::class, ['label' => 'store', 'description' => $intArrayDescription])
            ->add('country', TextArrayType::class, ['label' => 'country', 'description' => $countryDescription])
            ->add('runtime', IntegerType::class, ['label' => 'country', 'description' => 'Return only subscription for products with subscription time'])
            ->add('recurring', ChoiceType::class, ['label' => 'recurring', 'choices' => [0 => 0,1 => 1], 'choices_as_values' => true, 'required' => false, 'description' => 'Return only recurring subscription'])
            ->add('name', TextType::class, ['label' => 'name', 'description' => 'Form name'])
            ->add('search', TextType::class, ['label' => 'search', 'description' => 'Wildcard search']);
    }

    public function configureOptions(OptionsResolver $resolver) :void
    {
        $resolver->setDefaults(['csrf_protection' => false, 'data_class' => SearchFilterDto::class]);
    }
}