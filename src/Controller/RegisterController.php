<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Cocur\Slugify\Slugify;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry ;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegisterController extends AbstractController
{
    private $entityManager;

    private $verifyEmailHelper;
    private $mailer;

    public function __construct(EntityManagerInterface $entityManager, VerifyEmailHelperInterface $helper, MailerInterface $mailer)
    {
        // create entityManager => doctrine manager used for manipulation BDD
        $this->entityManager = $entityManager;

        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
    }


    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, PersistenceManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, VerifyEmailHelperInterface $verifyEmailHelper): Response
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
                
                // on slug le nom du User
                $fullname = $user->getFirstname()." ".$user->getLastname();
                $slugify = new Slugify();
                $slugify = $slugify->slugify($fullname);
                $user->setSlug($slugify);
                
                
                //return entity manager
                $entityManager = $doctrine->getManager();
                $entityManager->persist($user); //figer les données 
                $entityManager->flush(); // push les données

                $signatureComponents = $verifyEmailHelper->generateSignature(
                    'app_verify_email',
                    $user->getId(),
                    $user->getEmail(),
                    ['id' => $user->getId()]
                );

                $email = new TemplatedEmail();
                $email->from(new Address('ugoblackandwhite@gmail.com', 'Alice_CRM'));
                $email->to($user->getEmail());
                $email->subject('Confirmation de votre Email | Alice CRM');
                $email->htmlTemplate('register/email.html.twig');
                $email->context([
                    'signedUrl' => $signatureComponents->getSignedUrl(), 
                    'signatureComponents'=> $signatureComponents,
                ]);

                $this->mailer->send($email);
                
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
            'flash' => $this,
            'user' => $user,
        ]);
    }

    #[Route('/confirmation-email-envoye', name: 'app_send_email_confirm')]
    public function SendConfirmEmail (): Response
    {
        return $this->render('register/register_confirm.html.twig');
    }

    

    #[Route('/verification-email-inscription', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        
        $id = $request->get('id');

        // Verify the user id exists and is not null
        if (null === $id) {
        return $this->redirectToRoute('app_home');
        }

        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException();
        }
        try {
            $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());

            // TODO : j'ai pas fait de vérif en cas d'erreur ... ce que ça donne !!!
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('app_register');
        }
        $user->setIsVerified(true);
        $user->setRoles(["ROLE_USER"]);

        $entityManager->flush();

        $this->addFlash('success', 'Votre compte est vérifié! Vous pouvez vous connecter.');


        return $this->redirectToRoute('app_login');
    }
}

