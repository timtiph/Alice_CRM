<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Partner;
use App\Entity\Customer;
use App\Entity\TariffZone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => 'Nom',
            'constraints' => [
                new NotBlank([
                    'message' => 'Ce champs ne peut pas être vide.'
                ]),
            ]
        ])
        ->add('siret', TextType::class, [
            'label' => 'SIREN ou SIRET',
            'required' => false, 
            'constraints' => [
                new Callback([
                    'callback' => function ($numSirenSiret, ExecutionContextInterface $context) {
                        if (!empty($numSirenSiret)) { // On vérifie si le champ est non vide
                            // Suppression des espaces entre les groupes de chiffres
                            $numSirenSiret = str_replace(' ', '', $numSirenSiret);
                            // Validation du numéro SIREN/SIRET
                            $regex = '/^(?:\d{9}|\d{14})$/';
                            if (!preg_match($regex, $numSirenSiret)) {
                                $context->addViolation('Le numéro SIREN/SIRET n\'est pas valide');
                            }
                        }
                    },
                    'payload' => null,
                ]),
            ],
            'attr' => [
                'oninput' => "this.value=this.value.replace(/[^0-9 ]+/g,'').replace(/^(\d{3}) ?(\d{3}) ?(\d{3}) ?(\d{0,5}).*/, '$1 $2 $3 $4').trim()",
                'maxlength' => '18',
                'inputmode' => 'numeric'
            ],
        ])
        ->add('address', TextType::class, [
            'label' => 'Adresse',
            'constraints' => [
                new NotBlank([
                    'message' => 'Ce champ ne peut pas être vide.'
                ]),
                new Regex([
                    'pattern' => '/^[a-zA-Z0-9\s]*$/',
                    'message' => 'Ce champ ne peut contenir que des lettres et des chiffres.'
                ]),
                new Length([
                    'max' => 255,
                    'maxMessage' => 'Votre saisie est trop longue.'
                ]),
            ]
        ])
        ->add('zipCode', TextType::class, [
            'label' => 'Code Postal',
            'constraints' => [
                new Regex([
                    'pattern' => '/^[A-Z0-9]{4,6}$/',
                    'message' => 'Le code postal doit comporter entre 4 et 6 caractères, composés de lettres majuscules et/ou de chiffres.'
                ]),
                new NotBlank([
                    'message' => 'Ce champ ne peut pas être vide.'
                ]),
            ],
        ])
        ->add('city', TextType::class, [
            'label' => 'Ville',
            'constraints' => [
                new NotBlank([
                    'message' => 'Ce champ ne peut pas être vide.'
                ]),
                new Regex([
                    'pattern' => '/^[a-zA-Z0-9]*$/',
                    'message' => 'Ce champ ne peut contenir que des lettres et des chiffres.'
                ]),
                new Length([
                    'max' => 255,
                    'maxMessage' => 'Votre saisie est trop longue.'
                ]),
            ]
        ])
        ->add('country', CountryType::class, [
            'label' => 'Pays',
            'constraints' => [
                new NotBlank([
                    'message' => 'Ce champs ne peut pas être vide.'
                ])
            ],
            'data' => 'FR'
        ])
        ->add('isProfessional', CheckboxType::class, [
            'label' => 'Client Professionnel',
            'required' => false
        ])
        ->add('isPartner', CheckboxType::class, [
            'label' => 'Client Partenaire',
            'required' => false
        ])
        ->add('partner', EntityType::class, [
            'label' => 'Patenariat : ',
            'class' => Partner::class,
            'required' => true,
            'multiple' => false,
            'expanded' => false
        ])
        ->add('tariffZone', EntityType::class, [
            'label' => 'Zone Tarifaire : ',
            'class' => TariffZone::class,
            'required' => true,
            'multiple' => false,
            'expanded' => false
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
            'user' => User::class
        ]);
    }
}
