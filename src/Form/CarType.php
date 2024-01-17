<?php

namespace App\Form;


use App\Entity\Car;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;


class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Marque de la voiture',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('model', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Modèle de la voiture',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('color',  ColorType::class, [
                'attr' => ['placeholder' => '', 'style' => 'width: 100%; padding-top: 12px'],
                'label' => 'Couleur',
                'label_attr' => ['style' => ''],
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('plate', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Plaque d\'imatriculation',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
        ]);
    }
}
