<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_profile');
    }

    #[Route('/error', name: 'app_error')]
    public function error(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
    }
}
