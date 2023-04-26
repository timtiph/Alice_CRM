<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Document;
use App\Form\DocumentType;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


#[Route('/document')]
class DocumentController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/', name: 'app_document_list', methods: ['GET'])]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();
        
        if ($this->isGranted('ROLE_ADMIN')) {
            // Si l'utilisateur est un administrateur, afficher tous les documents
            $query = $this->entityManager->getRepository(Document::class)
                ->createQueryBuilder('d')
                ->leftJoin('d.user', 'u');

        } else {
            // Sinon, afficher seulement les documents de l'utilisateur connecté
            $query = $this->entityManager
                ->getRepository(Document::class)
                ->createQueryBuilder('d')
                ->join('d.user', 'u')
                ->where('u.id = :user_id')
                ->setParameter('user_id', $user->getId())
                ->orderBy('d.date', 'DESC');
        }
    
        $pagination = $paginator->paginate(
            $query, /* query builder contenant les données à paginer */
            $request->query->getInt('page', 1), /* numéro de la page par défaut */
            10, /* nombre d'éléments par page */
            ['defaultSortFieldName' => 'd.date', 'defaultSortDirection' => 'desc']
        );
        
        return $this->render('document/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/nouveau', name: 'app_document_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DocumentRepository $documentRepository, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $document = new Document();
        
        
        if ($this->isGranted('ROLE_ADMIN')) {
            $adminUser = [$this->getUser()];
        } else {
            $adminUser = $entityManager->createQueryBuilder()
                ->select('u')
                ->from(User::class, 'u')
                ->where('u.roles LIKE :role')
                ->setParameter('role', '%ROLE_ADMIN%')
                ->getQuery()
                ->getResult();
        }
        
        $users = array_merge([$this->getUser()], $adminUser);
        
        foreach ($users as $user) {
            $document->addUser($user);
        }

        $document->setDate(new \DateTime());
        $form = $this->createForm(DocumentType::class, $document);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $file = $form->get('fileName')->getData();
            
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $safeFilename = substr($safeFilename, 0, 20); // extrait les 20 premiers caractères
                $currentDate = date('Ymd');
                $newFilename = $currentDate.'-'.$safeFilename.'-'.uniqid().'.'.$file->guessExtension(); // devine l'xtention et l'ajoute
                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('documents_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                
                // updates the 'file' property to store the PDF file name
                $document->setFileName($newFilename);
                
            }

            $documentRepository->save($document, true);

            return $this->redirectToRoute('app_document_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('document/new.html.twig', [
            'document' => $document,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_document_show', methods: ['GET'])]
    public function show(Document $document, AuthorizationCheckerInterface $authChecker): Response
    {
        // Creation of an isAuthorized status to filter users who are authorized to view the document
        
        $isAuthorized = $authChecker->isGranted('ROLE_ADMIN');
        $authorizedUsers = $document->getUser()->toArray();
        $user = $this->getUser();
    
        if (!$isAuthorized && !in_array($user, $authorizedUsers)) {
            throw $this->createAccessDeniedException();
        }
    
        return $this->render('document/show.html.twig', [
            'document' => $document,
            'isAuthorized' => $isAuthorized,
            'authorizedUsers' => $authorizedUsers,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_document_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Document $document, DocumentRepository $documentRepository, AuthorizationCheckerInterface $authChecker): Response
    {
        // Seul ADMIN is authorized to edit document
        
        $isAuthorized = $authChecker->isGranted('ROLE_ADMIN');

        if ($this->isGranted('ROLE_ADMIN')) {

            $form = $this->createForm(DocumentType::class, $document);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                        
                $documentRepository->save($document, true);
                return $this->redirectToRoute('app_document_show', ['id' => $document->getId()], Response::HTTP_SEE_OTHER);
            }

        }
        

        return $this->render('document/edit.html.twig', [
            'document' => $document,
            // si $isAuthorized == true, on renvoie $form->createView() avec valeur 'form'. Else $isAuthorized == false, en renvoie null et pas de form créé
            'form' => $isAuthorized ? $form->createView() : null,
            'isAuthorized' => $isAuthorized,
        ]);
    }

    #[Route('/{id}', name: 'app_document_delete', methods: ['POST'])]
    public function delete(Request $request, Document $document, DocumentRepository $documentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$document->getId(), $request->request->get('_token'))) {
            $documentRepository->remove($document, true);
        }

        return $this->redirectToRoute('app_document_list', [], Response::HTTP_SEE_OTHER);
    }
}
