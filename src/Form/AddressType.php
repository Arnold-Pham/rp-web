<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->checkbox = $options['checkbox'];
        $this->iAddress = $options['iAddress'];
        $this->iCity = $options['iCity'];
        $this->iZip = $options['iZip'];

        $builder
            ->add('address', TextareaType::class, [
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
                'mapped' => false,
                'required' => false,
                'data' => $this->checkbox
            ])
            ->add(
                $builder->create('addressInvoice', FormType::class, [
                    'by_reference' => Address::class,
                    'mapped' => false
                ])
                    ->add('address_invoice', TextareaType::class, [
                        'attr' => ['placeholder' => ''],
                        'label' => 'Rue',
                        'mapped' => false,
                        'required' => true,
                        'row_attr' => [
                            'class' => 'form-floating mb-3'
                        ],
                        'data' => $this->iAddress
                    ])
                    ->add('city_invoice', TextType::class, [
                        'attr' => ['placeholder' => ''],
                        'label' => 'Ville',
                        'mapped' => false,
                        'required' => true,
                        'row_attr' => [
                            'class' => 'form-floating mb-3'
                        ],
                        'data' => $this->iCity
                    ])
                    ->add('zipCode_invoice', NumberType::class, [
                        'attr' => ['placeholder' => ''],
                        'label' => 'Code Postal',
                        'mapped' => false,
                        'required' => true,
                        'row_attr' => [
                            'class' => 'form-floating mb-3'
                        ],
                        'data' => $this->iZip
                    ])
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'checkbox' => false,
            'iAddress' => null,
            'iZip' => null,
            'iCity' => null
        ]);
    }
}
