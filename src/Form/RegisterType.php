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
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Votre Prénom',
                'constraints' => [
                    new Length([
                        'min' => 2, 
                        'minMessage' => 'Votre Prénom contient moins de {{ limit }} caractères ?',
                        'max' => 30,
                        'maxMessage' => 'Votre Prénom contient plus de {{ limit }} caractères ?'
                    ]),
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre Prénom !'
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\-\s]+$/u',
                        'message' => 'Ce champs ne peut contenir que des lettres et tirets.'
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'John'
                ],
                
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre Nom',
                'constraints' => [
                    new Length([
                        'min' => 2, 
                        'minMessage' => 'Votre Nom contient moins de {{ limit }} caractères ?',
                        'max' => 30,
                        'maxMessage' => 'Votre Nom contient plus de {{ limit }} caractères ?'
                    ]),
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre Nom !'
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\-\s]+$/u',
                        'message' => 'Ce champs ne peut contenir que des lettres et tirets.'
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
                    new Length([
                        'min' => 5, 
                        'max' => 255,
                        'minMessage' => 'Votre email contient moins de 5 caractères ?',
                        'maxMessage' => 'Votre email est trop long !' 
                    ]),
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
                        // longueure min 8 max 20
                        new Length([
                            'min' => 8, 
                            'max' => 20,
                            'minMessage' => 'Le mot depasse doit contenir au moins 8 caractères',
                            'maxMessage' => 'Le mot depasse doit contenir moins de 20 caractères' 
                        ]),
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
                        new Length([
                            'min' => 8, 
                            'max' => 20,
                            'minMessage' => 'Le mot depasse doit contenir au moins 8 caractères',
                            'maxMessage' => 'Le mot depasse doit contenir moins de 20 caractères'  
                        ]),
                        new NotBlank([
                            'message' => 'Merci de confirmer votre mot de passe !'
                        ]),
                    ],
                    
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire',
                'attr' => [
                    'class' => 'btn-alice g-recaptcha',
                    'data-sitekey' => 'reCAPTCHA_site_key',
                    'data-callback' => 'onSubmit',
                    'data-action' => 'submit',
                    'class' => 'btn-alice-form'
                ]
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3([
                    'message' => 'Il y a eu problème avec votre captcha. S\'il vous plait, essayez à nouveau.'
                ]),
                'action_name' => 'inscription',
                'locale' => 'fr',
            ]);
        ;
    }



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
