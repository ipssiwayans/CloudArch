<?php

namespace App\Controller;

use App\Manager\InvoiceManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/invoice')]
class InvoiceController extends AbstractController
{
    #[Route('/all', name: 'app_invoice_all')]
    public function allInvoice(Session $session, InvoiceManager $invoiceManager): Response
    {
        $invoices = $invoiceManager->getInvoiceByUser($this->getUser());

        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoices,
        ]);
    }

    #[Route('/{id}', name: 'app_invoice_show')]
    public function showInvoice(Session $session, InvoiceManager $invoiceManager, int $id): Response
    {
        $invoice = $invoiceManager->getInvoiceById($id);
        $user = $this->getUser();

        return $this->render('invoice/show.html.twig', [
            'user' => $user,
            'invoice' => $invoice,
        ]);
    }
}
