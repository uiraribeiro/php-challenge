<?php


namespace AppBundle\Lib\Stock\Form;


use AppBundle\Entity\Item;
use AppBundle\Form\Types\TextArrayType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockUpRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('item', EntityType::class, ['class' => Item::class, 'choice_label' => 'id', 'required' => true])
            ->add('countries', TextArrayType::class, ['label' => 'countries', 'required' => true, 'description' => 'Comma separated list of country codes eg.: DE,AT,CH ..'])
            ->add('quantity', IntegerType::class, ['label' => 'quantity', 'required' => true])
            ->add('from', DateType::class, ['label' => 'from', 'widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'required' => true])
            ->add('until', DateType::class, ['label' => 'until', 'widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'required' => true])
            ->add('mode', ChoiceType::class, ['label' => 'recurring', 'choices' => [0 => 0,1 => 1], 'choices_as_values' => true, 'required' => false, 'empty_data' => 0, 'description' => 'Select the stock up mode 0 = replace or 1 update existing values']);
    }

    public function configureOptions(OptionsResolver $resolver) :void
    {
        $resolver->setDefaults(['data_class' => StockUpRequestDto::class, 'csrf_protection' => false]);
    }
}