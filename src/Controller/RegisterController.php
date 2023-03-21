<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry ;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        // create entityManager => doctrine manager used for manipulation BDD
        $this->entityManager = $entityManager;
    }


    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, PersistenceManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {

        // Instance Nouveau User, liée au formulaire registerType pour la création d'un User
        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $user = $form->getData();  // injecte dans obj $user toutes les données récup dans $form
            
            // vérifier la présence du mail saisie pour éviter les doublons
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());
            
            if(!$search_email) {
                $password = $passwordHasher->hashPassword($user, $user->getPassword()); // hash le password saisi
                $user->setPassword($password); // envoi le password dans obj User
                
                // dd($password);
                // dd($user);
                
                //return entity manager
                $entityManager = $doctrine->getManager();
                $entityManager->persist($user); //figer les données 
                $entityManager->flush(); // push les données

                $mail = new Mail();
                $title = 'Confirmez votre Email';
                $subject = "Votre compte est en attende de la validation.";
                $content = "Bonjour ".$user->getFirstname()." ".$user->getLastname()."et merci pour votre inscription. <br><br> Afin de pouvoir vous connecter, Merci de cliquer sur ce lien :";
                $mail->sendConfirmEmail($user->getEmail(),$user->getFirstname(), $subject, $title, $content);

                $this->addFlash(
                    'success',
                    'Votre demande d\'inscription est enregistrée.'
                );
                return $this->redirectToRoute('app_send_email_confirm');
                
            } else {
                $this->addFlash(
                    'alert',
                    'L\'email que vous avez renseigné existe déjà !! Veuillez recommencer ou vous connecter.'
                );
                return $this->redirectToRoute('app_register');
            }
            
            
        }


        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'flash' => $this            
        ]);
    }

    #[Route('/confirmation-email-envoye', name: 'app_send_email_confirm')]
    public function SendConfirmEmail (): Response
    {
        return $this->render('register/register_confirm.html.twig');
    }
}
