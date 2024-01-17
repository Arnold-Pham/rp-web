<?php

namespace App\Form;


use App\Entity\Car;
use App\Entity\Address;
use App\Entity\PersonalData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class AdminPersonalDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        isset($options['genre']) ? $genre = $options['genre'] : $genre = 'Homme';
        isset($options['company']) ? $company = $options['company'] : $company = null;
        $genre == 'Homme' ? $sexe = 1 : $sexe = 0;

        $builder
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
                ],
                'data' => $sexe
            ])
            ->add('firstname', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Prénom',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('lastname', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Nom de famille',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('phoneNumber', NumberType::class, [
                'attr' => [
                    'class' => 'tel',
                    'placeholder' => ''
                ],
                'label' => 'Numéro de téléphone',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('Type', CheckboxType::class, [
                'label' => 'Réservation en tant qu\'Entreprise.',
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('company', TextType::class, [
                'attr' => [
                    'placeholder' => ''
                ],
                'label' => 'Nom de l\'entreprise',
                'mapped' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ],
                'data' => $company
            ])
            ->add('car', EntityType::class, [
                'attr' => [
                    'placeholder' => ''
                ],
                'class' => Car::class,
                'choice_label' => 'id',
                'label' => 'Voiture',
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ],
            ])
            ->add('address', EntityType::class, [
                'attr' => [
                    'placeholder' => ''
                ],
                'class' => Address::class,
                'choice_label' => 'id',
                'label' => 'Adresse',
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ],
            ])
            ->add('invoice', EntityType::class, [
                'attr' => [
                    'placeholder' => ''
                ],
                'class' => Address::class,
                'choice_label' => 'id',
                'label' => 'Adresse Facturation',
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonalData::class,
            'genre' => null,
            'company' => null,
        ]);
    }
}
