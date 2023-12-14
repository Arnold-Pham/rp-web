<?php

namespace App\Form;

use App\Entity\PersonalData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PersonalDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        isset($options['genre']) ? $genre = $options['genre'] : $genre = 'Homme';
        isset($options['company']) ? $company = $options['company'] : $company = null;

        $genre == 'Homme' ? $sexe = 1 : $sexe = 0;

        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type d\'utilisateur',
                'attr' => ['placeholder' => ''],
                'choices' => [
                    'Particulier' => 0,
                    'Entreprise' => 1,
                ],
                'required' => true,
                'expanded' => true,
                'row_attr' => [
                    'class' => 'mb-3'
                ]
            ])
            ->add('company', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Nom de l\'entreprise',
                'mapped' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ],
                'data' => $company
            ])
            ->add('lastname', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Nom de famille',
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
            ->add('phoneNumber', NumberType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Numéro de téléphone',
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
                ],
                'data' => $sexe
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
