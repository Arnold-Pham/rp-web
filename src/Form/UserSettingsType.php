<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class UserSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'attr' => ['placeholder' => ''],
                'disabled' => true,
                'label' => 'Nom',
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('firstname', TextType::class, [
                'attr' => ['placeholder' => ''],
                'disabled' => true,
                'label' => 'Prénom',
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('phoneNumber', NumberType::class, [
                'attr' => ['placeholder' => ''],
                'disabled' => true,
                'label' => 'N° de téléphone',
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => ''],
                'disabled' => true,
                'label' => 'Email',
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('zone', TextType::class, [
                'attr' => ['placeholder' => ''],
                'disabled' => true,
                'label' => 'Région',
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('gender', TextType::class, [
                'attr' => ['placeholder' => ''],
                'disabled' => true,
                'label' => 'Genre',
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
