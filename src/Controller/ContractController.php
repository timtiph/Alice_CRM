<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Form\ContractType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

class ContractController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/contrat', name: 'app_contract_list')]
    public function showContracts(): Response
    {
        $contracts = $this->entityManager->getRepository(Contract::class)->findAll();

        return $this->render('admin_main/contract_list.html.twig', [
            'contracts' => $contracts
        ]);
    }

    // #[Route('/admin/contrat/{id}', name: 'app_contract')]
    // public function showContract($id): Response
    // {
    //     $contract = $this->entityManager->getRepository(Contract::class)->findOneById($id);

    //     return $this->render('admin_main/contract_show.html.twig', [
    //         'contract' => $contract
    //     ]);

    // }

    #[Route('/admin/contrat/creer-un-contrat', name: 'app_contract_add')]
    public function createContract(Request $request, EntityManagerInterface $entityManager, PersistenceManagerRegistry $doctrine): Response
    {
        $contract = new Contract;

        $form = $this->createForm(ContractType::class, $contract);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $contract = $form->getData();

                
                $entityManager = $doctrine->getManager();
                $entityManager->persist($contract); //figer les données 
                $entityManager->flush(); // push les données

                $this->addFlash(
                    'success',
                    'La Création du contrat et bien enregistrée.'
                );

                return $this->redirectToRoute('app_contract_list');

            }
        }
       
        return $this->render('admin_main/contract_new.html.twig', [
            'form' => $form->createView(), 
            'flash' => $this,
            'customer' => $contract->getCustomer(),
        ]);
    }
}
