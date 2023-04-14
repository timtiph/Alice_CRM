<?php

namespace App\Form;

use App\Entity\Contract;
use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Regex;

class EditContractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $customer = $options['customer'];
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du contrat'
            ])
            ->add('date', DateType::class, [
                'label' => 'Date du contrat'
            ])
            ->add('amountCharged', MoneyType::class, [
                'label' => 'Montant facturé',
            ])
            ->add('timeCharged', NumberType::class, [
                'label' => 'Durée facturée',
                'invalid_message' => 'Veuillez saisir un nombre.'
            ])
            ->add('amountReal', MoneyType::class, [
                'label' => 'Montant réel'
            ])
            ->add('timeReal', NumberType::class, [
                'label' => 'Durée Réelle',
                'invalid_message' => 'Veuillez saisir un nombre.'
            ])
            ->add('websiteLink', UrlType::class, [
                'label' => 'lien site client',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
                        'message' => 'Veuillez saisir une URL valide.'
                    ])
                ],
            ])
            ->add('paymentFrequency', ChoiceType::class, [
                'label' => 'fractionnement',
                'choices' => [
                    'OneShot' => 'OneShot',
                    'Mensuel' => 'mensuel',
                    'Trimestriel' => 'trimestriel',
                    'Annuel' => 'annuel'
                ]
            ])
            ->add('openArea', TextareaType::class, [
                'label' => 'Commentaires',
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
            'data_class' => Contract::class,
            'customer' => Customer::class,
        ]);
    }
}
