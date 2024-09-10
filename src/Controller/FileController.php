<?php

namespace App\Controller;

use App\Entity\File;
use App\Form\AddFileType;
use App\Form\EditFileNameType;
use App\Manager\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/file')]
class FileController extends AbstractController
{
    #[Route('/', name: 'app_file')]
    public function index(FileManager $fileManager): Response
    {
        $user = $this->getUser();

        $files = $fileManager->getUserFiles();

        return $this->render('file/index.html.twig', [
            'files' => $files,
            'user' => $user,
        ]);
    }

    #[Route('/upload', name: 'app_add_file')]
    public function add_file(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $user = $this->getUser();

        $fileEntity = new File();
        $form = $this->createForm(AddFileType::class, $fileEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            if ($file) {
                $fileEntity->setName($file->getClientOriginalName());
                $fileEntity->setSize($file->getSize());
                $fileEntity->setFormat($file->getMimeType());
                $fileEntity->setCreation(new \DateTime('now'));

                $fileEntity->setUser($user);

                $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';
                $file->move($destination, $file->getClientOriginalName());

                $entityManager->persist($fileEntity);
                $entityManager->flush();

                $this->addFlash('success', 'Fichier téléchargé avec succès');

                return $this->redirectToRoute('app_file');
            }
            $this->addFlash('danger', 'Veuillez sélectionner un fichier');
        }

        return $this->render('file/add_file.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_edit_file', methods: ['GET', 'POST'])]
    public function edit(Request $request, File $file, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($file->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_error');
        }

        $oldFileName = $file->getName();
        $oldFileExtension = pathinfo($oldFileName, PATHINFO_EXTENSION);
        $form = $this->createForm(EditFileNameType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newFileName = $file->getName();
            $newFileName = $newFileName . '.' . $oldFileExtension;
            $file->setName($newFileName);
            $file->setLatestChanges(new \DateTime('now'));

            if ($oldFileName !== $newFileName) {
                $oldFilePath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $oldFileName;
                $newFilePath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $newFileName;
                rename($oldFilePath, $newFilePath);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Nom du fichier modifié avec succès.');

            return $this->redirectToRoute('app_file');
        }

        return $this->render('file/edit_file.html.twig', [
            'file' => $file,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_delete_file', methods: ['POST'])]
    public function delete(File $file, EntityManagerInterface $entityManager): Response
    {
        if ($file->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_error');
        }

        $filePath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $file->getName();
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $entityManager->remove($file);
        $entityManager->flush();

        $this->addFlash('success', 'Fichier supprimé avec succès.');

        return $this->redirectToRoute('app_file');
    }

    #[Route('/download/{id}', name: 'app_download_file', methods: ['GET'])]
    public function download(File $file): Response
    {
        $user = $this->getUser();

        if ($file->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_error');
        }

        $filePath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $file->getName();
        if (file_exists($filePath)) {
            return $this->file($filePath, $file->getName());
        }

        $this->addFlash('danger', 'Fichier introuvable.');

        return $this->redirectToRoute('app_file');
    }
}
