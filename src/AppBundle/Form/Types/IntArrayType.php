<?php


namespace AppBundle\Form\Types;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class IntArrayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder->addModelTransformer(new ExtendedTextToArrayTransformer(',', true, true, true));
    }

    public function getParent() :string
    {
        return TextType::class;
    }
}