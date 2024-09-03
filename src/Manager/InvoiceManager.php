<?php

namespace App\Manager;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;

class InvoiceManager
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getInvoiceByUser($user): array
    {
        return $this->entityManager->getRepository(Invoice::class)->findBy(['user' => $user]);
    }

    public function getInvoiceById($id): ?Invoice
    {
        return $this->entityManager->getRepository(Invoice::class)->find($id);
    }
}
