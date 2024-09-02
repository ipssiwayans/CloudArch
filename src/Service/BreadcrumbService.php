<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BreadcrumbService
{
    private ?SessionInterface $session = null;
    private array $excludedRoutes = ['app_login', 'app_registration'];

    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;

        if (!$this->session->isStarted()) {
            $this->session->start();
        }
    }

    public function addBreadcrumb(string $route): void
    {
        if (!$this->session || !$this->session->isStarted()) {
            return;
        }

        if (in_array($route, $this->excludedRoutes, true)) {
            return;
        }

        if ('app_home' === $route) {
            $this->session->set('breadcrumbs', []);

            return;
        }

        $breadcrumbs = $this->session->get('breadcrumbs', []);

        $existingIndex = array_search($route, $breadcrumbs, true);

        if (false !== $existingIndex) {
            $breadcrumbs = array_slice($breadcrumbs, 0, $existingIndex + 1);
        } else {
            $breadcrumbs[] = $route;
        }

        $this->session->set('breadcrumbs', $breadcrumbs);
    }

    public function getBreadcrumbs(): array
    {
        if (!$this->session || !$this->session->isStarted()) {
            return [];
        }

        return $this->session->get('breadcrumbs', []);
    }

    public function getPageTitle(string $route): string
    {
        $pageTitles = [
            'app_home' => 'Accueil',
            'app_profile' => 'Profil',
            'app_update' => 'Modifier le profil',
            'app_file' => 'Mes fichiers',
            'app_add_file' => 'Ajouter un fichier',
            'app_edit_file' => 'Modifier un fichier',
        ];

        return $pageTitles[$route] ?? ucfirst(str_replace('_', ' ', $route));
    }
}
