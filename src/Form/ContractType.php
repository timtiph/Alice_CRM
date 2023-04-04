<?php

namespace App\Form;

use App\Entity\Contract;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContractType extends AbstractType
{
    private $customerRepository;
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
        // dd($customerRepository);
        
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //dd($options);
        $customer = $options['customer'];
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
            ->add('timeCharged', NumberType::class, [
                'label' => 'Durée facturée',
            ])
            ->add('amountReal', MoneyType::class, [
                'label' => 'Montant réel'
            ])
            ->add('timeReal', NumberType::class, [
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
            // ->add('customer', EntityType::class, [
            //     'label' => 'Client : ',
            //     'class' => Customer::class,
            //     'required' => true,
            //     'multiple' => false,
            //     'expanded' => false,
            //     'attr' => [
            //         'placeholder' => $customer,
            //         ]
            // ])
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
