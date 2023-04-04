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
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => [
                    'placeholder' => 'Entrez un mot de passse provisoire'
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Length([
                        'min' => 2, 
                        'max' => 30,
                        'minMessage' => 'Votre Prénom contient moins de 2 lettres ?',
                        'maxMessage' => 'Votre Prénom est trop long !' 
                    ]),
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre Prénom !'
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
                        'max' => 30,
                        'minMessage' => 'Votre Nom contient moins de 2 lettres ?',
                        'maxMessage' => 'Votre Nom est trop long !' 
                    ]),
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre Nom !'
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
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn-alice g-recaptcha',
                    'data-sitekey' => 'reCAPTCHA_site_key',
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
