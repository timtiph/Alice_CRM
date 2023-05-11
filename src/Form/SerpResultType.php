<?php

namespace App\Form;

use App\Entity\SerpResult;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SerpResultType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('googleRank', HiddenType::class)
            ->add('SerpInfo', HiddenType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer les résultats dans la base',
                'attr' => [
                    'class' => 'btn btn-alice w-100',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SerpResult::class,
        ]);
    }
}
