<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\PaymentServiceInterface;

class PaymentController extends AbstractController
{
    private $paymentService;

    public function __construct(PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    #[Route('/api/payment/create', name: 'api_payment_create', methods: ['POST'])]
    public function createPayment(): JsonResponse
    {
        $amount = 1000; 
        $paymentIntent = $this->paymentService->createPaymentIntent($amount);

        return new JsonResponse([
            'clientSecret' => $paymentIntent->client_secret,
            'paymentIntentId' => $paymentIntent->id,
            'status' => $paymentIntent->status,
        ]);
    }
}
