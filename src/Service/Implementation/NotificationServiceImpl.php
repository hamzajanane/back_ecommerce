<?php

namespace App\Service\Implementation;

use App\Entity\Order;
use App\Service\NotificationServiceInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificationServiceImpl implements NotificationServiceInterface
{
    private MailerInterface $mailer;
    private string $adminEmail;

    public function __construct(
        MailerInterface $mailer,
        string $adminEmail = 'admin@example.com'
    ) {
        $this->mailer = $mailer;
        $this->adminEmail = $adminEmail;
    }
    /**
     * {@inheritdoc}
     */
    public function sendOrderCreationNotification(Order $order): void
    {
        $email = (new Email())
            ->from('admin@example.com')
            ->to($this->adminEmail)
            ->subject('Nouvelle commande #' . $order->getId() . ' reçue')
            ->html($this->createEmailContent($order));

        $this->mailer->send($email);
    }
    /**
     * Crée le contenu HTML de l'email de notification
     */
    private function createEmailContent(Order $order): string
    {
        return '
            <h1>Nouvelle commande reçue</h1>
            <p>Une nouvelle commande a été créée dans le système.</p>           
            <p>Connectez-vous au tableau de bord administrateur pour plus de détails.</p>
        ';
    }
}