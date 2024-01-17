<?php

namespace App\Form;


use NumberFormatter;
use App\Entity\Airport;
use App\Entity\Parking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;


class AdminParkingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Nom',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('dailyPrice', NumberType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Prix/Jour',
                'required' => true,
                'rounding_mode' => NumberFormatter::ROUND_HALFUP,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ],
                'scale' => 2
            ])
            ->add('airport', EntityType::class, [
                'attr' => ['placeholder' => ''],
                'choice_label' => 'name',
                'class' => Airport::class,
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Parking::class,
        ]);
    }
}
