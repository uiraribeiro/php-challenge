<?php


namespace AppBundle\Lib\Subscription\Form;


use AppBundle\Entity\Product;
use AppBundle\Entity\Store;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('store', EntityType::class, ['class' => Store::class, 'choice_label' => 'id', 'required' => true, 'description' => 'Store to add subscription too'])
            ->add('product', EntityType::class, ['class' => Product::class, 'choice_label' => 'id', 'required' => true])
            ->add('start', DateType::class, ['label' => 'Start date of the subscription', 'widget' => 'single_text', 'format' => 'yyyy-MM-dd','required' => false, 'description' => 'Leave blank for next month or start of the product'])
            ->add('quantity', IntegerType::class, ['label' => 'Quantity', 'required' => true])
            ->add('recurring', ChoiceType::class, ['label' => 'recurring', 'choices' => [0 => 0,1 => 1], 'choices_as_values' => true, 'required' => false, 'description' => 'Automatically renew subscription after expiration'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults(['csrf_protection' => false, 'data_class' => SubscriptionRequestDto::class]);
    }

    public function getBlockPrefix() :string
    {
        return '';
    }
}