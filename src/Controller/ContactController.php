<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

// Retoune les contacts

    #[Route('/admin/contacts', name: 'app_contacts')]
    public function index(): Response
    {

        $contacts = $this->entityManager->getRepository(Contact::class)->findAll();
    

        return $this->render('admin/contact.html.twig', [
            'contacts' => $contacts
        ]);
    }


    #[Route('/admin/contact/fiche{id}', name: 'app_contact')]

    public function show($id) 
    {

        $contact = $this->entityManager->getRepository(Contact::class)->findOneById($id);

        if(!$contact) { // si tu ne trouve pas de ID, redirect to app_contacts (liste des contacts)
            return $this->redirectToRoute('app_contacts');
        }
        //dd($contact);

        return $this->render('admin/contact_show.html.twig', [
            'contact' => $contact
        ]);

    }
   
    // Creer un contact

    #[Route('/admin/contact/creer-un-contact', name: 'app_contact_add')]
    public function createContact(Request $request, EntityManagerInterface $entityManager, PersistenceManagerRegistry $doctrine): Response
    {

        $contact = new Contact();
        
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($contact); //figer les données 
            $entityManager->flush(); // push les données

            $this->addFlash(
                'success',
                'La Création du contact et bien enregistrée.'
            );
        } else {
            $this->addFlash(
                'alert',
                'Une Erreur est survenue, veuillez recommencer.'
            );
            return $this->redirectToRoute('app_contact_add');
        }
        
        return $this->render('admin/contact_new.html.twig', [
            'form' => $form->createView(), 
            'flash' => $this 
        ]);
    }
    
    
    // Modifier 

    #[Route('/admin/contact{id}/modifier-un-contact', name: 'app_contact_edit')]
    public function editContact(Request $request, $id): Response
    {
        $contact = $this->entityManager->getRepository(Contact::class)->findOneById($id);

        // vérification 2 points : 1. est ce que l'adresse existe OU est ce que cette adresse appartient au User connecté ? sinon, redirect
        if (!$contact || $contact->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_contact'); 
        }

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('admin/contact_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
