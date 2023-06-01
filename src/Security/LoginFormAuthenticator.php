<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator, private EntityManagerInterface $entityManager)
    {
    }

    public function authenticate(Request $request): Passport
    {
        
        $email = $request->request->get('email', '');

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            // User not found, throw an exception
            throw new CustomUserMessageAuthenticationException('Vous n\'êtes pas enregistré. Veuillez vous inscrire pour accéder à votre compte.');
        }

        if (!$user->getIsVerified()) {
            // Email not verified, redirect user to verification page with an error message
            throw new CustomUserMessageAuthenticationException('Votre compte n\'est pas vérifié. Veuillez lire le mail qui vous a été envoyé lors de votre inscription et suivre les indications. (Pensez à vérifier vos spams.)<br> Vous pouvez aussi <a href="mailto:alice.webdeveloppement@gmail.com?subject=' . rawurlencode('Problème avec la vérification de l\'email de ' . $user->getFirstname() ." ". $user->getLastname()) . '" class="hover-link">écrire à Antoine.</a>');
        }

        //$request->getSession()->set(Security::LAST_USERNAME, $email); // => généré automatiquement mais déprécié par Intelephense
        $request->getSession()->set(SecurityBundleSecurity::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // For example:
        // throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
        
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
        
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
