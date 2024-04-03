<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdateFormType extends AbstractType
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
                'attr' => [
                    'placeholder' => 'Entrez votre prénom',
                    'class' => 'form-control',
                ],
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
                'attr' => [
                    'placeholder' => 'Entrez votre nom',
                    'class' => 'form-control',
                ],
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
                'attr' => [
                    'placeholder' => 'Entrez votre email',
                    'class' => 'form-control',
                ],
            ])
            ->add('change_password', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Changer le mot de passe',
                'attr' => [
                    'class' => 'change-password-checkbox form-check-input',
                ],
            ])
            ->add('old_password', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Ancien mot de passe',
                'constraints' => [
                    new NotBlank([
                        'message' => 'L\'ancien mot de passe est requis.',
                        'groups' => ['password_change'],
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Entrez votre ancien mot de passe',
                    'class' => 'form-control',
                    'disabled' => true,
                ],
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Nouveau mot de passe',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nouveau mot de passe est requis.',
                        'groups' => ['password_change'],
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit contenir au moins 8 caractères.',
                        'max' => 1024,
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Entrez votre nouveau mot de passe',
                    'class' => 'form-control',
                    'disabled' => true,
                ],
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
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Numéro de rue',
                    'class' => 'form-control',
                ],
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
                'attr' => [
                    'placeholder' => 'Entrez le nom de votre rue',
                    'class' => 'form-control',
                ],
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
                'attr' => [
                    'placeholder' => 'Entrez votre code postal',
                    'class' => 'form-control',
                ],
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
                'attr' => [
                    'placeholder' => 'Entrez votre ville',
                    'class' => 'form-control',
                ],
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le pays est requis.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (fichier PNG ou JPG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (PNG ou JPG)',
                    ]),
                ],
                'attr' => [
                    'class' => 'btn btn-outline-primary btn-sm radius-30 px-4',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
