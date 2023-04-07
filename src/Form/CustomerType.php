<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Partner;
use App\Entity\Customer;
use App\Entity\TariffZone;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\EntityRepository;
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
                        // Suppression des espaces entre les groupes de chiffres
                        $numSirenSiret = str_replace(' ', '', $numSirenSiret);
                        // Validation du numéro SIREN/SIRET
                        $regex = '/^(?:\d{9}|\d{14})$/';
                        if (!preg_match($regex, $numSirenSiret)) {
                            $context->addViolation('Le numéro SIREN/SIRET n\'est pas valide');
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
                    'message' => 'Ce champs ne peut pas être vide.'
                ])
            ]
        ])
        // TODO : Revoir les pattern
        ->add('zipCode', IntegerType::class, [
            'label' => 'Code Postal',
            'constraints' => [
                new Length([
                    'min' => 4,
                    'minMessage' => 'Le numéro est trop court',
                    'max' => 10,
                    'maxMessage' => 'Le Le numéro est trop long'
                ]),
                new NotBlank([
                    'message' => 'Ce champs ne peut pas être vide.'
                ]),
            ]
        ])
        ->add('city', TextType::class, [
            'label' => 'Ville',
            'constraints' => [
                new NotBlank([
                    'message' => 'Ce champs ne peut pas être vide.'
                ])
            ]
        ])
        ->add('country', CountryType::class, [
            'label' => 'Pays',
            'constraints' => [
                new NotBlank([
                    'message' => 'Ce champs ne peut pas être vide.'
                ])
            ]
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
            'attr' => [
                'novalidate' => "novalidate"
            ],
            'user' => User::class
        ]);
    }
}
