<?php


namespace AppBundle\Lib\Product\Form;


use AppBundle\Entity\Country;
use AppBundle\Entity\Item;
use AppBundle\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateUpdateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
            ->add('item', EntityType::class, ['class' => Item::class, 'choice_label' => 'id', 'required' => true])
            ->add('country', EntityType::class, ['class' => Country::class, 'choice_label' => 'id', 'required' => true])
            ->add('notes', TextType::class, ['label' => 'Notes', 'required' => true])
            ->add('price', IntegerType::class, ['label' => 'Price', 'required' => true, 'description' => 'price in cent'])
            ->add('vat', NumberType::class, ['label' => 'vat', 'required' => true])
            ->add('available_from', DateTimeType::class, ['label' => 'From', 'required' => true, 'widget' => 'single_text', 'date_format' => 'yyyy-MM-dd'])
            ->add('available_until',DateTimeType::class, ['label' => 'Until', 'required' => true, 'widget' => 'single_text', 'date_format' => 'yyyy-MM-dd'])
            ->add('min_subscription_time', IntegerType::class, ['label' => 'Subscription time', 'required' => true])
            ->add('min_quantity',IntegerType::class, ['label' => 'Minimum Quantity', 'required' => true])
            ->add('shipping_costs', CollectionType::class, ['entry_type' => ShippingCostsType::class, 'allow_add' => true, 'allow_delete' => true]);
    }

    public function configureOptions(OptionsResolver $resolver) :void
    {
        $resolver->setDefaults(['csrf_protection' => false, 'data_class' => Product::class]);
    }

    public function getBlockPrefix() :string
    {
        return '';
    }
}