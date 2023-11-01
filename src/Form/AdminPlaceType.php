<?php

namespace App\Form;


use App\Entity\Place;
use App\Entity\Parking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class AdminPlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'attr' => ['placeholder' => ''],
                'label' => 'Nom',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ])
            ->add('parking', EntityType::class, [
                'attr' => ['placeholder' => ''],
                'choice_label' => 'name',
                'class' => Parking::class,
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3 col'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
