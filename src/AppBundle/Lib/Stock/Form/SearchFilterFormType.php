<?php


namespace AppBundle\Lib\Stock\Form;


use AppBundle\Form\Types\IntArrayType;
use AppBundle\Form\Types\TextArrayType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $intArrayDescription = 'Accepts either a single integer value or a comma separated list of values';
        $countryDescription = 'Limits the search to product in country, accepts either a single country code or a comma separated list of countries';

        $builder
            ->add('item', IntArrayType::class, ['label' => 'item', 'description' => $intArrayDescription])
            ->add('country', TextArrayType::class, ['label' => 'country', 'description' => $countryDescription])
            ->add('quantity', IntegerType::class, ['label' => 'quantity', 'description' => 'Return only records with quantity'])
            ->add('at_date', DateType::class, ['label' => 'date_at', 'widget' => 'single_text', 'format' => 'yyyy-MM-dd' ,'description' => 'Records at date'])
            ->add('year', IntArrayType::class, ['label' => 'Year', 'description' => 'Limit selection to year'])
            ->add('month', IntArrayType::class, ['label' => 'Month', 'description' => 'Limit selection to month'])
            ->add('only_current', ChoiceType::class, ['label' => 'current_only', 'choices' => [0 => 0,1 => 1], 'choices_as_values' => true, 'required' => false, 'description' => 'limit selection to current records']);

    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults(['csrf_protection' => false, 'data_class' => SearchFilterDto::class]);
    }
}