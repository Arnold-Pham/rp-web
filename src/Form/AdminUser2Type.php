<?php

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;


class AdminUser2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        isset($options['region']) ? $region = $options['region'] : $region = 7;

        $builder
            ->add('role', ChoiceType::class, [
                'attr' => ['placeholder' => ''],
                'choices' => [
                    'Voiturier' => 0,
                    'Administrateur' => 1
                ],
                'label' => 'Role',
                'mapped' => false,
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('lastname', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Nom',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('firstname', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Prénom',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Nom',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('phoneNumber', NumberType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Numéro de téléphone',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('region',  ChoiceType::class, [
                'attr' => ['placeholder' => ''],
                'choices' => [
                    'Métropole' => [
                        'Auvergne-Rhône-Alpes' => 0,
                        'Bourgogne-Franche-Comté' => 1,
                        'Bretagne' => 2,
                        'Centre-Val de Loire' => 3,
                        'Corse' => 4,
                        'Grand Est' => 5,
                        'Hauts-de-France' => 6,
                        'Ile-de-France' => 7,
                        'Normandie' => 8,
                        'Nouvelle-Aquitaine' => 9,
                        'Occitanie' => 10,
                        'Pays de la Loire' => 11,
                        'Provence-Alpes-Côte d\'Azur' => 12,
                    ],
                    'Outre-Mer' => [
                        'Guadeloupe' => 13,
                        'Guyane' => 14,
                        'Martinique' => 15,
                        'La Réunion' => 16,
                        'Mayotte' => 17
                    ]
                ],
                'data' => $region,
                'label' => 'Région',
                'mapped' => false,
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('genre',  ChoiceType::class, [
                'attr' => ['placeholder' => ''],
                'choices' => [
                    'M' => 1,
                    'Mme' => 0
                ],
                'label' => 'Genre',
                'mapped' => false,
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'region' => null
        ]);
    }
}
