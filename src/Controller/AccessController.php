<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UpdateFormType;
use App\Manager\FileManager;
use App\Service\EmailService;
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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AccessController extends AbstractController
{
    private Security $security;
    private Filesystem $filesystem;
    private PaymentService $paymentService;
    private EmailService $emailService;

    public function __construct(
        Security $security,
        Filesystem $filesystem,
        PaymentService $paymentService,
        EmailService $emailService
    ) {
        $this->security = $security;
        $this->filesystem = $filesystem;
        $this->paymentService = $paymentService;
        $this->emailService = $emailService;
    }

    #[Route('/registration', name: 'app_registration')]
    public function registration(Request $request, UserPasswordHasherInterface $passwordHasher): Response
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
    public function paymentSuccess(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $request->getSession()->get('registration_data');

        $this->emailService->sendEmail('account_creation', $user);

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

    #[Route('/profile', name: 'app_profile')]
    public function profile(FileManager $fileManager): Response
    {
        $user = $this->getUser();

        $totalFiles = $fileManager->getTotalFilesByUser($user);
        $storageUsed = $fileManager->getStorageUsedByUser($user);
        $totalStorage = $user->getTotalStorage();
        $availableStorage = ($totalStorage * (1024 ** 3)) - $storageUsed;
        $storagePercentage = (($storageUsed / (1024 ** 3)) / $totalStorage) * 100;

        $invoices = count($user->getInvoices());

        return $this->render('access/profile.html.twig', [
            'user' => $user,
            'totalFiles' => $totalFiles,
            'storageUsed' => $storageUsed,
            'storagePercentage' => $storagePercentage,
            'availableStorage' => $availableStorage,
            'invoices' => $invoices,
        ]);
    }

    #[Route('/update/{id}', name: 'app_update')]
    public function update(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, int $id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        $countries = Countries::getNames();

        if ($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_error');
        }

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

                        $this->addFlash('success', 'Les informations ont bien été mise à jour !');
                    } else {
                        $this->addFlash('danger', "L'ancien mot de passe est incorrect.");
                    }
                }
            } else {
                $entityManager->flush();

                $this->addFlash('success', 'Les informations ont bien été mise à jour !');
            }

            return $this->redirectToRoute('app_update', ['id' => $user->getId()]);
        }

        return $this->render('access/update.html.twig', [
            'countries' => $countries,
            'updateForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/delete/{id}', name: 'app_delete')]
    public function delete(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, SessionInterface $session, int $id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if ($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_error');
        }

        $imagePath = $this->getParameter('images_directory') . '/' . $user->getImageFilename();
        if ($user->getImageFilename() && file_exists($imagePath)) {
            $this->filesystem->remove($imagePath);
        }

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

        $entityManager->remove($user);
        $entityManager->flush();

        if ($this->getUser() == $user) {
            $tokenStorage->setToken(null);
            $session->invalidate();
        } else {
            $this->addFlash('danger', 'Le compte a bien été supprimé !');

            return $this->redirectToRoute('app_admin');
        }

        $this->emailService->sendEmail('account_deletion', $user);
        $this->emailService->sendEmail('admin_notification', $user);

        $this->addFlash('danger', 'Le compte a bien été supprimé !');

        return $this->redirectToRoute('app_registration');
    }
}
