<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Contact;
use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'disabled' => true
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
            ->add('role', ChoiceType::class, [
                'label' => 'Statut',
                'choices'  => [
                    'DEFAUT' => ' ',
                    'COMPTA' => "COMPTA",
                    'COLLAB' => "COLLAB",
                    'CLIENT' => "CLIENT",
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn-alice-form'
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
