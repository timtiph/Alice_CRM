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
   
    // Creer un contact à partir du USER avec user.id + user.slug

    #[Route('/admin/contact/creer-un-contact/{id}/{slug}', name: 'app_contact_add')]
    public function createContact(Request $request, EntityManagerInterface $entityManager, PersistenceManagerRegistry $doctrine, $id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->findOneById($id);
        $slug = $user->getSlug();
        
        $contact = new Contact();
        
        $form = $this->createForm(ContactType::class, $contact, [
            'user' => $user,
        ]);
        $contact->setUser($user);
        //dd($contact);
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

                return $this->redirectToRoute('app_user_show', ['id' => $id, 'slug' => $slug]);
           } 
        }
        //dd($contact->getUser());
        return $this->render('admin/contact_new.html.twig', array(
            'user' => $user,
            'flash' => $this,
            'form' => $form->createView(), 
        ));
    }
    
    
    // Modifier 

    #[Route('/admin/contact/modifier-un-contact/{id}/{slug}', name: 'app_contact_edit')]
    public function editContact(Request $request, $id, $slug): Response
    {
        $contact = $this->entityManager->getRepository(Contact::class)->findOneById($id);
        $user = $contact->getUser();
        $customer = $user->getCustomer();

        if (!$contact) {
            return $this->redirectToRoute('app_user_show', array('id' => $id, 'slug' => $slug)); 
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
