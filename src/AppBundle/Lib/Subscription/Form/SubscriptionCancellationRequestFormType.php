<?php


namespace AppBundle\Lib\Subscription\Form;


use AppBundle\Entity\Subscription;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionCancellationRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
            ->add('subscription', EntityType::class, ['class' => Subscription::class, 'choice_label' => 'id', 'required' => true])
            ->add('reason', TextType::class, ['label' => 'Reason', 'required' => true])
            ->add('confirm', ChoiceType::class, ['label' => 'recurring', 'choices' => [0 => 0,1 => 1], 'choices_as_values' => true, 'required' => false, 'description' => 'Confirm cancellation'])
            ->add('at_date', DateType::class, ['label' => 'cancellation date', 'required' => false, 'widget' => 'single_text', 'format' => 'yyyy-MM-dd']);
    }

    public function configureOptions(OptionsResolver $resolver) :void
    {
        $resolver->setDefaults(['csrf_protection' => false, 'data_class' => SubscriptionCancellationRequestDto::class]);
    }

    public function getBlockPrefix() :string
    {
        return '';
    }

}