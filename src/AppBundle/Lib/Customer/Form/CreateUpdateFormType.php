<?php


namespace AppBundle\Lib\Customer\Form;


use AppBundle\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateUpdateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder->add('name', TextType::class, ['label' => 'Name', 'required' => true]);
    }

    public function configureOptions(OptionsResolver $resolver) :void
    {
        $resolver->setDefaults(['csrf_protection' => false, 'data_class' => Customer::class]);
    }

    public function getBlockPrefix() :string
    {
        return '';
    }
}