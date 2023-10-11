<?php

namespace App\Form;

use DateTime;
use DateTimeZone;
use App\Entity\Airport;
use App\Entity\Parking;
use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => ['placeholder' => ''],
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('dateA', DateTimeType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Date Aller',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ],
                'model_timezone' => 'Europe/Paris',
                'widget' => 'single_text',
                // 'time_widget' => 'choice',
                'minutes' => [0, 15, 30, 45],
                'view_timezone' => 'Europe/Paris',
                "data" => new DateTime('now', new DateTimeZone('Europe/Paris')),
            ])
            ->add('flightA', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'N° de Vol Aller',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('dateB', DateTimeType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Date Retour',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ],
                'model_timezone' => 'Europe/Paris',
                'widget' => 'single_text',
                // 'time_widget' => 'choice',
                'minutes' => [0, 15, 30, 45],
                'view_timezone' => 'Europe/Paris',
                "data" => new DateTime('now', new DateTimeZone('Europe/Paris')),
            ])
            ->add('flightB', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'N° de Vol Retour',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('airport', EntityType::class, [
                'attr' => ['placeholder' => ''],
                'choice_label' => 'name',
                'class' => Airport::class,
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('valet', CheckboxType::class, [
                'label' => 'Voiturier',
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('extra1', CheckboxType::class, [
                'label' => 'Nettoyage Intérieur',
                'mapped' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('extra2', CheckboxType::class, [
                'label' => 'Nettoyage Extérieur',
                'mapped' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('extra3', CheckboxType::class, [
                'label' => 'Pleins d\'essence',
                'mapped' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('parking', EntityType::class, [
                'attr' => ['placeholder' => ''],
                'choice_label' => 'name',
                'class' => Parking::class,
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
