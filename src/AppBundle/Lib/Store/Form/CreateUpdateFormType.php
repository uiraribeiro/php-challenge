<?php


namespace AppBundle\Lib\Store\Form;


use AppBundle\Entity\Country;
use AppBundle\Entity\Store;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('description', TextType::class, ['label' => 'Description', 'empty_data' => '', 'required' => true])
            ->add('country', EntityType::class, ['class' => Country::class, 'choice_label' => 'id', 'required' => true]);
    }

    public function configureOptions(OptionsResolver $resolver) :void
    {
        $resolver->setDefaults(['csrf_protection' => false, 'data_class' => Store::class]);
    }

    public function getBlockPrefix() :string
    {
        return '';
    }
}