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
        return $this->render('app.html.twig', []);
    }

    #[Route('/prices', name: 'app_prices')]
    public function prices(): Response
    {
        return $this->render('prices/prices.html.twig', []);
    }

    #[Route('/complete', name: 'app_complete')]
    public function complete(): Response
    {
        return $this->render('prices/complete.html.twig', []);
    }
}
