<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Entity\Customer;
use App\Entity\SerpInfo;
use App\Form\ContractType;
use App\Form\SerpInfoType;
use App\Form\EditContractType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
    public function showContract($id, Request $request, \Doctrine\Persistence\ManagerRegistry $doctrine): Response
    {
        $contract = $this->entityManager->getRepository(Contract::class)->findOneById($id);
        $serpInfos = $contract->getSerpInfos();

        $serpInfoForm=$this->createForm(SerpInfoType::class);
        $serpInfoForm->handleRequest($request);

        if($serpInfoForm->isSubmitted() && $serpInfoForm->isValid()) {
            $newSerpInfo = new SerpInfo();
            $newKeyword = $serpInfoForm->get('keyword')->getData();
            $newSerpInfo->setKeyword($newKeyword);
            $newSerpInfo->setContract($contract);


            $em = $doctrine->getManager();
            $em->persist($newSerpInfo);
            $em->flush();

            $this->addFlash('success', 'Mot clé enregistré avec succès');

            return $this->redirectToRoute('app_contract_show', ['id' => $id]);
        }


        return $this->render('admin_main/contract_show.html.twig', [
            'serpInfoForm' => $serpInfoForm->createView(),
            'contract' => $contract,
            'serpInfos' => $serpInfos,
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
                    'La création du contrat est bien enregistrée.'
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
    public function removeContract(Contract $contract, Request $request, EntityManagerInterface $entityManager, PersistenceManagerRegistry $doctrine): Response
    {

        $customer = $contract->getCustomer();
        $customerId = $customer->getId();
        $customerSlug = $customer->getSlug();
        $csrf_token = $request->query->get('csrf_token', '');

        if (!$this->isCsrfTokenValid('delete_contract' . $contract->getId(), $csrf_token)) {

            $this->addFlash(
                'error',
                'Vous ne pouvez pas supprimer cet élément.'
            );

        } else {
                $entityManager = $doctrine->getManager();
                $entityManager->remove($contract);
                $entityManager->flush(); // push les données
                $this->addFlash(
                    'success',
                    'Le contrat à bien été supprimé.'
                );
        }

        return $this->redirectToRoute('app_customer', ['id' => $customerId, 'slug' => $customerSlug]);

    }

    #[Route('/admin/contrat/{id}/supprimer-serp-info/{serpInfoId}', name: 'app_serp_info_remove')]
    #[ParamConverter('serpInfo', options: ['id' => 'serpInfoId'])]
    public function removeSerpInfo(SerpInfo $serpInfo, Request $request, EntityManagerInterface $entityManager, PersistenceManagerRegistry $doctrine): Response
    {

        $contract = $serpInfo->getContract();
        $contractId = $contract->getId();
        $csrf_token = $request->query->get('csrf_token', '');

        if (!$this->isCsrfTokenValid('delete_serp_info' . $serpInfo->getId(), $csrf_token)) {

            $this->addFlash(
                'error',
                'Vous ne pouvez pas supprimer cet élément.'
            );

        } else {

            $entityManager = $doctrine->getManager();
            $entityManager->remove($serpInfo);
            $entityManager->flush(); // push les données
            $this->addFlash(
                'success',
                'Le mot clé à bien été supprimé.'
            );
        }

        return $this->redirectToRoute('app_contract_show', [
            'id' => $contractId,
        ]);

    }

}
