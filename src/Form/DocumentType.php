<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Document;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DocumentType extends AbstractType
{
    private $authChecker;

    public function __construct(AuthorizationCheckerInterface $authChecker)
    {
        $this->authChecker = $authChecker;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du document'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            
            ->add('family', ChoiceType::class, [
                'label' => 'Famille de document',
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Facture'       => 'Facture',
                    'Maquette'      => 'Maquette',
                    'Devis'         => 'Devis',
                    'Rapport'       => 'Rapport',
                    'Autre'         => 'Autre',
                ]
            ]);
            if (!$options['disable_file_upload']) { // went edit, for change file
                $builder
                ->add('fileName', FileType::class, [
                    'label' => 'Envoyer un document',
                    'attr' => [
                        'class' => 'mb-3',
                    ],
                    // unmapped means that this field is not associated to any entity property
                    'mapped' => false,
    
                    // make it optional so you don't have to re-upload the PDF file every time you edit the file
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
                ]);
            }

            // condition the display of the 'user' field according to the user's role
            if ($this->authChecker->isGranted('ROLE_ADMIN')) {
                $builder->add('user', EntityType::class, [
                    'label' => 'Pour qui ce document est-il destiné ?',
                    'class' => User::class,
                    'multiple' => true,
                    'expanded' => true,
                ]);
            } elseif ($this->authChecker->isGranted('ROLE_USER')) {
                $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                    $form = $event->getForm();
                    $form->remove('user');
                });
            }
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
            'disable_file_upload' => false, // option with a default value
        ]);
    }
}
