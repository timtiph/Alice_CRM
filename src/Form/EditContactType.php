<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class EditContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
                        'pattern' => '/^[a-zA-Z0-9_\-\s]+$/',
                        'message' => 'Le champ ne doit contenir que des lettres, des chiffres, des tirets et des underscores.'
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
                        'minMessage' => 'Le Nom contient seulement 1 caractère ?',
                        'max' => 30,
                        'maxMessage' => 'Le Nom contient plus de 30 caractères ?'
                    ]),
                    new NotBlank([
                        'message' => 'Veuillez renseigner un Nom !'
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9_\-\s]+$/',
                        'message' => 'Le champ ne doit contenir que des lettres, des chiffres, des tirets et des underscores.'
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Doe'
                ],
                
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Entrez votre adresse email'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre adresse email.'
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z]{2,4}$/',
                        'message' => 'L\'adresse email "{{ value }}" n\'est pas valide.',
                    ]),
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Numéro de téléphone',
                    'oninput' => "this.value=this.value.replace(/[^0-9 ]+/g,'').replace(/^(\d{2,4}) ?(\d{2}) ?(\d{2}) ?(\d{2}) ?(\d{2}) ?(\d{2}).*/, '$1 $2 $3 $4 $5 ?$6').trim()",
                    'maxlength' => '14',
                    'inputmode' => 'numeric'
                ],
                'help' => 'Veuillez saisir un numéro de téléphone valide, sans espaces, sans points, sans virgules. Si le numéro n\'est pas français, merci d\'ajouter l\'indicatif du pays.',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner un numéro de téléphone.'
                    ]),
                    new Length([
                        'min' => 9,
                        'max' => 13,
                        'minMessage' => 'Le numéro de téléphone doit comporter au moins {{ limit }} chiffres.',
                        'maxMessage' => 'Le numéro de téléphone ne doit pas comporter plus de {{ limit }} chiffres, avez-vous mis des espaces ?'
                    ]),
                    new Regex([
                        'pattern' => '/^[+ .-]*\d[\d\s]{7,13}\d[+ .-]*$/',
                        'message' => 'Numéro de téléphone invalide.'
                    ]),
                    new Callback([
                        'callback' => function($phone, ExecutionContextInterface $context) {
                            if (!ctype_digit(str_replace(['+', ' ', '-', '.'], '', $phone))) {
                                $context->buildViolation('Le numéro de téléphone ne doit contenir que des chiffres.')
                                    ->addViolation();
                            }
                        },
                        'payload' => null
                    ])
                ],
            ])
            
            
            ->add('position', TextType::class,[
                'label' => 'Fonction de l\'interlocuteur',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner une fonction !'
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9_\-\s]+$/',
                        'message' => 'Le champ ne doit contenir que des lettres, des chiffres, des tirets et des underscores.'
                    ]),
                ],
            ])
            ->add('isMain', CheckboxType::class, [
                'label' => 'Interlocuteur principal',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn-alice-form'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
