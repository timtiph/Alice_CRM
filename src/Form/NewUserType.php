<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class NewUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'label' => 'Email',
            'attr' => [
                'placeholder' => 'Entrez votre adresse email'
            ],
            'constraints' => [
                new Length([
                    'min' => 5, 
                    'max' => 255,
                    'minMessage' => 'Votre email contient moins de 5 caractères ?',
                    'maxMessage' => 'Votre email est trop long !' 
                ]),
                new NotBlank([
                    'message' => 'Veuillez renseigner votre adresse email.'
                ]),
                new Regex([
                    'pattern' => '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z]{2,4}$/',
                    'message' => 'L\'adresse email "{{ value }}" n\'est pas valide.',
                ]),
            ],
            'invalid_message' => 'Veuillez saisir une adresse mail valide.'
        ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => [
                    'placeholder' => 'Entrez un mot de passse provisoire'
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
                // TODO : revoir cette partie sur le MDP provisoire .............
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Length([
                        'min' => 2, 
                        'minMessage' => 'Le Prénom contient moins de {{ limit }} caractères ?',
                        'max' => 30,
                        'maxMessage' => 'Le Prénom contient plus de {{ limit }} caractères ?'
                    ]),
                    new NotBlank([
                        'message' => 'Veuillez renseigner un Prénom !'
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
                'label' => 'Nom',
                'constraints' => [
                    new Length([
                        'min' => 2, 
                        'minMessage' => 'Le Prénom contient moins de {{ limit }} caractères ?',
                        'max' => 30,
                        'maxMessage' => 'Le Prénom contient plus de {{ limit }} caractères ?'
                    ]),
                    new NotBlank([
                        'message' => 'Veuillez renseigner un Prénom !'
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
            ->add('role', ChoiceType::class, [
                'label' => 'Statut',
                'choices'  => [
                    'COMPTA' => "COMPTA",
                    'COLLAB' => "COLLAB",
                    'CLIENT' => "CLIENT",
                ],
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn-alice',
                    'data-callback' => 'onSubmit',
                    'data-action' => 'submit',
                    'class' => 'btn-alice-form'
                ]
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
