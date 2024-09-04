<?php

namespace App\Manager;

use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class FileManager
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function getUserFiles(): array
    {
        $currentUser = $this->security->getUser();

        return $this->entityManager->getRepository(File::class)->findBy(['user' => $currentUser]);
    }

    public function getTotalFilesByUser($user): int
    {
        return count($this->entityManager->getRepository(File::class)->findBy(['user' => $user]));
    }

    public function getStorageUsedByUser($user): int
    {
        $files = $this->entityManager->getRepository(File::class)->findBy(['user' => $user]);
        $totalSize = 0;
        foreach ($files as $file) {
            $totalSize += $file->getSize();
        }

        return $totalSize;
    }

    public function getTotalFiles(): int
    {
        return count($this->entityManager->getRepository(File::class)->findAll());
    }
}
