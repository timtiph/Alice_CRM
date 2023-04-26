<?php

namespace App\Controller;

use App\Entity\Partner;
use App\Form\PartnerType;
use App\Repository\PartnerRepository;
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/partenaire')]
class PartnerController extends AbstractController
{
    #[Route('/', name: 'app_partner_list', methods: ['GET'])]
    public function partnerList(PartnerRepository $partnerRepository): Response
    {
        $partners = $partnerRepository->findAll();
        $customers = $partnerRepository->findCustomersByPartners($partners);
    
        return $this->render('partner/list.html.twig', [
            'partners' => $partners,
            'customers' => $customers
        ]);
    }
    
    #[Route('/ajouter', name: 'app_partner_add', methods: ['GET', 'POST'])]
    public function partnerAdd(Request $request, PartnerRepository $partnerRepository): Response
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

            return $this->redirectToRoute('app_partner_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partner/new.html.twig', [
            'partner' => $partner,
            'form' => $form,
            'flash' => $this
        ]);
    }

    #[Route('/{id}', name: 'app_partner_show', methods: ['GET'])]
    public function partnerShow(Partner $partner, CustomerRepository $customerRepository): Response
    {
        $customers = $customerRepository->findBy(['partner' => $partner]);
    
        return $this->render('partner/show.html.twig', [
            'partner' => $partner,
            'customers' => $customers
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_partner_edit', methods: ['GET', 'POST'])]
    public function partnerEdit(Request $request, Partner $partner, PartnerRepository $partnerRepository): Response
    {
        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $partnerRepository->save($partner, true);

            return $this->redirectToRoute('app_partner_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partner/edit.html.twig', [
            'partner' => $partner,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_partner_delete', methods: ['POST'])]
    public function partnerDelete(Request $request, Partner $partner, PartnerRepository $partnerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$partner->getId(), $request->request->get('_token'))) {
            $partnerRepository->remove($partner, true);
        }

        return $this->redirectToRoute('app_partner_list', [], Response::HTTP_SEE_OTHER);
    }
}
