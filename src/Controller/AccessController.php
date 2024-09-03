<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UpdateFormType;
use App\Manager\FileManager;
use App\Service\BreadcrumbService;
use App\Service\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AccessController extends AbstractController
{
    private Security $security;
    private Filesystem $filesystem;
    private BreadcrumbService $breadcrumbService;
    private PaymentService $paymentService;

    public function __construct(
        Security $security,
        Filesystem $filesystem,
        BreadcrumbService $breadcrumbService,
        PaymentService $paymentService
    ) {
        $this->security = $security;
        $this->filesystem = $filesystem;
        $this->breadcrumbService = $breadcrumbService;
        $this->paymentService = $paymentService;
    }

    #[Route('/registration', name: 'app_registration')]
    public function registration(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $request->getSession()->set('registration_data', $user);

            return $this->redirectToRoute('create_checkout_session');
        }

        return $this->render('access/registration.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @throws ApiErrorException
     */
    #[Route('/create-checkout-session', name: 'create_checkout_session')]
    public function createCheckoutSession(): Response
    {
        $session = $this->paymentService->createCheckoutSession(
            'Abonnement initial de 20 Go',
            2000,
            'app_payment_success',
            'app_payment_cancel'
        );

        return $this->redirect($session->url, 303);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/payment-success', name: 'app_payment_success')]
    public function paymentSuccess(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $user = $request->getSession()->get('registration_data');

        $email = new Email();
        $email
            ->subject('Confirmation de votre abonnement')
            ->to($user->getEmail())
            ->from('contact@stomen.site')
            ->text('Merci pour votre inscription . Vous avez souscrit à un abonnement de 20 Go et vous avez maintenant accès à votre espace');
        $mailer->send($email);

        if ($user instanceof User) {
            $user
                ->setTotalStorage(20)
                ->setRegistrationDate(new \DateTime());
            $entityManager->persist($user);
            $entityManager->flush();

            $invoice = new Invoice();
            $invoice
                ->setUser($user)
                ->setDate(new \DateTime())
                ->setAmountHt(16.60)
                ->setObject('Abonnement')
                ->setQuantity(1)
                ->setDescription('Abonnement initial de 20 Go')
                ->setAmountTva(3.40)
                ->setUnitPriceHt(16.60)
                ->setTotalAmount(20.00);
            $entityManager->persist($invoice);
            $entityManager->flush();

            $request->getSession()->remove('registration_data');

            return $this->render('access/payment_success.html.twig');
        }

        $this->addFlash('error', 'Erreur lors de l\'enregistrement. Veuillez réessayer.');

        return $this->redirectToRoute('app_registration');
    }

    #[Route('/payment-cancel', name: 'app_payment_cancel')]
    public function paymentCancel(Request $request): Response
    {
        $request->getSession()->remove('registration_data');
        $this->addFlash('error', 'Le paiement a échoué ou a été annulé. Veuillez réessayer.');

        return $this->redirectToRoute('app_registration');
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
    public function profile(SessionInterface $session, FileManager $fileManager): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }

        $this->breadcrumbService->setSession($session);
        $this->breadcrumbService->addBreadcrumb('app_profile');

        $user = $this->getUser();

        $totalFiles = $fileManager->getTotalFilesByUser($user);
        $storageUsed = $fileManager->getStorageUsedByUser($user);
        $storageUsed = $storageUsed / (1024 * 1024 * 1024);
        $totalStorage = $user->getTotalStorage();
        $storagePercentage = ($storageUsed / $totalStorage) * 100;

        $invoices = count($user->getInvoices());

        return $this->render('access/profile.html.twig', [
            'user' => $user,
            'totalFiles' => $totalFiles,
            'storageUsed' => $storageUsed,
            'storagePercentage' => $storagePercentage,
            'invoices' => $invoices,
            'breadcrumbs' => $session->get('breadcrumbs', []),
        ]);
    }

    #[Route('/update', name: 'app_update')]
    public function update(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, BreadcrumbService $breadcrumbService, SessionInterface $session): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }
        $this->breadcrumbService->setSession($session);

        $this->breadcrumbService->addBreadcrumb('app_update');

        $user = $this->getUser();
        $countries = Countries::getNames();

        $form = $this->createForm(UpdateFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $this->filesystem->copy(
                        $imageFile->getPathname(),
                        $this->getParameter('images_directory') . '/' . $newFilename,
                        true
                    );
                } catch (FileException $e) {
                }

                $user->setImageFilename($newFilename);
            }

            if ($form->get('change_password')->getData()) {
                $oldPassword = $form->get('old_password')->getData();
                $newPassword = $form->get('password')->getData();
                if (!empty($oldPassword) && !empty($newPassword)) {
                    $errors = $validator->validate($form->getData(), null, ['password_change']);

                    if ($passwordHasher->isPasswordValid($user, $oldPassword)) {
                        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                        $user->setPassword($hashedPassword);

                        $entityManager->flush();

                        $this->addFlash('success', 'Vos informations ont bien été mise à jour !');
                    } else {
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
            'breadcrumbs' => $this->breadcrumbService->getBreadcrumbs(),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/delete', name: 'app_delete')]
    public function delete(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, SessionInterface $session, MailerInterface $mailer): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        foreach ($user->getFiles() as $file) {
            $filePath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $file->getName();
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $entityManager->remove($file);
        }

        foreach ($user->getInvoices() as $invoice) {
            $entityManager->remove($invoice);
        }

        $imagePath = $this->getParameter('images_directory') . '/' . $user->getImageFilename();
        if ($this->filesystem->exists($imagePath)) {
            $this->filesystem->remove($imagePath);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $tokenStorage->setToken(null);
        $session->invalidate();

        $email = new Email();
        $email
            ->subject('Confirmation de la suppression de votre compte.')
            ->to($user->getEmail())
            ->from('contact@stomen.site')
            ->text('Votre compte a bien été supprimé. Nous espérons vous revoir bientôt !');
        $mailer->send($email);

        $this->addFlash('danger', 'Votre compte a bien été supprimé !');

        return $this->redirectToRoute('app_registration');
    }
}
