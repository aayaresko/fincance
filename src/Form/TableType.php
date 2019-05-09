<?php

namespace App\Form;

use App\Dto\TableDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fontSize', NumberType::class, ['label' => 'table.font_size'])
            ->add('numberOfRows', NumberType::class, ['label' => 'table.number_of_rows'])
            ->add('numberOfColumns', NumberType::class, ['label' => 'table.number_of_columns']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TableDto::class,
            'validation_group' => 'build_table'
        ]);
    }
}
