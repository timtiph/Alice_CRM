<?php

namespace App\Controller;

use App\Entity\DynamicContent;
use App\Form\DynamicContentType;
use App\Repository\DynamicContentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dynamic/content')]
class DynamicContentController extends AbstractController
{
    #[Route('/', name: 'app_dynamic_content_index', methods: ['GET'])]
    public function index(DynamicContentRepository $dynamicContentRepository): Response
    {
        return $this->render('dynamic_content/index.html.twig', [
            'dynamic_contents' => $dynamicContentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_dynamic_content_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DynamicContentRepository $dynamicContentRepository): Response
    {
        $dynamicContent = new DynamicContent();
        $form = $this->createForm(DynamicContentType::class, $dynamicContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dynamicContentRepository->save($dynamicContent, true);

            return $this->redirectToRoute('app_dynamic_content_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dynamic_content/new.html.twig', [
            'dynamic_content' => $dynamicContent,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dynamic_content_show', methods: ['GET'])]
    public function show(DynamicContent $dynamicContent): Response
    {
        return $this->render('dynamic_content/show.html.twig', [
            'dynamic_content' => $dynamicContent,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dynamic_content_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DynamicContent $dynamicContent, DynamicContentRepository $dynamicContentRepository): Response
    {
        $form = $this->createForm(DynamicContentType::class, $dynamicContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dynamicContentRepository->save($dynamicContent, true);

            return $this->redirectToRoute('app_dynamic_content_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dynamic_content/edit.html.twig', [
            'dynamic_content' => $dynamicContent,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dynamic_content_delete', methods: ['POST'])]
    public function delete(Request $request, DynamicContent $dynamicContent, DynamicContentRepository $dynamicContentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dynamicContent->getId(), $request->request->get('_token'))) {
            $dynamicContentRepository->remove($dynamicContent, true);
        }

        return $this->redirectToRoute('app_dynamic_content_index', [], Response::HTTP_SEE_OTHER);
    }
}
