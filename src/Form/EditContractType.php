<?php

namespace App\Form;

use App\Entity\Contract;
use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EditContractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => 'Nom du contrat'
        ])
        ->add('date', DateType::class, [
            'label' => 'Date du contrat'
        ])
        ->add('amountCharged', MoneyType::class, [
            'label' => 'Montant facturé'
        ])
        ->add('timeCharged', TextType::class, [
            'label' => 'Durée facturée',
        ])
        ->add('amountReal', MoneyType::class, [
            'label' => 'Montant réel'
        ])
        ->add('timeReal', TextType::class, [
            'label' => 'Durée Réelle',
        ])
        ->add('websiteLink', TextType::class, [
            'label' => 'lien site client'
        ])
        ->add('paymentFrequency', ChoiceType::class, [
            'label' => 'fractionnement',
            'choices' => [
                'OneShot' => 'OneShot',
                'mensuel' => 'mensuel',
                'trimestriel' => 'trimestriel',
                'annuel' => 'annuel'
            ]
        ])
        ->add('openArea', TextareaType::class, [
            'label' => 'Remarques'
        ])
        ->add('customer', EntityType::class, [
            'label' => 'Client : ',
            'class' => Customer::class,
            'required' => true,
            'multiple' => false,
            'expanded' => false
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
        ]);
    }
}
