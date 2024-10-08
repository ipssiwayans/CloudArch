<?php

namespace App\Controller;

use App\Manager\InvoiceManager;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[Route('/invoice')]
class InvoiceController extends AbstractController
{
    #[Route('/', name: 'app_invoice')]
    public function allInvoice(InvoiceManager $invoiceManager): Response
    {
        $invoices = $invoiceManager->getInvoiceByUser($this->getUser());

        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoices,
        ]);
    }

    #[Route('/{id}', name: 'app_invoice_show')]
    public function showInvoice(
        EntityManagerInterface $entityManager,
        InvoiceManager $invoiceManager,
        int $id
    ): Response {
        $invoice = $invoiceManager->getInvoiceById($id);
        $user = $invoice->getUser();

        if ($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_error');
        }

        return $this->render('invoice/show.html.twig', [
            'user' => $user,
            'invoice' => $invoice,
        ]);
    }

    #[Route('/download/{id}', name: 'app_invoice_download')]
    public function download(int $id, InvoiceManager $invoiceManager, Environment $twig): Response
    {
        $invoice = $invoiceManager->getInvoiceById($id);

        if ($invoice->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_error');
        }

        $template = $twig->load('invoice/show.html.twig');
        $html = $template->renderBlock('pdf_content', [
            'invoice' => $invoice,
            'user' => $this->getUser(),
        ]);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        $pdfContent = $dompdf->output();

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', ResponseHeaderBag::DISPOSITION_ATTACHMENT . '; filename="invoice_' . $id . '.pdf"');

        return $response;
    }
}
