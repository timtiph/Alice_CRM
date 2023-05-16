<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Customer;
use App\Form\NewUserType;
use App\Form\CustomerType;
use App\Form\EditUserType;
use Cocur\Slugify\Slugify;
use App\Entity\DynamicContent;
use App\Form\EditCustomerType;
use App\Form\DynamicContentType;
use App\Repository\UserRepository;
use App\Repository\ContactRepository;
use App\Repository\ContractRepository;
use App\Repository\CustomerRepository;
use App\Repository\TariffZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/admin')]

class AdminMainController extends AbstractController
{
    private $userRepository;
    private $customerRepository;
    private $contactRepository;
    private $contractRepository;
    private $tariffZoneRepository;
    private $entityManager;


    public function __construct(UserRepository $userRepository, CustomerRepository $customerRepository, ContactRepository $contactRepository, ContractRepository $contractRepository, TariffZoneRepository $tariffZoneRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
        $this->contactRepository = $contactRepository;
        $this->contractRepository = $contractRepository;
        $this->tariffZoneRepository = $tariffZoneRepository;
        $this->entityManager = $entityManager;
    }


    #[Route('/utilisateur', name: 'app_user_list')]
    public function showUserList(): Response
    {
        $users = $this->userRepository->findAll();
        $customers = $this->customerRepository->findAll();

        return $this->render('admin_main/user_list.html.twig', [
            'users' => $users,
            'customer' => $customers
        ]);
    }

    #[Route('/utilisateur/{id}/{slug}', name: 'app_user_show')]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id', 'slug' => 'slug']])]
    public function showUser(User $id, string $slug): Response
    {
        $user = $this->userRepository->findOneById($id);

        // Check that the slug in the URL matches the one stored in the database
        if ($user->getSlug() !== $slug) {
            // If the slugs don't match, redirect the user to the users list + Message
            $this->addFlash(
                'alert',
                'Vous ne pouvez pas faire ça !'
            );
            return $this->redirectToRoute('app_user_list');
        }

        $contacts = $user->getContacts();
        $customer = $user->getCustomer();



        return $this->render('admin_main/user_show.html.twig', [
            'user' => $user,
            'contacts' => $contacts,
            'customer' => $customer,
            'flash' => $this,
        ]);
    }

    #[Route('/nouvel-utilisateur', name: 'app_user_add')]
    public function addUser(Request $request,PersistenceManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(NewUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();  // injects in obj $user all data collected in $form

            // check if email already present in the database to avoid duplicates
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

            if(!$search_email) {
                $password = $passwordHasher->hashPassword($user, $user->getPassword()); // hash le password
                $user->setPassword($password); // set password Hash in User object

                // slug user name
                $fullname = $user->getFirstname()." ".$user->getLastname();
                $slugify = new Slugify();
                $slugify = $slugify->slugify($fullname);
                $user->setSlug($slugify);

                // Use Entity Manager to get the user ID for the redirectToRoute parameters
                $entityManager = $doctrine->getManager();
                $entityManager->persist($user); //persist data
                $entityManager->flush(); // push

                $this->addFlash(
                    'success',
                    'Le nouvel utilisateur est enregistré.'
                );
                $id = $user->getId();
                $slug = $slugify;
                return $this->redirectToRoute('app_user_show', ['id' => $id, 'slug' => $slug]);

            } else {

                $this->addFlash(
                    'alert',
                    'L\'email que vous avez renseigné existe déjà !!'
                );
                return $this->redirectToRoute('app_user_list');

            }
        }
        return $this->render('admin_main/user_new.html.twig', [
            'form' => $form->createView(),
            'flash' => $this
        ]);
    }

    #[Route('/utilisateur/{id}/{slug}/modifier', name: 'app_user_edit')]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id', 'slug' => 'slug']])]
    public function editUser(Request $request, $id, $slug, PersistenceManagerRegistry $doctrine, User $user): Response
    {
        if (!$user) {
            $this->addFlash(
               'alert',
               'L\'utilisateur n\'éxiste pas'
            );
            return $this->redirectToRoute('app_user_list');
        }
        

        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if($form->isValid()){
                // use EntityManger because method save on repository is not working
                $user = $form->getData();

                $this->entityManager = $doctrine->getManager();

                $this->entityManager->flush(); // push les données

                $this->addFlash(
                    'success',
                    'La modification du contact et bien enregistrée.'
                );
                return $this->redirectToRoute('app_user_show', ['id' => $id, 'slug' => $slug]);

            } else {

                $this->addFlash(
                    'alert',
                    'Erreur sur le formulaire.'
                );

                return $this->redirectToRoute('app_user_list');

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
        $customers = $this->customerRepository->findAll();

        return $this->render('admin_main/customer_list.html.twig', [
            'customers' => $customers
        ]);
    }

    #[Route('/client/{id}/{slug}', name: 'app_customer')]
    #[ParamConverter('customer', options: ['mapping' => ['id' => 'id', 'slug' => 'slug']])]
    public function showCustomer(Customer $id, String $slug, Request $request): Response
    {
        // recover customer object
        $customer = $this->customerRepository->findOneById($id);

        if(!$customer) { // redirect to app_customer_list (customer list) if dont find customer Id
            return $this->redirectToRoute('app_customer_list', [], 301);
        }
        
        // check that slug in URL match to $customer->getSlug()
        if ($customer->getSlug() !== $slug) {
            // if different = redirect to list of customer
            $this->addFlash(
                'alert',
                'Vous ne pouvez pas faire ça !'
            );
            return $this->redirectToRoute('app_customer_list', [], 301); 
            // use a permanent redirection (code 301) so that search engines update their indexes
        }

        // recover user from customer
        $user = $customer->getUser();

        // recover contact from user
        $contacts = $this->contactRepository->findBy(['user' => $user]);

        // recover contracts from customer
        $contracts = $this->contractRepository->findBy(['customer' => $customer]);

        // Create a new object DynamicContent to store the dynamicContent
        $dynamicContent = new DynamicContent();

        // Creating a form to create or modify dynamic content
        $form = $this->createForm(DynamicContentType::class, $dynamicContent);

        // Treatment of the data submitted by the form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Saving dynamic content in a database
            $this->entityManager->persist($dynamicContent);
            $this->entityManager->flush();
        }

        return $this->render('admin_main/customer_show.html.twig', [
            'customer' => $customer,
            'user' => $user,
            'contacts' => $contacts,
            'contracts' => $contracts,
            'dynamicContent' => $dynamicContent,
            'form' => $form->createView(),
        ]);

    }

    #[Route('/client/creer-un-client/{id}/{slug}', name: 'app_customer_add')]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    public function createCustomer(Request $request, EntityManagerInterface $entityManager, PersistenceManagerRegistry $doctrine, User $user): Response
    {
        $user = $this->userRepository->findOneById($user);
        $tariffZone = $this->tariffZoneRepository->findAll();
        
        if (!$tariffZone) {
            $this->addFlash(
                'notice',
                'Vous n\'avez pas encore définit de zone tarifaire. Merci de renseigner préalablement cet élément. Vous pourrez retourner sur le formulaire de création client par la suite.',
            );
            return $this->redirectToRoute('app_tariff_zone_new');
        }
        
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer, [
            'user' => $user
        ]);
        $customer->setUser($user);
        
        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $customer = $form->getData();
               
                if($customer->getSiret()){
                    // remove spaces
                    $siret = str_replace(' ', '', $form->get('siret')->getData());
                    // set Siret without spaces
                    $customer->setSiret($siret);
                }
                
                // create Slug
                $fullname = $customer->getName();
                $slugify = new Slugify();
                $slugify = $slugify->slugify($fullname);
                $customer->setSlug($slugify);

                // pass User object in customer object 
                $customer->setUser($user);

                $entityManager = $doctrine->getManager();
                $entityManager->persist($customer); //persist data
                $entityManager->flush(); // and push 

                $this->addFlash(
                    'success',
                    'La création du client est bien enregistrée.'
                );
                $customerId = $customer->getId();
                return $this->redirectToRoute('app_customer', [ 'id' => $customerId, 'slug' => $slugify ]);
            } 
        }

        return $this->render('admin_main/customer_new.html.twig', [
            'form' => $form->createView(),
            'flash' => $this,
            'customer' => $customer,
            'user' => $user
        ]);
    }

    

    #[Route('/client/{id}/{slug}/modifier-un-client', name: 'app_customer_edit')]
    #[ParamConverter('customer', options: ['mapping' => ['id' => 'id']])]
    #[ParamConverter('customer', options: ['mapping' => ['slug' => 'slug']])]
    public function editCustomer(Request $request, $id, $slug): Response
    {

        $customer = $this->customerRepository->findOneById($id);

        if(!$customer) { // if customer do not exist => redirect to list
            return $this->redirectToRoute('app_customer_list', [], 301);
        }
        
        // check that slug in URL match to $customer->getSlug()
        if ($customer->getSlug() !== $slug) {
            // if different => redirect to list of customer
            $this->addFlash(
                'alert',
                'Vous ne pouvez pas faire ça !'
            );
            
            return $this->redirectToRoute('app_customer', ['id' => $id, 'slug' => $slug], 301); // use a permanent redirection (code 301) so that search engines update their indexes
        }
        
        $form = $this->createForm(EditCustomerType::class, $customer);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($customer->getSiret()){
                // remove the spaces between the groups of numbers
                $siret = str_replace(' ', '', $form->get('siret')->getData());
                //set the siret without space
                $customer->setSiret($siret);
            }
            $this->entityManager->flush();

            $this->addFlash(
                'success',
                'La modification du client est bien enregistrée.'
            );
            return $this->redirectToRoute('app_customer', array('id' => $id, 'slug' => $slug));
        }

        return $this->render('admin_main/customer_edit.html.twig', [
            'form' => $form->createView(),
            'flash' => $this,
            'customer' => $customer
        ]);
    }
    
    #[Route('/contenu-dynamique/modifier/{id}/{slug}/{name}/', name: 'dynamic_content_edit', requirements: ["name" => "[a-z0-9_-]{2,50}"])]
    #[ParamConverter('customer', options: ['mapping' => ['slug' => 'slug']])]
    #[ParamConverter('customer', options: ['mapping' => ['id' => 'id']])]
    public function dynamicContentEdit($name, PersistenceManagerRegistry $doctrine, Request $request, Customer $customer): Response
    {
        // We will search by name (which serves as a key) the corresponding dynamic content
        $dynamicContentRepo = $doctrine->getRepository(DynamicContent::class);

        $currentDynamicContent = $dynamicContentRepo->findOneByName($name);

        $em = $doctrine->getManager();

        // If the content is empty, we create one with the name passed in the twig function
        if (empty($currentDynamicContent)) {
            $currentDynamicContent = new DynamicContent();
            $currentDynamicContent->setName($name);
            $em->persist($currentDynamicContent);
        }

        // Otherwise, the existing content is modified by the new one
        $form = $this->createForm(DynamicContentType::class, $currentDynamicContent);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            $this->addFlash('success', 'Le contenu a bien été modifié !');

            return $this->redirectToRoute('app_customer', [
                'id' => $customer->getId(),
                'slug' => $customer->getSlug()
            ]);

        }

        return $this->render('dynamic_content/edit.html.twig', ['form' => $form->createView(),]);
    }


}
