<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Service\EmailService;
use App\Service\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

class StorageController extends AbstractController
{
    private PaymentService $paymentService;
    private EmailService $emailService;

    public function __construct(PaymentService $paymentService, EmailService $emailService)
    {
        $this->paymentService = $paymentService;
        $this->emailService = $emailService;
    }

    /**
     * @throws ApiErrorException
     */
    #[Route('/create-checkout-session-storage', name: 'create_checkout_session_storage')]
    public function createCheckoutSession(): Response
    {
        $session = $this->paymentService->createCheckoutSession(
            'Stockage supplémentaire de 20 Go',
            2000,
            'app_storage_payment_success',
            'app_payment_cancel'
        );

        return $this->redirect($session->url, 303);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/storage-payment-success', name: 'app_storage_payment_success')]
    public function paymentSuccess(
        EntityManagerInterface $entityManager,
    ): Response {
        $user = $this->getUser();

        $this->emailService->sendEmail('account_storage', $user);

        $user
            ->setTotalStorage($user->getTotalStorage() + 20)
            ->setRegistrationDate(new \DateTime());
        $entityManager->persist($user);
        $entityManager->flush();

        $invoice = new Invoice();
        $invoice
            ->setUser($user)
            ->setDate(new \DateTime())
            ->setAmountHt(16.60)
            ->setObject('Stockage')
            ->setQuantity(1)
            ->setDescription('Ajout de 20 Go de stockage supplémentaire')
            ->setAmountTva(3.40)
            ->setUnitPriceHt(16.60)
            ->setTotalAmount(20.00);
        $entityManager->persist($invoice);
        $entityManager->flush();

        $storage = 'checkout_session_storage';

        $this->addFlash('success', 'Paiement effectué avec succès !');

        return $this->render('access/payment_success.html.twig', [
            'storage' => $storage,
        ]);
    }
}
