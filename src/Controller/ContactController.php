<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\User;
use App\Form\ContactType;
use App\Form\EditContactType;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


class ContactController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /*
    * Retoune les contacts
    */ 

    #[Route('/admin/contact', name: 'app_contacts')]
    public function showContacts(): Response
    {

        $contacts = $this->entityManager->getRepository(Contact::class)->findAll();
        
        return $this->render('admin/contact_list.html.twig', [
            'contacts' => $contacts,
        ]);
    }


    #[Route('/admin/contact/{id}/{slug}', name: 'app_contact')]

    public function showContact($id) 
    {

        $contact = $this->entityManager->getRepository(Contact::class)->findOneById($id);
        
        // récup user associé
        $user = $contact->getUser();
        $customer = $user->getCustomer();
        

        if(!$contact) { // si tu ne trouve pas de ID, redirect to app_contacts (liste des contacts)
            return $this->redirectToRoute('app_contacts');
        }

        return $this->render('admin/contact_show.html.twig', [
            'contact' => $contact,
            'user' => $user,
            'customer' => $customer
        ]);

    }
   
    // Creer un contact

    #[Route('/admin/contact/creer-un-contact', name: 'app_contact_add')]
    public function createContact(Request $request, EntityManagerInterface $entityManager, PersistenceManagerRegistry $doctrine): Response
    {
        $contact = new Contact();
        
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted()){

            if ($form->isValid()) {

                $contact = $form->getData();

                $fullname = $contact->getFirstname()." ".$contact->getLastname();
                $slugify = new Slugify();
                $slugify = $slugify->slugify($fullname);
                $contact->setSlug($slugify);

                

                $entityManager = $doctrine->getManager();
                $entityManager->persist($contact); //figer les données 
                $entityManager->flush(); // push les données



                $this->addFlash(
                    'success',
                    'La Création du contact et bien enregistrée.'
                );

                return $this->redirectToRoute('app_contacts');
           } 
            else {

                $this->addFlash(
                    'alert',
                    'Une Erreur est survenue, veuillez recommencer.'
                );
                return $this->redirectToRoute('app_contact_add');
            }

        }
        
        return $this->render('admin/contact_new.html.twig', [
            'form' => $form->createView(), 
            'flash' => $this,
            'user' => $contact->getUser(),
            'customer' => $contact->getUser(),
        ]);
    }
    
    
    // Modifier 

    #[Route('/admin/contact/modifier-un-contact/{id}/{slug}', name: 'app_contact_edit')]
    public function editContact(Request $request, $id, $slug): Response
    {
        $contact = $this->entityManager->getRepository(Contact::class)->findOneById($id);
        $user = $contact->getUser();
        $customer = $user->getCustomer();

        // vérification 2 points : 1. est ce que l'adresse existe OU est ce que cette adresse appartient au User connecté ? sinon, redirect
        if (!$contact) {
            return $this->redirectToRoute('app_contact'); 
        }

        $form = $this->createForm(EditContactType::class, $contact);

        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_contact', array('id' => $id, 'slug' => $slug));
        }

        return $this->render('admin/contact_edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'customer' => $customer
        ]);
    }
}
