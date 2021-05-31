<?php


namespace AppBundle\Lib\Product\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class ShippingCostsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
            ->add('from', IntegerType::class, [
                'label' => 'from',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'from cannot be blank']),
                    new Type(['type' => 'integer']),
                    new GreaterThanOrEqual(['value' => 0])
                ]
            ])
            ->add('costs', IntegerType::class, [
                'label' => 'costs',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'costs cannot be blank']),
                    new Type(['type' => 'integer']),
                    new GreaterThanOrEqual(['value' => 0])
                ]
            ]);
    }
}