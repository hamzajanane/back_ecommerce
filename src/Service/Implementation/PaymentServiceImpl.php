<?php
namespace App\Service\Implementation;

use App\Service\PaymentServiceInterface;
use Stripe\StripeClient;

class PaymentServiceImpl implements PaymentServiceInterface
{
    private $stripe;

    public function __construct(string $stripeSecretKey)
    {
        $this->stripe = new StripeClient($stripeSecretKey);
    }

    public function createPaymentIntent(int $amount, string $currency = 'eur')
    {
        return $this->stripe->paymentIntents->create([
            'amount' => $amount,
            'currency' => $currency,
        ]);
    }
}
