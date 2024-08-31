<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class BreadcrumbListener
{
    private SessionInterface $session;
    private LoggerInterface $logger;
    private array $excludedRoutes = ['app_login', 'app_registration'];

    public function __construct(SessionInterface $session, LoggerInterface $logger)
    {
        $this->session = $session;
        $this->logger = $logger;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (HttpKernelInterface::MAIN_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $currentRoute = $request->attributes->get('_route');

        if (in_array($currentRoute, $this->excludedRoutes, true)) {
            return;
        }

        $breadcrumbs = $this->session->get('breadcrumbs', []);

        $parentRouteIndex = array_search($currentRoute, $breadcrumbs, true);
        if (false !== $parentRouteIndex) {
            $breadcrumbs = array_slice($breadcrumbs, 0, $parentRouteIndex + 1);
        } else {
            $breadcrumbs[] = $currentRoute;
        }

        $this->session->set('breadcrumbs', $breadcrumbs);

        $this->logger->info('Breadcrumbs updated', ['breadcrumbs' => $breadcrumbs]);
    }
}
