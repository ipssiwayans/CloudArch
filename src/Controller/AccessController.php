<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Intl\Countries;

class AccessController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('access/login.html.twig', [
        ]);
    }

    #[Route('/signup', name: 'app_signup')]
    public function logout(): Response
    {
        $countries = Countries::getNames();
        return $this->render('access/sign_up.html.twig', [
            'countries' => $countries,
        ]);
    }
}
