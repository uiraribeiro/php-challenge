<?php


namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractSearchSortFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
            ->add('sort_by', ChoiceType::class, ['choices' => $this->getSortChoices(), 'required' => true, 'choices_as_values' => true])
            ->add('direction', ChoiceType::class, ['choices' => $this->getDirections(), 'required' => true, 'choices_as_values' => true]);
    }

    public function configureOptions(OptionsResolver $resolver) :void
    {
        $resolver->setDefaults(['csrf_protection' => false, 'data_class' => SearchSortDto::class]);
    }

    abstract public function getSortChoices() :array;

    public function getDirections() :array
    {
        $d = ['ASC', 'DESC'];

        return array_combine($d, $d);
    }
}