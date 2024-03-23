<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UpdateFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Intl\Countries;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
    public function update(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $countries = Countries::getNames();

        $form = $this->createForm(UpdateFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            if (!empty($password)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Vos informations ont bien été mise à jour !');

            return $this->redirectToRoute('app_update');
        }

        return $this->render('access/update.html.twig', [
            'countries' => $countries,
            'updateForm' => $form->createView(),
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
