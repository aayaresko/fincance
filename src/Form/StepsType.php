<?php

namespace App\Form;

use App\Dto\StepsDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StepsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('depositAmount', NumberType::class, ['label' => 'steps.deposit_amount'])
            ->add('number', NumberType::class, ['label' => 'steps.number'])
            ->add('firstStepPercent', NumberType::class, ['label' => 'steps.first_step_percent']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StepsDto::class,
            'validation_group' => 'steps_for_deposit'
        ]);
    }
}
