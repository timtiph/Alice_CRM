<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Contact;
use App\Entity\Customer;
use App\Form\CustomerType;
use App\Form\EditUserType;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/utilisateurs', name: 'app_users_list')]
    public function showUsers(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        
        return $this->render('admin_main/user_list.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/utilisateur/modifier/{id}', name: 'app_user_edit')]
    public function editUser(Request $request, $id, EntityManagerInterface $entityManager, PersistenceManagerRegistry $doctrine): Response
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
    

    #[Route('/clients', name: 'app_customer_list')]
    public function showCustomers(): Response
    {
        $customers = $this->entityManager->getRepository(Customer::class)->findAll();
        
        return $this->render('admin_main/customer_list.html.twig', [
            'customers' => $customers
        ]);
    }

    #[Route('/clients/creer-un-client', name: 'app_customer_add')]
    public function createCustomer(Request $request, EntityManagerInterface $entityManager, PersistenceManagerRegistry $doctrine): Response
    {

        $customer = new Customer();
        
        $form = $this->createForm(CustomerType::class, $customer);

        $form->handleRequest($request);        
        if ($form->isSubmitted() && $form->isValid()) {
            $customer = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($customer); //figer les données 
            $entityManager->flush(); // push les données

            $this->addFlash(
                'success',
                'La Création du client et bien enregistrée.'
            );
        } else {
            $this->addFlash(
                'alert',
                'Une Erreur est survenue, veuillez recommencer.'
            );
            //return $this->redirectToRoute('app_customer_add');
        }
        
        return $this->render('admin_main/customer_new.html.twig', [
            'form' => $form->createView(), 
            'flash' => $this 
        ]);
    }

    #[Route('/client/fiche{id}', name: 'app_customer')]
    public function showCustomer($id) 
    {

        // TODO : changer l'ordre de la logique des if
        // récup données client
        $customer = $this->entityManager->getRepository(Customer::class)->findOneById($id);

        // récup user associé
        $user = $customer->getUser($this);
        
        // récup contact associé au user
        $contacts = $this->entityManager->getRepository(Contact::class)->findBy(['user' => $user]);
        dump($contacts);

        if(!$customer) { // si tu ne trouve pas de ID, redirect to app_customer_list (liste des clients)
            return $this->redirectToRoute('app_customer_list');
        }

        return $this->render('admin_main/customer_show.html.twig', [
            'customer' => $customer,
            'user' => $user,
            'contacts' => $contacts
        ]);

    }


}
