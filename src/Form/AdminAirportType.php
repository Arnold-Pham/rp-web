<?php

namespace App\Form;


use App\Entity\Airport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class AdminAirportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        isset($options['region']) ? $region = $options['region'] : $region = 7;

        $builder
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Nom de l\'Aéroport',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('iataCode', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Code de l\'Aéroport',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Airport::class,
            'region' => null
        ]);
    }
}
