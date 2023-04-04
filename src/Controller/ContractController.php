<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Entity\Customer;
use App\Form\ContractType;
use App\Form\EditContractType;
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

    #[Route('/admin/contrat/{id}', name: 'app_contract_show')]
    public function showContract($id): Response
    {
        $contract = $this->entityManager->getRepository(Contract::class)->findOneById($id);

        return $this->render('admin_main/contract_show.html.twig', [
            'contract' => $contract
        ]);
    }

    #[Route('/admin/contrat/creer-un-contrat/{id}/{slug}', name: 'app_contract_add')]
    public function createContract(Request $request, EntityManagerInterface $entityManager, PersistenceManagerRegistry $doctrine, $id): Response
    {
        $customer = $this->entityManager->getRepository(Customer::class)->findOneById($id);
        $slug = $customer->getSlug();
        
        $contract = new Contract;
        

        $form = $this->createForm(ContractType::class, $contract, [
            'customer' => $customer,
        ]);
        $contract->setCustomer($customer);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $contract = $form->getData();
                //dd($contract);
                
                $entityManager = $doctrine->getManager();
                $entityManager->persist($contract); //figer les données 
                $entityManager->flush(); // push les données
                
                $this->addFlash(
                    'success',
                    'La Création du contrat et bien enregistrée.'
                );
                
                return $this->redirectToRoute('app_customer', ['id' => $id, 'slug' => $slug]);
                
            }
        }
        
        return $this->render('admin_main/contract_new.html.twig', [
            'customer' => $customer,
            'user' => $customer->getUser(),
            'flash' => $this,
            'form' => $form->createView(), 
        ]);
    }

    #[Route('/admin/contrat/modifier-un-contrat/{id}', name: 'app_contract_edit')]
    public function editContract(Request $request, $id): Response
    {
        $contract = $this->entityManager->getRepository(Contract::class)->findOneById($id);
        $customer = $contract->getCustomer();
        $slug = $customer->getSlug();
        $user = $customer->getUser();
        $userId = $user->getId();

        if (!$contract) {
            return $this->redirectToRoute('app_customer_list'); 
        }

        $form = $this->createForm(EditContractType::class, $contract);


        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_contract_show', array('id' => $id));
        }

        return $this->render('admin_main/contract_edit.html.twig', [
            'contract' => $contract,
            'form' => $form->createView(),
            'user' => $user,
            'customer' => $customer,
            'id' => $userId
        ]);
    }

    #[Route('/admin/contrat/{id}/supprimer', name: 'app_contract_remove')]
    public function removeContract(Request $request, $id): Response
    {
        $contract = $this->entityManager->getRepository(Contract::class)->findOneById($id);

        if ($contract) {
            
            $customer = $contract->getCustomer();
            $user = $customer->getUser();
            $id = $customer->getId();
            $slug = $customer->getSlug();
            
            $this->entityManager->remove($contract);
            $this->entityManager->flush();
            
            return $this->redirectToRoute('app_customer', ['id' => $id, 'slug' => $slug]);
        }

        return $this->redirectToRoute('app_contract_show', ['id' => $id]);

    }
}
