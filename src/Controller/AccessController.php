<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccessController extends AbstractController
{
    #[Route('/access', name: 'app_access')]
    public function index(): Response
    {
        return $this->render('access/index.html.twig', [
            'controller_name' => 'AccessController',
        ]);
    }
}
