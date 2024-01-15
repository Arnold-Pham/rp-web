<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Car;
use App\Entity\PersonalData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminPersonalDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname')
            ->add('firstname')
            ->add('phoneNumber')
            ->add('type')
            ->add('companyName')
            ->add('gender')
            ->add('car', EntityType::class, [
                'class' => Car::class,
                'choice_label' => 'id',
            ])
            ->add('address', EntityType::class, [
                'class' => Address::class,
                'choice_label' => 'id',
            ])
            ->add('invoice', EntityType::class, [
                'class' => Address::class,
                'choice_label' => 'id',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonalData::class,
        ]);
    }
}
