<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Cocur\Slugify\Slugify;
use App\Form\EditContactType;
use App\Repository\ContactRepository;
use App\Repository\DocumentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/compte')]

class HomeController extends AbstractController
{

    // Entity Manager needed to edit and create contacts
    private $documentRepository;
    private $contactRepository;

    public function __construct(DocumentRepository $documentRepository, ContactRepository $contactRepository)
    {
        $this->documentRepository = $documentRepository;
        $this->contactRepository = $contactRepository;
    }
   
    #[Route('', name: 'app_home')]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();

        // verify if connected user isVerify
        if($user->getIsVerified()){

            if ($this->isGranted('ROLE_ADMIN')) {
                // If User is ADMIN = show all documents
                $query = $this->documentRepository->createQueryBuilder('d')->orderBy('d.date', 'DESC');
            } else {
                // else, show documents where user.id = documents.user.id
                $query = 
                    $this->documentRepository
                        ->createQueryBuilder('d')
                        ->join('d.user', 'u')
                        ->where('u.id = :userId')
                        ->setParameter('userId', $user->getId())
                        ->orderBy('d.date', 'DESC');
            }
            // pagination on documents
            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                10
            );

            if ($this->isGranted('ROLE_USER')) {
                // return the contacts of the connected user if not ADMIN
                $contacts = $user->getContacts();
            }
    
            

        } else {

            // else in anticipation of a connection although the conditions in the controllerFormLogin 
            $this->addFlash(
                'alert',
                'Votre compte n\'a pas été vérifié. Veuillez vérifier votre boite mail, ainsi que les spams.'
            );
            return $this->redirectToRoute('app_logout');
        }
        
        return $this->render('home/index.html.twig', [
            'pagination' => $pagination,
            'contacts' => $contacts,
            'flash' => $this,
            'user' => $user,
        ]);
    }

    #[Route('/contacts', name: 'app_contacts_user')]
    public function showUsercontacts()
    {
        $user = $this->getUser();

        // recover contacts of connected user
        $contacts = $user->getContacts();

        // return contacts on template
        return $this->render('home/contact_user_list.html.twig', [
            'contacts' => $contacts,
            'user' => $user,
        ]);
    }

    #[Route('/creer-un-contact', name: 'app_contact_user_add')]
    public function addUserContact(Request $request): Response
    {
        $user = $this->getUser();
        
        $contact = new Contact();
        
        $form = $this->createForm(ContactType::class, $contact, [
            'user' => $user,
        ]);
        $contact->setUser($user);

        $form->handleRequest($request);
        
        if ($form->isSubmitted()){
            
            if ($form->isValid()) {
                
                $contact = $form->getData();
                $fullname = $contact->getFirstname()." ".$contact->getLastname();
                $slugify = new Slugify();
                $slugify = $slugify->slugify($fullname);
                $contact->setSlug($slugify);
                
                $this->contactRepository->save($contact, true);
                
                $this->addFlash(
                    'success',
                    'La Création du contact et bien enregistrée.'
                );
                
                return $this->redirectToRoute('app_contacts_user');
           } 
        }
    
        return $this->render('home/contact_user_add.html.twig', array(
            'user' => $user,
            'flash' => $this,
            'form' => $form->createView(), 
        ));
    }

    #[Route('/contacts/{id}/{slug}/modifier-un-contact', name: 'app_contacts_user_edit')]
    public function editUserContact(Request $request, $id, ContactRepository $contactRepository): Response
    {
        $contact = $contactRepository->findOneById($id);

        if (!$contact) {
            $this->addFlash(
                'error',
                'Le contact n\'existe pas.'
            );
            return $this->redirectToRoute('app_home'); 
        }

        $form = $this->createForm(EditContactType::class, $contact);

        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();
            $this->contactRepository->save($contact, true);
            $this->addFlash(
                'success',
                'La modification du contact est bien enregistrée.'
            );
            return $this->redirectToRoute('app_contacts_user');
        }

        return $this->render('home/contact_user_edit.html.twig', [
            'form' => $form->createView(),
            'flash' => $this,
        ]);
    }
}
