<?php

namespace App\Form;

use App\Entity\Contact;
use libphonenumber\PhoneNumberFormat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber;
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
            
            ->add('phone', PhoneNumberType::class, [
                'label' => 'Téléphone',
                'required' => true,
                'default_region' => 'FR',
                'format' => PhoneNumberFormat::INTERNATIONAL,
                'attr' => [
                    'placeholder' => 'Numéro de téléphone',
                    'maxlength' => '17',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner un numéro de téléphone.'
                    ]),
                    new PhoneNumber([
                        'message' => 'Numéro de téléphone invalide.'
                    ]),
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
