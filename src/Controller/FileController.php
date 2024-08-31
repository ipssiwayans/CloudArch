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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/file')]
class FileController extends AbstractController
{
    #[Route('/', name: 'app_file')]
    public function index(FileManager $fileManager, SessionInterface $session): Response
    {
        $files = $fileManager->getUserFiles();

        return $this->render('file/index.html.twig', [
            'files' => $files,
            'breadcrumbs' => $session->get('breadcrumbs', []),
        ]);
    }

    #[Route('/upload', name: 'app_add_file')]
    public function add_file(
        Request $request,
        EntityManagerInterface $entityManager,
        Security $security,
    ): Response {
        $fileEntity = new File();
        $form = $this->createForm(AddFileType::class, $fileEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $file = $form->get('file')->getData();
                if ($file) {
                    $fileEntity->setName($file->getClientOriginalName());
                    $fileEntity->setSize($file->getSize());
                    $fileEntity->setFormat($file->getMimeType());
                    $fileEntity->setCreation(new \DateTime('now'));

                    $currentUser = $security->getUser();
                    $fileEntity->setUser($currentUser);

                    $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';
                    $file->move($destination, $file->getClientOriginalName());

                    $entityManager->persist($fileEntity);
                    $entityManager->flush();

                    $this->addFlash('success', 'Fichier téléchargé avec succès');

                    return $this->redirectToRoute('app_file');
                }
                $this->addFlash('error', 'Veuillez sélectionner un fichier');
            } else {
                $errors = $form->getErrors(true);
                foreach ($errors as $error) {
                    $errorMessage = $error->getMessage();
                    if ('This file is too large. The maximum size allowed is 5MB.' === $errorMessage) {
                        $this->addFlash('error', 'Le fichier est trop volumineux. Taille maximale autorisée : 5MB');
                    } elseif (str_contains($errorMessage, 'The mime type of the file is invalid')) {
                        $this->addFlash('error', 'Type de fichier non valide. Veuillez télécharger un fichier au format autorisé.');
                    } elseif ('The uploaded file was too large. Please try to upload a smaller file.' === $errorMessage) {
                        $this->addFlash('error', 'Le fichier téléchargé est trop volumineux. Veuillez essayer un fichier plus petit.');
                    } else {
                        $this->addFlash('error', $errorMessage);
                    }
                }
            }
        }

        return $this->render('file/add_file.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/file/edit/{id}', name: 'app_edit_file', methods: ['GET', 'POST'])]
    public function edit(Request $request, File $file, EntityManagerInterface $entityManager): Response
    {
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
            'form' => $form->createView(),
        ]);
    }

    #[Route('/file/delete/{id}', name: 'app_delete_file', methods: ['POST'])]
    public function delete(Request $request, File $file, EntityManagerInterface $entityManager): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $file->getName();
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $entityManager->remove($file);
        $entityManager->flush();

        $this->addFlash('success', 'Fichier supprimé avec succès.');

        return $this->redirectToRoute('app_file');
    }
}
