<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Cocur\Slugify\Slugify;
use App\Form\EditContactType;
use App\Repository\UserRepository;
use App\Repository\ContactRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
   
    #[Route('/compte', name: 'app_home')]
    public function index(DocumentRepository $documentRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();

        if($user->getIsVerified()){

            if ($this->isGranted('ROLE_ADMIN')) {
                // Si l'utilisateur est un administrateur, afficher tous les documents
                $query = $documentRepository->createQueryBuilder('d')->orderBy('d.date', 'DESC');
            } else {
                // Sinon, afficher seulement les documents de l'utilisateur connecté
                $query = 
                    $documentRepository
                        ->createQueryBuilder('d')
                        ->join('d.user', 'u')
                        ->where('u.id = :userId')
                        ->setParameter('userId', $user->getId())
                        ->orderBy('d.date', 'DESC');
            }

            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                10
            );

            if ($this->isGranted('ROLE_USER')) {
                // Récupérer les contacts de l'utilisateur connecté
                $contacts = $user->getContacts();
            }
    
            

        } else {
            $this->addFlash(
                'alert',
                'Votre compte n\'a pas été vérifié. Veuillez vérifier votre boite mail, ainsi que les spams.'
            );
            return $this->redirectToRoute('app_logout', ['delay' => 3000]);
        }
        
        return $this->render('home/index.html.twig', [
            'pagination' => $pagination,
            'contacts' => $contacts,
            'flash' => $this,
        ]);
    }

    #[Route('/compte/contacts', name: 'app_contacts_user')]
    public function showUsercontacts()
    {
        $user = $this->getUser();

        // Vérifier si l'utilisateur est un administrateur
        if ($this->isGranted('ROLE_ADMIN')) {
            // Si l'utilisateur est un administrateur, renvoyer une réponse vide
            return $this->render('contacts/empty.html.twig');
        }

        // Récupérer les contacts de l'utilisateur connecté
        $contacts = $user->getContacts();

        // Afficher les contacts
        return $this->render('home/contact_user_list.html.twig', [
            'contacts' => $contacts,
            'user' => $user,
        ]);
    }

    #[Route('/contact/creer-un-contact/{id}/{slug}', name: 'app_contact_user_add')]
    public function addUserContact(Request $request, EntityManagerInterface $em): Response
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
                
                $em->persist($contact);
                $em->flush();
                
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

    #[Route('/contacts/modifier-un-contact/{id}/{slug}', name: 'app_contacts_user_edit')]
    public function editUserContact(Request $request, $id, $slug, ContactRepository $contactRepository, EntityManagerInterface $em): Response
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
            $em->flush();
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
