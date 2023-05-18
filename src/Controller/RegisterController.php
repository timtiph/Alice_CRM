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

        // Instance new User, linked to the registerType form for the creation of a User
        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $user = $form->getData();  // injects in obj $user all data retrieved in $form
            
            // check the presence of the mail entered to avoid duplicates
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());
            
            if(!$search_email) {
                $password = $passwordHasher->hashPassword($user, $user->getPassword()); // hash salt password 
                $user->setPassword($password); // send password in obj User
                
                // we slug the name of the User
                $fullname = $user->getFirstname()." ".$user->getLastname();
                $slugify = new Slugify();
                $slugify = $slugify->slugify($fullname);
                $user->setSlug($slugify);
                
                
                //return entity manager
                $entityManager = $doctrine->getManager();
                $entityManager->persist($user); //figer les données 
                $entityManager->flush(); // push les données

                // generate email signature
                $signatureComponents = $verifyEmailHelper->generateSignature(
                    'app_verify_email',
                    $user->getId(),
                    $user->getEmail(),
                    ['id' => $user->getId()]
                );

                // instance of new email confirmation account
                $email = new TemplatedEmail();
                $email->from(new Address('no-reply@alice-le-blog.fr', 'Alice_CRM'));
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
                    'L\'email que vous avez renseigné existe déjà !! Connectez-vous.'
                );
                return $this->redirectToRoute('app_login');
                
            }
                        
        }


        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'flash' => $this,
            'user' => $user,
        ]);
    }

    #[Route('/confirmation-email-envoye', name: 'app_send_email_confirm')]
    public function sendConfirmEmail (): Response
    {
        //displays a message to confirm the sending of an email to confirm the creation of an account
        return $this->render('register/register_confirm.html.twig');
    }

    

    #[Route('/verification-email-inscription', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        // get id in URL
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
            $user->setIsVerified(true);
            $user->setRoles(["ROLE_USER"]);
            $userRepository->save($user, true);
            $this->addFlash('success', 'Votre compte est vérifié! Vous pouvez vous connecter.');

            // TODO : j'ai pas fait de vérif en cas d'erreur ... ce que ça donne !!!
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('app_register');
        }



        return $this->redirectToRoute('app_login');
    }
}

