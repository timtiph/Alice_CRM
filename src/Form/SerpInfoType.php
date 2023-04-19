<?php

namespace App\Form;

use App\Entity\SerpInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SerpInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('keyword', TextType::class, [
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot clé',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 400,
                        'minMessage' => 'Votre mot clé contient moins de {limit} lettres ?',
                        'maxMessage' => 'Votre mot clé doit contenir moins de {limit} lettres !'
                    ])
                ],
            ])

            ->add('save', SubmitType::class, [
            'label_html' => true,
            'label' => 'Ajouter',
            'attr' => [
                'class' => 'btn btn-outline-primary',]]);
            ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SerpInfo::class,
        ]);
    }
}
