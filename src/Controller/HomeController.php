<?php

namespace App\Controller;

use App\Repository\DocumentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
   
    #[Route('/compte', name: 'app_home')]
    public function index(DocumentRepository $documentRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();
        $query = $documentRepository->createQueryBuilder('d')->join('d.user', 'u')->where('u.id = :userId')->setParameter('userId', $user->getId())->orderBy('d.date', 'DESC');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
            
        return $this->render('home/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

}
