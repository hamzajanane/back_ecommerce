<?php
namespace App\Service;

interface PaymentServiceInterface
{
    public function createPaymentIntent(int $amount, string $currency = 'eur');
}
