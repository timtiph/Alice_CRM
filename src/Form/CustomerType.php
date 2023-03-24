<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => 'Nom complet',
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez renseigner le nom complet du client !'
                ]),
            ]
        ])
        ->add('siret', IntegerType::class, [
            'label' => 'SIREN ou SIRET',
            'required' => false, 
            'constraints' => [
                new Length([
                    'min' => 6,
                    'minMessage' => 'Le numéro est trop court',
                    'max' => 14,
                    'maxMessage' => 'Le Le numéro est trop long'
                ]),
                new Regex([
                    'pattern' => '/(\d{3}(\s*)?){3}(\d{5})?/',
                    'message' => 'Il semble que le numéro saisi soit incorrect.'
                ]),
            ],
        ])
        ->add('address', TextType::class, [
            'label' => 'Adresse',
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir l\'adresse du client.'
                ])
            ]
        ])
        ->add('postalCode', IntegerType::class, [
            'label' => 'Code Postal',
            'constraints' => [
                new Length([
                    'min' => 4,
                    'minMessage' => 'Le numéro est trop court',
                    'max' => 10,
                    'maxMessage' => 'Le Le numéro est trop long'
                ]),
                new Regex([
                    'pattern' => '/^(F-)?\d{4,10}$/',
                    'message' => 'Il semble que le numéro saisi soit incorrect.'
                ]),
                new NotBlank([
                    'message' => 'Veuillez saisir le code postal du client.'
                ]),
            ]
        ])
        ->add('city', TextType::class, [
            'label' => 'Ville',
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir la ville du client.'
                ])
            ]
        ])
        ->add('country', TextType::class, [
            'label' => 'Pays',
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir le pays du client.'
                ])
            ]
        ])
        ->add('isProfessional', CheckboxType::class, [
            'label' => 'Client Pro ?',
            'required' => false
        ])
        ->add('isPartner', CheckboxType::class, [
            'label' => 'Client Partenaire ?',
            'required' => false
        ])
        ->add('user', EntityType::class, [
            'label' => 'À quel utilisateur ce client est-il lié ?',
            'required' => true,
            'class' => User::class,
            'multiple' => false,
            'expanded' => true
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
            'data_class' => Customer::class,
            'attr' => [
                'novalidate' => "novalidate"
            ]
        ]);
    }
}
