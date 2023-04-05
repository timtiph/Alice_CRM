<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\TariffZone;
use App\Form\TariffZoneType;
use App\Repository\CustomerRepository;
use App\Repository\TariffZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/tariff_zone')]
class TariffZoneController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/', name: 'app_tariff_zone_index', methods: ['GET'])]
    public function index(TariffZoneRepository $tariffZoneRepository): Response
    {
        return $this->render('tariff_zone/list.html.twig', [
            'tariff_zones' => $tariffZoneRepository->findAll(),
        ]);
    }

    #[Route('/ajouter', name: 'app_tariff_zone_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TariffZoneRepository $tariffZoneRepository): Response
    {
        $tariffZone = new TariffZone();
        $form = $this->createForm(TariffZoneType::class, $tariffZone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tariffZoneRepository->save($tariffZone, true);
            $this->addFlash(
                'success',
                'La Création de la nouvelle zone tarifaire est bien enregistrée.'
            );
            return $this->redirectToRoute('app_tariff_zone_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tariff_zone/new.html.twig', [
            'tariff_zone' => $tariffZone,
            'form' => $form,
            'flash' => $this
        ]);
    }

    #[Route('/{id}', name: 'app_tariff_zone_show', methods: ['GET'])]
    public function show(TariffZone $tariffZone, EntityManagerInterface $entityManager): Response
    {
        $tariffZoneId = $tariffZone->getId();
        $customers = $tariffZone->getCustomers();
        $customers = $entityManager->getRepository(Customer::class)->findBy(['tariffZone' => $tariffZoneId]);
        

        return $this->render('tariff_zone/show.html.twig', [
            'customers' => $customers,
            'tariff_zone' => $tariffZone,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_tariff_zone_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TariffZone $tariffZone, TariffZoneRepository $tariffZoneRepository): Response
    {
        $form = $this->createForm(TariffZoneType::class, $tariffZone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tariffZoneRepository->save($tariffZone, true);
            $this->addFlash(
                'success',
                'La modification de la zone tarifaire à été enregistrée.'
            );

            return $this->redirectToRoute('app_tariff_zone_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tariff_zone/edit.html.twig', [
            'tariff_zone' => $tariffZone,
            'flash' => $this,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tariff_zone_delete', methods: ['POST'])]
    public function delete(Request $request, TariffZone $tariffZone, TariffZoneRepository $tariffZoneRepository): Response
    {
        if ($this->isCsrfTokenValid('delete_tariffZone'.$tariffZone->getId(), $request->request->get('_token'))) {
            $tariffZoneRepository->remove($tariffZone, true);
        }

        $this->addFlash(
            'success',
            'La zone tarifaire à bien été supprimée.'
        );

        return $this->redirectToRoute('app_tariff_zone_index', [], Response::HTTP_SEE_OTHER);
    }
}
