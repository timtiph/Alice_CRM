<?php

namespace App\Controller;

use App\Class\Search;
use App\Entity\Customer;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
        //injection de dependance : tu prends l'entityManager pour entrer dans ce controller
    // Appel entityManager pour récup tous les produits
    private $entityManager;

    // intialise un constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/compte', name: 'app_home')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $customers = $this->entityManager->getRepository(Customer::class)->findAll();
        $search = new Search;
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if ($form->isSubmitted()&&$form->isValid()){
            $customers = $this->entityManager->getRepository(Customer::class)->findWithSearch($search);
        }

        return $this->render('home/index.html.twig', [
            'customers' => $customers,
            'form' => $form
        ]);
    }
}
