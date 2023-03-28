<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Contact;
use App\Entity\Contract;
use App\Entity\Customer;
use App\Form\ContractType;
use App\Form\CustomerType;
use App\Form\EditCustomerType;
use App\Form\EditUserType;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


#[Route('/admin')]

class AdminMainController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('', name: 'app_admin_main')]
    public function index(): Response
    {
        return $this->render('admin_main/index.html.twig', [
            'controller_name' => 'AdminMainController',
        ]);
    }

    #[Route('/utilisateur', name: 'app_users_list')]
    public function showUsers(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $customers = $this->entityManager->getRepository(Customer::class)->findAll();
        
        return $this->render('admin_main/user_list.html.twig', [
            'users' => $users,
            'customer' => $customers
        ]);
    }

    #[Route('/utilisateur/modifier/{id}/{slug}', name: 'app_user_edit')]
    public function editUser(Request $request, $id, $slug, EntityManagerInterface $entityManager, PersistenceManagerRegistry $doctrine): Response
    {
        $user = $this->entityManager->getRepository(User::class)->findOneById($id);

        if (!$user) {
            $this->addFlash(
               'alert',
               'L\'utilisateur n\'éxiste pas'
            );
            return $this->redirectToRoute('app_users_list'); 
        }

        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            
            if($form->isValid()){

                $user = $form->getData();
                
                $entityManager = $doctrine->getManager();
                
                $entityManager->flush(); // push les données
                
                $this->addFlash(
                    'success',
                    'La modification du contact et bien enregistrée.'
                );
                return $this->redirectToRoute('app_users_list');

            } else {

                $this->addFlash(
                    'alert',
                    'Erreur sur le formulaire.'
                );

                return $this->redirectToRoute('app_users_list');

            }
        }
        
        return $this->render('admin_main/user_edit.html.twig', [
            'form' => $form->createView(), 
            'user' => $user,
        ]);
    }
    

    #[Route('/client', name: 'app_customer_list')]
    public function showCustomers(): Response
    {
        $customers = $this->entityManager->getRepository(Customer::class)->findAll();
        
        return $this->render('admin_main/customer_list.html.twig', [
            'customers' => $customers
        ]);
    }

    #[Route('/client/{id}/{slug}', name: 'app_customer')]
    public function showCustomer($id) 
    {

        // TODO : changer l'ordre de la logique des if
        // récup données client
        $customer = $this->entityManager->getRepository(Customer::class)->findOneById($id);

        // récup user associé
        $user = $customer->getUser($this);
        
        // récup contact associé au user
        $contacts = $this->entityManager->getRepository(Contact::class)->findBy(['user' => $user]);
        
        if(!$customer) { // si tu ne trouve pas de ID, redirect to app_customer_list (liste des clients)
            return $this->redirectToRoute('app_customer_list');
        }

        return $this->render('admin_main/customer_show.html.twig', [
            'customer' => $customer,
            'user' => $user,
            'contacts' => $contacts
        ]);

    }

    #[Route('/client/creer-un-client/{id}/{slug}', name: 'app_customer_add')]
    public function createCustomer(Request $request, EntityManagerInterface $entityManager, PersistenceManagerRegistry $doctrine, $id): Response
    {

        $user = $this->entityManager->getRepository(User::class)->findOneById($id);

        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        
        $form->handleRequest($request);        
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $customer = $form->getData();

                // création Slug
                $fullname = $customer->getName();
                $slugify = new Slugify();
                $slugify = $slugify->slugify($fullname);
                $customer->setSlug($slugify);

                // récup User Id
                $customer->setUser($user);
                
                $entityManager = $doctrine->getManager();
                $entityManager->persist($customer); //figer les données 
                $entityManager->flush(); // push les données

                $this->addFlash(
                    'success',
                    'La Création du client est bien enregistrée.'
                );
                return $this->redirectToRoute('app_users_list');
            }
        } 
        // else {
        //     $this->addFlash(
        //         'alert',
        //         'Une Erreur est survenue, veuillez recommencer.'
        //     );
            //return $this->redirectToRoute('app_customer_add');
        // }
        
        return $this->render('admin_main/customer_new.html.twig', [
            'form' => $form->createView(), 
            'flash' => $this,
            'customer' => $customer
        ]);
    }

    #[Route('/client/modifier-un-client/{id}/{slug}', name: 'app_customer_edit')]
    public function editCustomer(Request $request, EntityManagerInterface $entityManager, PersistenceManagerRegistry $doctrine, $id, $slug): Response
    {

        $customer = $this->entityManager->getRepository(Customer::class)->findOneById($id);

        if (!$customer) {
            $this->addFlash(
                'alert',
                'Vous ne pouvez pas modifier ce client.'
            );
            return $this->redirectToRoute('app_customer', array('id' => $id, 'slug' => $slug)); 
        }
    
        $form = $this->createForm(EditCustomerType::class, $customer);
        
        $form->handleRequest($request);        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->entityManager->flush();

            $this->addFlash(
                'success',
                'La modification du client est bien enregistrée.'
            );
            return $this->redirectToRoute('app_customer', array('id' => $id, 'slug' => $slug));
        } 
        // else {
        //     $this->addFlash(
        //         'alert',
        //         'Une Erreur est survenue, veuillez recommencer.'
        //     );
        //     return $this->redirectToRoute('app_customer', array('id' => $id, 'slug' => $slug));
        // }
        
        return $this->render('admin_main/customer_new.html.twig', [
            'form' => $form->createView(), 
            'flash' => $this,
            'customer' => $customer
        ]);
    }

}
