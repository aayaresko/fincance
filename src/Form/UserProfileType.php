<?php

namespace App\Form;

use App\Entity\Subscription\CurrencySubscription;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
//use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entities              = $options['selected_subscriptions'];
        $selectedSubscriptions = [];
        foreach ($entities as $selectedSubscription) {
            if ($selectedSubscription instanceof CurrencySubscription) {
                $selectedSubscriptions[] = $selectedSubscription->getId();
            } else {
                $selectedSubscriptions[] = $selectedSubscription;
            }
        }

        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'disabled' => true
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'disabled' => true
                ]
            )
            /*->add(
                'plain_password',
                PasswordType::class
            )*/
            ->add(
                'currency_subscriptions',
                EntityType::class,
                [
                    'class'        => CurrencySubscription::class,
                    'choice_label' => 'name',
                    'expanded'     => true,
                    'multiple'     => true,
                    'choice_attr'  => function ($val, $key, $index) use ($selectedSubscriptions) {
                        /** @var CurrencySubscription $val */
                        return [
                            'class'   => 'form-check-input',
                            'checked' => in_array($val->getId(), $selectedSubscriptions) ? true : false,
                        ];
                    }
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('selected_subscriptions');

        $resolver->setDefaults([
            // uncomment if you want to bind to a class
            'data_class'        => User::class,
            'validation_groups' => false,
        ]);
    }
}
