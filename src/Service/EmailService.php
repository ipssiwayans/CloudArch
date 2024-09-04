<?php

namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param mixed $user
     *
     * @throws TransportExceptionInterface
     */
    public function sendEmail(string $type, $user): void
    {
        $email = new Email();

        switch ($type) {
            case 'account_creation':
                $email->subject('Confirmation de la création de votre compte.')
                    ->to($user->getEmail())
                    ->from('contact@stomen.site')
                    ->text('Merci pour votre inscription . Vous avez souscrit à un abonnement de 20 Go et vous avez maintenant accès à votre espace');
                break;
            case 'account_storage':
                $email->subject('Confirmation de votre paiement pour l\'espace de stockage.')
                    ->to($user->getEmail())
                    ->from('contact@stomen.site')
                    ->text('Merci pour votre achat ! Votre nouvel espace de stockage est maintenant disponible.');
                break;
            case 'account_deletion':
                $email->subject('Confirmation de la suppression de votre compte.')
                    ->to($user->getEmail())
                    ->from('contact@stomen.site')
                    ->text('Votre compte a bien été supprimé. Nous espérons vous revoir bientôt !');
                break;
            case 'admin_notification':
                $email->subject('Suppression du compte de ' . $user->getFirstname() . ' ' . $user->getLastname())
                    ->to('dytsaw94@gmail.com')
                    ->from('contact@stomen.site')
                    ->text('Le compte de ' . $user->getFirstname() . ' ' . $user->getLastname() . ' a bien été supprimé. Le nombre de fichier supprimé est de ' . count($user->getFiles()));
                break;
            default:
                throw new \InvalidArgumentException('Invalid email type');
        }

        $this->mailer->send($email);
    }
}
