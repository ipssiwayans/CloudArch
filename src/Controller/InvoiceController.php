<?php

namespace App\Controller;

use App\Manager\InvoiceManager;
use App\Service\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/invoice')]
class InvoiceController extends AbstractController
{
    private BreadcrumbService $breadcrumbService;

    public function __construct(BreadcrumbService $breadcrumbService)
    {
        $this->breadcrumbService = $breadcrumbService;
    }

    #[Route('/all', name: 'app_invoice_all')]
    public function allInvoice(Session $session, InvoiceManager $invoiceManager): Response
    {
        $invoices = $invoiceManager->getInvoiceByUser($this->getUser());

        $this->breadcrumbService->setSession($session);
        $this->breadcrumbService->addBreadcrumb('app_invoice_all');

        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoices,
            'breadcrumbs' => $this->breadcrumbService->getBreadcrumbs(),
        ]);
    }

    #[Route('/{id}', name: 'app_invoice_show')]
    public function showInvoice(Session $session, InvoiceManager $invoiceManager, int $id): Response
    {
        $invoice = $invoiceManager->getInvoiceById($id);
        $user = $this->getUser();

        dump($invoice);
        dump($user);
        $this->breadcrumbService->setSession($session);
        $this->breadcrumbService->addBreadcrumb('app_invoice_show');
        $this->breadcrumbService->addBreadcrumb('Facture n°' . $id);

        return $this->render('invoice/show.html.twig', [
            'user' => $user,
            'invoice' => $invoice,
            'breadcrumbs' => $this->breadcrumbService->getBreadcrumbs(),
        ]);
    }
}
