<?php

namespace App\Twig;

use App\Entity\DynamicContent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use \HTMLPurifier;

class AppExtension extends AbstractExtension
{
// Import des services nécessaires aux dynamic_contents
    private $doctrine;
    private $purifier;
    private $urlGenerator;
    private $authenticateUser;


    public function __construct(ManagerRegistry $doctrine, HTMLPurifier $purifier, UrlGeneratorInterface $urlGenerator, AuthorizationCheckerInterface $authenticateUser)
    {
        $this->doctrine = $doctrine;
        $this->purifier = $purifier;
        $this->urlGenerator = $urlGenerator;
        $this->authenticateUser = $authenticateUser;
    }

    public function getFunctions(): array
    {
        // Creation of the twig function to create the dynamic_contents
        return [
            new TwigFunction('display_dynamic_content', [$this, 'displayDynamicContent'], ['is_safe' => ['html']
            ]),
        ];
    }

    public function displayDynamicContent(string $name, string $slug, int $id): string
    {

        // We will search by name the dynamic content we want
        $dynamicContentRepo = $this->doctrine->getRepository(DynamicContent::class);

        $currentDynamicContent = $dynamicContentRepo->findOneByName($name);

        if($this->authenticateUser->isGranted('ROLE_ADMIN')){
        // If the user is admin, we create an edit button with a specific url in the name of the dynamic content.
            return (empty($currentDynamicContent) ? '' : $this->purifier->purify($currentDynamicContent->getContent())) . ('<a href="' . $this->urlGenerator->generate('dynamic_content_edit', ['name' => $name, 'slug' => $slug, 'id' =>$id]) . '">Modifier</a>');

        } else {
            //Otherwise, we display the dynamic content
            return (empty($currentDynamicContent) ? '' : $this->purifier->purify($currentDynamicContent->getContent()));
        }
    }

}
