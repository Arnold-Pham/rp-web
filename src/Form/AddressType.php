<?php

namespace App\Form;


use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        isset($options['checkbox']) ? $checkbox = $options['checkbox'] : $checkbox = 0;
        $checkbox ? $coche = true : $coche = false;

        $builder
            ->add('address', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Rue',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('city', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Ville',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('zipCode', NumberType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Code Postal',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('diff', CheckboxType::class, [
                'data' => $coche,
                'label' => 'Adresse de facturation différente',
                'mapped' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'checkbox' => false
        ]);
    }
}
