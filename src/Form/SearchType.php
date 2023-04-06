<?php

namespace App\Form;

use App\Class\Search;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('string', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher un client',
                    'class' => ''
                ],
            ])
            // ->add('customers', EntityType::class, [
            //     'label' => false,
            //     'required' => false,
            //     'class' => Customer::class,
            //     'multiple' => true,
            //     'expanded' => true,
            //     'attr' => [
            //         'class' => ''
            //     ]
            // ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => [
                    'class' => 'btn-block btn-alice-lg'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);

        
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
