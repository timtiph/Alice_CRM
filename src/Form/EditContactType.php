<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class EditContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Length(['min' => 2, 'max' => 30]),
                    new NotBlank([
                        'message' => 'Veuillez renseigner un Prénom !'
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'John'
                ],

            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Length(['min' => 2, 'max' => 30]),
                    new NotBlank([
                        'message' => 'Veuillez renseigner un Nom !'
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Doe'
                ],
                
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'example@mail.com'
                ],
                // 'constraints' => [
                //     new Length(['min' => 2, 'max' => 30]),
                //     new NotBlank([
                //         'message' => 'Veuillez renseigner votre adresse email !'
                //     ]),
                //     new Regex([
                //         // on accept alfa num . _ -  +@ et alfa num +. 2 à 4 (.com, .fr, .asso)
                //         'pattern' => '/^[a-b0-9.-_]+@[a-b0-9.-_]+\.[a-z]{2,4}$/i',
                //         'message' => 'Veuillez saisir une adresse Email valide'
                //     ]),
                // ],
                'invalid_message' => 'Veuillez renseigner une adresse email valide',
            ])
            // 
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => '00 00 00 00 00'
                ],
                // 'constraints' => [
                //     new Length(['min' => 0, 'max' => 30]),
                //     new Regex([
                //         'pattern' => '/^(\+|0)[1-9][0-9 \-\(\)\.]{7,32}$/g',
                //         'message' => 'Numéro de téléphone invalide : doit commencer par 0 ou +"indicatif"'
                //     ]),
                // ],
            ])
            ->add('position', TextType::class,[
                'label' => 'Fonction de l\'interlocuteur',
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
