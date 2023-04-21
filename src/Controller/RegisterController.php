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

    public function __construct(EntityManagerInterface $entityManager)
    {
        // create entityManager => doctrine manager used for manipulation BDD
        $this->entityManager = $entityManager;
    }


    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, PersistenceManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, VerifyEmailHelperInterface $verifyEmailHelper, MailerInterface $mailer): Response
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
                // dd($signatureComponents); ok, génére Token, URI ....


                $email = (new TemplatedEmail())
                ->from(new Address('ugoblackandwhite@gmail.com', 'Alice_CRM'))
                ->to($user->getEmail())
                ->subject('Confirmation de votre Email | Alice CRM')
                ->htmlTemplate('register/email.html.twig')
                ->context([
                    'signatureComponents' => $signatureComponents,
                ])
            ;
            $mailer->send($email);
            //dd($mailer);

                // TODO: Revoir le template envoi email : templates/reset_password/email.html.twig
                // Est ce que pour autoriser l'acces apres vérif de l'email, il convient de Flush seulement après click sur le lien de l'email, qui renvoit donc toutes les infos necessaire dans la BDD ? C'est à dire revoir la logic dans l'autre sens ???
                // $mail = new Mail();
                // $api_key_public = $this->getParameter('app.mailjet.public_key');
                // $api_key_secret = $this->getParameter('app.mailjet.private_key');
                // $title = 'Confirmez votre Email';
                // $subject = "Votre compte est en attende de la validation.";
                // $content = "Bonjour ".$user->getFirstname()." ".$user->getLastname()."et merci pour votre inscription. <br><br> Afin de pouvoir vous connecter, Merci de cliquer sur ce lien :";
                // $sign_key = $signatureComponents->getSignedUrl();
                // // complete le mail de la class Mail
                // $mail->sendConfirmEmail($api_key_public, $api_key_secret, $subject, $title, $content, $sign_key);

                
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
        ]);
    }

    #[Route('/confirmation-email-envoye', name: 'app_send_email_confirm')]
    public function SendConfirmEmail (): Response
    {
        return $this->render('register/register_confirm.html.twig');
    }

    

    #[Route('/verification-email-inscription', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository, EntityManagerInterface $entityManager, string $signatureComponents): Response
    {
        $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }
        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail(),
            );
            // TODO : j'ai pas fait de vérif en cas d'erreur ... ce que ça donne !!!
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('app_register');
        }
        $user->setIsVerified(true);
        $user->setRoles(["ROLE_USER"]);

        $entityManager->flush();

        $this->addFlash('success', 'Votre compte est vérifié! Vous pouvez vous connecter.');


        return $this->redirectToRoute('app_login', [
            'signatureComponents' => $signatureComponents,
        ]);
    }
}
