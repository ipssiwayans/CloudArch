<?php

namespace App\Service;

class BreadcrumbService
{
    private array $pageTitles = [
        'app_home' => 'Accueil',
        'app_file' => 'Fichiers',
        'app_invoice' => 'Factures',
        'app_profile' => 'Profil',
        'app_update' => 'Modifier le profil',
        'app_registration' => 'Inscription',
        'app_admin' => 'Administration',
    ];

    public function getPageTitle(string $route): string
    {
        return $this->pageTitles[$route] ?? ucfirst(str_replace('_', ' ', $route));
    }
}