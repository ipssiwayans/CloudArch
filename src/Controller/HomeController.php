<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/', name: 'app_home')]
    public function index(SessionInterface $session): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        return $this->render('app.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/complete', name: 'app_complete')]
    public function complete(): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();

        return $this->render('prices/complete.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/error', name: 'app_error')]
    public function error(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
    }
}
