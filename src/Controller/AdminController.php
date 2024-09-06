<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\FileManager;
use App\Repository\FileRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $userRepository, FileRepository $fileRepository, FileManager $fileManager): Response
    {
        $user = $this->getUser();
        $users = $userRepository->findAll();

        $userData = [];
        foreach ($users as $user) {
            $usedStorage = 0;
            $filesPerUser = $fileManager->getTotalFilesByUser($user);
            foreach ($user->getFiles() as $file) {
                $usedStorage += $file->getSize();
            }

            $totalStorage = $user->getTotalStorage() * 1024 * 1024 * 1024;

            $availableStorage = max(0, $totalStorage - $usedStorage);

            $userData[] = [
                'user' => $user,
                'usedStorage' => $usedStorage,
                'availableStorage' => $availableStorage,
                'filesPerUser' => $filesPerUser,
            ];
        }

        $totalFiles = $fileManager->getTotalFiles();
        $todayFiles = $fileRepository->getCountTodayFiles();

        return $this->render('admin/index.html.twig', [
            'users' => $userData,
            'totalFiles' => $totalFiles,
            'todayFiles' => $todayFiles,
            'user' => $user,
        ]);
    }

    #[Route('/admin/user/{id}/files', name: 'admin_user_files')]
    public function userFiles(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $files = $user->getFiles();

        return $this->render('admin/user_files.html.twig', [
            'user' => $user,
            'files' => $files,
        ]);
    }
}
