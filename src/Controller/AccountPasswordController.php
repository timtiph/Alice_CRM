<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/mon-compte/modifier-mon-mot-de-passe', name: 'app_account_password')]
    public function index(Request $request, PersistenceManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $notificationOk = null;
        $notificationError = null;
        $user = $this->getUser(); // User connecté
        // dd($user); // ok User connecté


        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $old_pwd = $form->get('old_password')->getData();
        // dd($old_pwd); // ok variable chargée
            
            if($passwordHasher->isPasswordValid($user, $old_pwd)){

                $new_pwd = $form->get('new_password')->getData();
                // dd($new_pwd); // ok récup du new_password
                $password = $passwordHasher->hashPassword($user, $new_pwd);

                $user->setPassword($password);
                $entityManager = $doctrine->getManager();
                $entityManager->flush();
                $notificationOk = 'Votre mot de passe à bien été mis à jour.';
            
            } else {
                
                $notificationError = "Votre mot de passe actuel n'est pas le bon !";

            }
        }


        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notificationOk' => $notificationOk,
            'notificationError' => $notificationError
        ]);
    }
}

        