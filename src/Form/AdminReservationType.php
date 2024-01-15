<?php

namespace App\Form;

use App\Entity\Airport;
use App\Entity\Option;
use App\Entity\Parking;
use App\Entity\PersonalData;
use App\Entity\Place;
use App\Entity\Reservation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('code')
            ->add('dateA')
            ->add('dateB')
            ->add('flightA')
            ->add('flightB')
            ->add('status')
            ->add('valet')
            ->add('dateC')
            ->add('option', EntityType::class, [
                'class' => Option::class,
                'choice_label' => 'id',
            ])
            ->add('personalData', EntityType::class, [
                'class' => PersonalData::class,
                'choice_label' => 'id',
            ])
            ->add('airport', EntityType::class, [
                'class' => Airport::class,
                'choice_label' => 'id',
            ])
            ->add('parking', EntityType::class, [
                'class' => Parking::class,
                'choice_label' => 'id',
            ])
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'id',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
