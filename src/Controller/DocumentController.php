<?php

namespace App\Controller;

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


#[Route('/document')]
class DocumentController extends AbstractController
{
    #[Route('/', name: 'app_document_list', methods: ['GET'])]
    public function index(DocumentRepository $documentRepository, PaginatorInterface $paginator, Request $request, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();
    
        if ($this->isGranted('ROLE_ADMIN')) {
            $query = $entityManager->getRepository(Document::class)->createQueryBuilder('d')->join('d.user', 'u');
        } else {
            $query = $entityManager->getRepository(Document::class)->createQueryBuilder('d')->join('d.user', 'u')->where('u.id = :user_id')->setParameter('user_id', $user->getId());
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
    public function new(Request $request, DocumentRepository $documentRepository, SluggerInterface $slugger): Response
    {
        $document = new Document();
        
        $document->setDate(new \DateTime());
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $file = $form->get('fileName')->getData();
            
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

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
                // instead of its contents
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
    public function show(Document $document): Response
    {


        return $this->render('document/show.html.twig', [
            'document' => $document,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_document_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Document $document, DocumentRepository $documentRepository): Response
    {
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $documentRepository->save($document, true);

            return $this->redirectToRoute('app_document_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('document/edit.html.twig', [
            'document' => $document,
            'form' => $form,
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
