<?php

namespace App\Controller;

use App\Service\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private Security $security;
    private BreadcrumbService $breadcrumbService;

    public function __construct(Security $security, BreadcrumbService $breadcrumbService)
    {
        $this->security = $security;
        $this->breadcrumbService = $breadcrumbService;
    }

    #[Route('/', name: 'app_home')]
    public function index(SessionInterface $session): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }

        $this->breadcrumbService->setSession($session);

        $this->breadcrumbService->addBreadcrumb('app_home');

        $user = $this->getUser();

        return $this->render('app.html.twig', [
            'user' => $user,
            'breadcrumbs' => $this->breadcrumbService->getBreadcrumbs(),
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
}
