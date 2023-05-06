<?php

namespace App\Controller;

use App\Entity\TariffZone;
use App\Form\TariffZoneType;
use App\Repository\TariffZoneRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/tariff_zone')]
class TariffZoneController extends AbstractController
{
    private $tariffZoneRepository;

    public function __construct(TariffZoneRepository $tariffZoneRepository)
    {
        $this->tariffZoneRepository = $tariffZoneRepository;
    }
    
    #[Route('/', name: 'app_tariff_zone_list', methods: ['GET'])]
    public function list(): Response
    {
        return $this->render('tariff_zone/list.html.twig', [
            'tariff_zones' => $this->tariffZoneRepository->findAll(),
        ]);
    }

    #[Route('/ajouter', name: 'app_tariff_zone_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $tariffZone = new TariffZone();
        $form = $this->createForm(TariffZoneType::class, $tariffZone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tariffZoneRepository->save($tariffZone, true);
            $this->addFlash(
                'success',
                'La création de la nouvelle zone tarifaire est bien enregistrée.'
            );
            return $this->redirectToRoute('app_tariff_zone_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tariff_zone/new.html.twig', [
            'tariff_zone' => $tariffZone,
            'form' => $form,
            'flash' => $this
        ]);
    }

    #[Route('/{id}', name: 'app_tariff_zone_show', methods: ['GET'])]
    public function show(TariffZone $tariffZone): Response
    {
        $customers = $tariffZone->getCustomers();

        return $this->render('tariff_zone/show.html.twig', [
            'customers' => $customers,
            'tariff_zone' => $tariffZone,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_tariff_zone_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TariffZone $tariffZone): Response
    {
        $form = $this->createForm(TariffZoneType::class, $tariffZone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tariffZoneRepository->save($tariffZone, true);
            $this->addFlash(
                'success',
                'La modification de la zone tarifaire à été enregistrée.'
            );

            return $this->redirectToRoute('app_tariff_zone_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tariff_zone/edit.html.twig', [
            'tariff_zone' => $tariffZone,
            'flash' => $this,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tariff_zone_delete', methods: ['POST'])]
    public function delete(Request $request, TariffZone $tariffZone): Response
    {
        if ($this->isCsrfTokenValid('delete_tariffZone'.$tariffZone->getId(), $request->request->get('_token'))) {
            $this->tariffZoneRepository->remove($tariffZone, true);
            
            $this->addFlash(
                'success',
                'La zone tarifaire à bien été supprimée.'
            );
        } else {
            $this->addFlash(
                'alert',
                'Une erreur est survenue.',
            );
            return $this->redirectToRoute('app_tariff_zone_list', [], Response::HTTP_SEE_OTHER);
        }


        return $this->redirectToRoute('app_tariff_zone_list', [], Response::HTTP_SEE_OTHER);
    }
}
