<?php

namespace App\Controller;

use App\Entity\SerpResult;
use App\Form\SerpResultType;
use App\Repository\SerpResultRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/serp/result')]
class SerpResultController extends AbstractController
{
    #[Route('/', name: 'app_serp_result_index', methods: ['GET'])]
    public function index(SerpResultRepository $serpResultRepository): Response
    {
        return $this->render('serp_result/index.html.twig', [
            'serp_results' => $serpResultRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_serp_result_new', methods: ['POST'])]
    public function save(Request $request, SerpResultRepository $serpResultRepository): Response
    {
        
        $serpResult = new SerpResult();
        $form = $this->createForm(SerpResultType::class, $serpResult);
        $form->handleRequest($request);
    
        // Add code to handle the JSON data sent by the JavaScript function
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['keyword']) && isset($data['rank'])) {
            $serpResult->setSerpInfo($data['keyword']);
            $serpResult->setGoogleRank($data['rank']);
            $serpResultRepository->save($serpResult, true);
        }
    
        return $this->render('serp_result/new.html.twig', [
            'serp_result' => $serpResult,
            'form' => $form,
        ]);
    }
    

    #[Route('/{id}', name: 'app_serp_result_show', methods: ['GET'])]
    public function show(SerpResult $serpResult): Response
    {
        return $this->render('serp_result/show.html.twig', [
            'serp_result' => $serpResult,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_serp_result_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SerpResult $serpResult, SerpResultRepository $serpResultRepository): Response
    {
        $form = $this->createForm(SerpResultType::class, $serpResult);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $serpResultRepository->save($serpResult, true);

            return $this->redirectToRoute('app_serp_result_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('serp_result/edit.html.twig', [
            'serp_result' => $serpResult,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_serp_result_delete', methods: ['POST'])]
    public function delete(Request $request, SerpResult $serpResult, SerpResultRepository $serpResultRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$serpResult->getId(), $request->request->get('_token'))) {
            $serpResultRepository->remove($serpResult, true);
        }

        return $this->redirectToRoute('app_serp_result_index', [], Response::HTTP_SEE_OTHER);
    }
}
