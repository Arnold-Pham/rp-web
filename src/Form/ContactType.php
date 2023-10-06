<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Email',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('sujet', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Sujet',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('resa', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'N° Réservation',
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('text', TextareaType::class, [
                'attr' => [
                    'placeholder' => '',
                    'style' => 'height: 200px'
                ],
                'label' => 'Message',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
