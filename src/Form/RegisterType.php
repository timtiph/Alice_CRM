<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Votre Prénom',
                'constraints' => [
                    new Length(['min' => 2, 'max' => 30]),
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre Prénom !'
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'John'
                ],
                
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre Nom',
                'constraints' => [
                    new Length(['min' => 2, 'max' => 30]),
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre Nom !'
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Doe'
                ],
                
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre Email',
                'attr' => [
                    'placeholder' => 'example@mail.com'
                ],
                'constraints' => [
                    new Length(['min' => 2, 'max' => 30]),
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre adresse email !'
                    ]),
                    new Regex([
                        // on accept alfa num . _ -  +@ et alfa num +. 2 à 4 (.com, .fr, .asso)
                        'pattern' => '/^[a-b0-9.-_]+@[a-b0-9.-_]+\.[a-z]{2,4}$/i',
                        'message' => 'Veuillez saisir une adresse Email valide'
                    ]),
                ],
                'invalid_message' => 'Veuillez renseigner une adresse email valide',
            ])

            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => 'Votre mot de passe',
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'Merci de saisir votre mot de passe'
                    ],
                    'constraints' => [
                        // longueure min 2 max 30
                        new Length(['min' => 2, 'max' => 30]),
                        // invalide si null
                        new NotBlank([
                            'message' => 'Veuillez renseigner un mot de passe !'
                        ]),
                        // Oblige à entrer un MDP avec 8 à 20 char + 1 maj + 1 min + chiffre + caractere spé 
                        new Regex([
                            'pattern' => '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,20}$/',
                            'message' => 'Votre mot de passe doit contenir 1 majuscule, 1 minuscule, 1 caractère spécial, 1 chiffre et doit être composé de 8 à 20 caractères'
                        ]),
                    ],
                ],
                'invalid_message' => 'Votre mot de passe et la confirmation doivent être identiques',
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe',
                    'attr' => [
                        'placeholder' => 'Merci de confirmer votre mot de passe'
                    ],
                    'constraints' => [
                        new Length(['min' => 2, 'max' => 30]),
                        new NotBlank([
                            'message' => 'Merci de confirmer votre mot de passe !'
                        ]),
                    ],
                    
                ]
            ])


            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire'
            ])
        ;
    }

    // Ajout de contraintes à définir : https://symfony.com/doc/current/validation.html#constraints

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => [
                'novalidate' => "novalidate"
            ]
        ]);
    }
}
