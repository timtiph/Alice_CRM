<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DocumentSecondType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du document'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('fileName', FileType::class, [
                'label' => 'Télécharger votre document',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the file
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '20M',
                        'mimeTypes' => [
                            'application/pdf',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier PDF ou image valide',
                    ])
                ],
            ])
            ->add('family', ChoiceType::class, [
                'label' => 'Famille de document',
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Facture' => 'Facture',
                    'Maquette' => 'Maquette',
                    'Devis' => 'Devis',
                    'Rapport' => 'Rapport'
                ]
            ])
            ->add('user', EntityType::class, [
                'label' => 'Pour qui ce document est-il destiné ?',
                'class' => User::class,
                'multiple' => true,
                'expanded' => true,
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
