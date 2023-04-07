<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\User;
use App\Form\ContactType;
use App\Form\EditContactType;
use App\Repository\ContactRepository;
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
        
        // récup infos customer pour redirect si exist
        if($user->getCustomer()){

            $customer = $user->getCustomer();
            $customerId = $customer->getId();
            $customerSlug = $customer->getSlug();
            
        }

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
                
                if ($user->getCustomer()) {
                    // Return on Customer View if $customer is NOT Null
                    return $this->redirectToRoute('app_customer', ['id' => $customerId, 'slug' => $customerSlug]);
                } else {
                    // Return on User View if $customer is Null
                    return $this->redirectToRoute('app_user_show', ['id' => $id, 'slug' => $slug]);
                }
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
            $this->addFlash(
                'error',
                'Le contact n\'existe pas.'
            );
            return $this->redirectToRoute('app_user_show', array('id' => $id, 'slug' => $slug)); 
        }

        $form = $this->createForm(EditContactType::class, $contact);

        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash(
                'success',
                'La modification du contact est bien enregistrée.'
            );
            return $this->redirectToRoute('app_contact', array('id' => $id, 'slug' => $slug));
        }

        return $this->render('admin/contact_edit.html.twig', [
            'form' => $form->createView(),
            'flash' => $this,
            'user' => $user,
            'customer' => $customer
        ]);
    }

    #[Route('/admin/contact/{id}/{slug}/supprimer', name: 'app_contact_delete')]
    public function deleteContact(Contact $contact, Request $request,EntityManagerInterface $entityManager, PersistenceManagerRegistry $doctrine, $id): Response
    {
        $contact = $this->entityManager->getRepository(Contact::class)->findOneById($id);
        $user = $contact->getUser();
        $userId = $user->getId();
        $slug = $user->getSlug();
        $csrf_token = $request->query->get('csrf_token', '');

        
        if (!$this->isCsrfTokenValid('delete_contact' . $contact->getId(), $csrf_token)) {
            $this->addFlash(
                'error',
                'Vous ne pouvez pas supprimer cet élément.'
            );
        } else {
            // si le contact est lié à un client + redirect sur la page client 
            if($user->getCustomer()){
                $customer = $user->getCustomer();
                $customerId = $customer->getId();
                $customerSlug = $customer->getSlug();
                
                $entityManager = $doctrine->getManager();
                $entityManager->remove($contact);
                $entityManager->flush(); // push les données

                $this->addFlash(
                    'success',
                    'Le contact à été supprimé.'
                );
                return $this->redirectToRoute('app_customer', ['id' => $customerId, 'slug' => $customerSlug]);
                
                
            } else {
                
                // si le contact est lié à un utilisateur autre que client 

                $entityManager = $doctrine->getManager();
                $entityManager->remove($contact);
                $entityManager->flush(); // push les données
                
                $this->addFlash(
                    'success',
                    'Le contact à été supprimé.'
                );
                
                return $this->redirectToRoute('app_user_show', ['id' => $userId, 'slug' => $slug]);
            }
        }
    }
            
}
