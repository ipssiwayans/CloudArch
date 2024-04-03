<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UpdateFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Intl\Countries;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AccessController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/registration', name: 'app_registration')]
    public function registration(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($this->security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_home');
        }

        $countries = Countries::getNames();
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $user->setTotalStorage(20);
            $user->setRegistrationDate(new \DateTime());

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('access/registration.html.twig', [
            'countries' => $countries,
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/reset', name: 'app_reset_password')]
    public function resetPassword(): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();

        return $this->render('access/reset.html.twig');
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();

        return $this->render('access/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/update', name: 'app_update')]
    public function update(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $countries = Countries::getNames();

        $form = $this->createForm(UpdateFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            // this condition is needed because the 'image' field is not required
            // so the image file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Move the file to the directory where images are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'imageFilename' property to store the image file name
                // instead of its contents
                $user->setImageFilename($newFilename);
            }

            // On vérifie si l'utilisateur souhaite changer de mot de passe
            if ($form->get('change_password')->getData()) {
                $oldPassword = $form->get('old_password')->getData();
                $newPassword = $form->get('password')->getData();

                // On change le mot de passe si les champs sont remplis
                if (!empty($oldPassword) && !empty($newPassword)) {
                    // On valide les données du formulaire avec le groupe de validation 'password_change'
                    $errors = $validator->validate($form->getData(), null, ['password_change']);

                    // On vérifie si le mot de passe actuel est correct
                    if ($passwordHasher->isPasswordValid($user, $oldPassword)) {
                        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                        $user->setPassword($hashedPassword);

                        $entityManager->flush();

                        $this->addFlash('success', 'Vos informations ont bien été mise à jour !');
                    } else {
                        // On ajoute un message d'erreur si le mot de passe actuel est incorrect
                        $this->addFlash('danger', "L'ancien mot de passe est incorrect.");
                    }
                }
            } else {
                $entityManager->flush();

                $this->addFlash('success', 'Vos informations ont bien été mise à jour !');
            }

            return $this->redirectToRoute('app_update');
        }

        return $this->render('access/update.html.twig', [
            'countries' => $countries,
            'updateForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/delete', name: 'app_delete')]
    public function delete(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, SessionInterface $session): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        $entityManager->remove($user);
        $entityManager->flush();

        $tokenStorage->setToken(null);
        $session->invalidate();

        $this->addFlash('danger', 'Votre compte a bien été supprimé !');

        return $this->redirectToRoute('app_registration');
    }
}
