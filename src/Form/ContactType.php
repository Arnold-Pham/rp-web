<?php

namespace App\Form;


use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
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
                'constraints' => [
                    new NotBlank(),
                    new Email(['message' => 'L\'email {{ value }} n\'est pas une adresse valide']),
                ],
                'label' => 'Email',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('sujet', TextType::class, [
                'attr' => ['placeholder' => ''],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                        'max' => 100,
                        'minMessage' => 'Le sujet est trop court',
                        'maxMessage' => 'Le sujet est trop long'
                    ])
                ],
                'label' => 'Sujet',
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('reservation', TextType::class, [
                'attr' => ['placeholder' => ''],
                'constraints' => [
                    new Length([
                        'min' => 16,
                        'max' => 16
                    ])
                ],
                'label' => 'N° Réservation',
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            ->add('texte', TextareaType::class, [
                'attr' => [
                    'placeholder' => '',
                    'style' => 'height: 200px'
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 20,
                        'max' => 2000,
                        'minMessage' => 'Le message est trop court.',
                        'maxMessage' => 'Le message est trop long.'
                    ])
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
            'data_class' => Contact::class
        ]);
    }
}
