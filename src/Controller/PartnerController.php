<?php

namespace App\Controller;

use App\Entity\Partner;
use App\Entity\Customer;
use App\Form\PartnerType;
use App\Repository\PartnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/partenaire')]
class PartnerController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/', name: 'app_partner_index', methods: ['GET'])]
    public function index(PartnerRepository $partnerRepository, EntityManagerInterface $entityManager): Response
    {
        $partners = $entityManager->getRepository(Partner::class)->findAll();
        $customers = $entityManager->getRepository(Customer::class)->findBy(['partner' => $partners]);
        

        return $this->render('partner/list.html.twig', [
            'partners' => $partnerRepository->findAll(),
            'customers' => $customers
        ]);
    }

    #[Route('/ajouter', name: 'app_partner_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PartnerRepository $partnerRepository): Response
    {
        $partner = new Partner();
        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $partnerRepository->save($partner, true);

            $this->addFlash(
                'success',
                'La création du nouveau partenariat est bien enregistrée.'
            );

            return $this->redirectToRoute('app_partner_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partner/new.html.twig', [
            'partner' => $partner,
            'form' => $form,
            'flash' => $this
        ]);
    }

    #[Route('/{id}', name: 'app_partner_show', methods: ['GET'])]
    public function show(Partner $partner, EntityManagerInterface $entityManager): Response
    {
        $partnerId = $partner->getId();
        $customers = $partner->getCustomers();
        $customers = $entityManager->getRepository(Customer::class)->findBy(['partner' => $partnerId]);

        return $this->render('partner/show.html.twig', [
            'partner' => $partner,
            'customers' => $customers
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_partner_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Partner $partner, PartnerRepository $partnerRepository): Response
    {
        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $partnerRepository->save($partner, true);

            return $this->redirectToRoute('app_partner_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partner/edit.html.twig', [
            'partner' => $partner,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_partner_delete', methods: ['POST'])]
    public function delete(Request $request, Partner $partner, PartnerRepository $partnerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$partner->getId(), $request->request->get('_token'))) {
            $partnerRepository->remove($partner, true);
        }

        return $this->redirectToRoute('app_partner_index', [], Response::HTTP_SEE_OTHER);
    }
}
