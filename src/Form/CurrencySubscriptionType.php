<?php

namespace App\Form;

use App\Entity\Currency;
use App\Entity\Subscription\CurrencySubscription;
use App\Service\SubscriptionService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CurrencySubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add(
                'period',
                ChoiceType::class,
                [
                    'choices' => SubscriptionService::getAvailablePeriods()
                ]
            )
            ->add(
                'currency',
                EntityType::class,
                [
                    'class'        => Currency::class,
                    'choice_label' => 'name',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // uncomment if you want to bind to a class
            'data_class' => CurrencySubscription::class,
        ]);
    }
}
