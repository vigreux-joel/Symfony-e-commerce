<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Length(null, 3, 30)
                ],
                'attr' => [
                    'placeholder' => 'Votre prénom'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Length(null, 3, 30)
                ],
                'attr' => [
                    'placeholder' => 'Votre nom'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new Length(null, 3, 30),
                    new Email(
                        ['mode' => 'strict']
                    ),
                ],
                'attr' => [
                    'placeholder' => 'Votre email'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Length(null, 8),
                ],
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identique',
                'label' => 'Confirmez votre mot de passe',
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'Votre mot de passe'
                    ]
                    ],
                'second_options' => [
                    'label' => 'Confirmer votre mot de passe',
                    'attr' => [
                        'placeholder' => 'Confirmez votre mot de passe'
                    ]
                    ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => "S'inscrire"
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
