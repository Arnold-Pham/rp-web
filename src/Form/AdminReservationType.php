<?php

namespace App\Form;


use App\Entity\Place;
use App\Entity\Airport;
use App\Entity\Parking;
use App\Entity\Reservation;
use App\Entity\PersonalData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


class AdminReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $extra = 0;

        if (isset($options['extra'])) $extra = $options['extra'];
        $extra & 1 ? $extra1 = true : $extra1 = false;
        $extra & 2 ? $extra2 = true : $extra2 = false;
        $extra & 4 ? $extra3 = true : $extra3 = false;

        $builder
            ->add('code', TextType::class, [
                'attr' => ['placeholder' => ''],
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('email', EmailType::class, [
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
                'minutes' => [0, 15, 30, 45],
                'view_timezone' => 'Europe/Paris',
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
                'minutes' => [0, 15, 30, 45],
                'view_timezone' => 'Europe/Paris',
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
                'label' => 'Aéroport',
                'choice_label' => 'name',
                'class' => Airport::class,
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('valet', CheckboxType::class, [
                'attr' => [
                    'checked' => 'checked',
                    'class' => 'defcoch'
                ],
                'label' => 'Voiturier',
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('extra1', CheckboxType::class, [
                'data' => $extra1,
                'label' => 'Nettoyage Intérieur',
                'mapped' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col defbloc'
                ]
            ])
            ->add('extra2', CheckboxType::class, [
                'data' => $extra2,
                'label' => 'Nettoyage Extérieur',
                'mapped' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col defbloc'
                ]
            ])
            ->add('extra3', CheckboxType::class, [
                'data' => $extra3,
                'label' => 'Pleins d\'essence',
                'mapped' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col defbloc'
                ]
            ])
            ->add('parking', EntityType::class, [
                'attr' => ['placeholder' => ''],
                'choice_label' => 'name',
                'class' => Parking::class,
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col defnone'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'attr' => ['placeholder' => ''],
                'choices' => [
                    'En attente' => 'Awaiting',
                    'Annulée' => 'Canceled',
                    'Finie' => 'Done'
                ],
                'data' => 'Awaiting',
                'label' => 'Status',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('personalData', EntityType::class, [
                'class' => PersonalData::class,
                'choice_label' => 'id',
                'placeholder' => 'Aucune',
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'id',
                'placeholder' => 'Aucune',
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ],
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'extra' => 0
        ]);
    }
}
