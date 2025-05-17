<?php

namespace App\Service;

use App\Entity\Order;

interface NotificationServiceInterface
{
    /**
     * Envoie une notification pour une nouvelle commande
     * 
     * @param Order $order La commande créée
     * @return void
     */
    public function sendOrderCreationNotification(Order $order): void;
}