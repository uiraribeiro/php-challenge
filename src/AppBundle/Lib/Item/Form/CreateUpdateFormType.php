<?php


namespace AppBundle\Lib\Item\Form;


use AppBundle\Entity\Item;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateUpdateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'name', 'required' => true])
            ->add('description', TextType::class, ['label' => 'description', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver) :void
    {
        $resolver->setDefaults(['data_class' => Item::class, 'csrf_protection' => false]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}