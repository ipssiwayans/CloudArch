<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{IsTrue, Length, NotBlank, Email, Regex, Type};

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le prénom est requis.',
                    ]),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => ['placeholder' => 'Entrez votre prénom']
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom est requis.',
                    ]),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => ['placeholder' => 'Entrez votre nom']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'L\'email est requis.',
                    ]),
                    new Email([
                        'message' => 'L\'email {{ value }} n\'est pas une adresse email valide.',
                    ]),
                ],
                'attr' => ['placeholder' => 'Entrez votre email']
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le mot de passe est requis.',
                    ]),
//                    new Regex([
//                        'pattern' => '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/',
//                        'message' => 'Le mot de passe doit contenir au moins 8 caractères et inclure au moins une lettre et un chiffre.',
//                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit contenir au moins 8 caractères.',
                        'max' => 1024,
                    ]),
                ],
                'attr' => ['placeholder' => 'Entrez votre mot de passe']
            ])
            ->add('street_number', NumberType::class, [
                'label' => 'Numéro de rue',
                'html5' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le numéro de rue est requis.',
                    ]),
                    new Length([
                        'max' => 10,
                        'maxMessage' => 'Le numéro de rue ne peut pas dépasser 10 caractères.',
                    ])
                ],
                'attr' => ['placeholder' => 'Numéro de rue']
            ])
            ->add('street_address', TextType::class, [
                'label' => 'Nom de la rue',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom de la rue est requis.',
                    ]),
                    new Length([
                        'max' => 200,
                        'maxMessage' => 'Le nom de la rue ne peut pas dépasser 200 caractères.',
                    ]),
                ],
                'attr' => ['placeholder' => 'Entrez le nom de votre rue']
            ])
            ->add('postal_code', NumberType::class, [
                'label' => 'Code postal',
                'html5' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le code postal est requis.',
                    ]),
                    new Length([
                        'max' => 10,
                        'maxMessage' => 'Le code postal ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => ['placeholder' => 'Entrez votre code postal']
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'constraints' => [
                    new NotBlank([
                        'message' => 'La ville est requise.',
                    ]),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'Le nom de la ville ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => ['placeholder' => 'Entrez votre ville']
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le pays est requis.',
                    ]),
                ],
                'placeholder' => 'Sélectionnez un pays',
            ])
        ->add('termsAgreed', CheckboxType::class, [
            'label' => ' Je suis d\'accord avec les termes et conditions',
            'mapped' => false, // Permet de ne pas lier cette propriété à aucune propriété de l'entité User
            'constraints' => [
                new IsTrue([
                    'message' => 'Vous devez accepter les termes.',
                ]),
            ],
            'required' => false,
            'attr' => ['class' => 'form-check-input'],
            'label_attr' => ['class' => 'form-check-label'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}