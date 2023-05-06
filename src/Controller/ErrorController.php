<?php

namespace App\Controller;

use Throwable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ErrorController extends AbstractController
{
    #[Route('/error', name: 'app_error')]

    public function show(Throwable $exception): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error.html.twig', [
            'exception' => $exception,
        ]);
    }
}
