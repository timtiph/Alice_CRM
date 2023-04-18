<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Contact;
use App\Entity\Contract;
use App\Entity\Customer;
use App\Form\NewUserType;
use App\Entity\TariffZone;
use App\Form\CustomerType;
use App\Form\EditUserType;
use Cocur\Slugify\Slugify;
use App\Entity\DynamicContent;
use App\Form\EditCustomerType;
use App\Form\DynamicContentType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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

    #[Route('/utilisateur/{id}/{slug}', name: 'app_user_show')]
    public function showUser(User $id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->findOneById($id);
        $contacts = $user->getContacts();
        $customer = $user->getCustomer();



        return $this->render('admin_main/user_show.html.twig', [
            'user' => $user,
            'contacts' => $contacts,
            'customer' => $customer
        ]);
    }

    #[Route('/nouvel-utilisateur', name: 'app_user_add')]
    public function addUser(Request $request,PersistenceManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(NewUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();  // injecte dans obj $user toutes les données récup dans $form

            // vérifier la présence du mail saisie pour éviter les doublons
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

            if(!$search_email) {
                $password = $passwordHasher->hashPassword($user, $user->getPassword()); // hash le password saisi
                $user->setPassword($password); // envoi le password dans obj User

                // on slug le nom du User
                $fullname = $user->getFirstname()." ".$user->getLastname();
                $slugify = new Slugify();
                $slugify = $slugify->slugify($fullname);
                $user->setSlug($slugify);


                //return entity manager
                $entityManager = $doctrine->getManager();
                $entityManager->persist($user); //figer les données
                $entityManager->flush(); // push les données

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
                return $this->redirectToRoute('app_users_list');

            }
        }
        return $this->render('admin_main/user_new.html.twig', [
            'form' => $form->createView(),
            'flash' => $this
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
                return $this->redirectToRoute('app_user_show', ['id' => $id, 'slug' => $slug]);

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
    public function showCustomer($id, Request $request): Response
    {

        // récup données client
        $customer = $this->entityManager->getRepository(Customer::class)->findOneById($id);

        // récup user associé
        $user = $customer->getUser();

        // récup contact associé au user
        $contacts = $this->entityManager->getRepository(Contact::class)->findBy(['user' => $user]);
        // récup contrats associés au customer
        $contracts = $this->entityManager->getRepository(Contract::class)->findBy(['customer' => $customer]);

        if(!$customer) { // si tu ne trouve pas de ID, redirect to app_customer_list (liste des clients)
            return $this->redirectToRoute('app_customer_list');
        }

        // Création d'un nouvel objet DynamicContent pour stocker le contenu dynamique
        $dynamicContent = new DynamicContent();

        // Création d'un formulaire pour créer ou modifier le contenu dynamique
        $form = $this->createForm(DynamicContentType::class, $dynamicContent);

        // Traitement des données soumises par le formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrement du contenu dynamique en base de données
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
    public function createCustomer(Request $request, EntityManagerInterface $entityManager, PersistenceManagerRegistry $doctrine, $id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->findOneById($id);
        $tariffZone = $this->entityManager->getRepository(TariffZone::class)->findAll();
        
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
            $form->getErrors(true);
            if ($form->isValid()) {
                $customer = $form->getData();
               
                if($customer->getSiret()){
                    // on enlève les espaces entre les groupes de chiffre
                    $siret = str_replace(' ', '', $form->get('siret')->getData());
                    //on set le siret sans espace
                    $customer->setSiret($siret);
                    //dd($siret);
                }
                
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
                $customerId = $customer->getId();
                return $this->redirectToRoute('app_customer', [ 'id' => $customerId, 'slug' => $slugify ]);
            } else {
                $this->addFlash(
                    'alert',
                    'Une Erreur est survenue, veuillez recommencer.'
                );
               return $this->redirectToRoute('app_customer_add', [ 'id' => $id, 'slug' => $user->getSlug() ]);
            }
        } 

        return $this->render('admin_main/customer_new.html.twig', [
            'form' => $form->createView(),
            'flash' => $this,
            'customer' => $customer,
            'user' => $user
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
            $siret = str_replace(' ', '', $form->get('siret')->getData());
            $customer->setSiret($siret);
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

        //On va chercher par nom (qui sert de clé) le dynamic content correspondant
        $dynamicContentRepo = $doctrine->getRepository(DynamicContent::class);

        $currentDynamicContent = $dynamicContentRepo->findOneByName($name);

        $em = $doctrine->getManager();

        // Si le contenu est vide, on en crée un avec le nom passé dans la fonction twig
        if (empty($currentDynamicContent)) {
            $currentDynamicContent = new DynamicContent();
            $currentDynamicContent->setName($name);
            $em->persist($currentDynamicContent);
        }

        // Sinon, on modifie le contenu existant par le nouveau contenu
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
