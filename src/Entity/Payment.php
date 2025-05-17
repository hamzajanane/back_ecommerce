<?php
// src/Entity/Payment.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private $id;

    #[ORM\Column(type:"string")]
    private $stripePaymentId;

    #[ORM\Column(type:"integer")]
    private $amount;

    #[ORM\Column(type:"string")]
    private $currency;

    #[ORM\Column(type:"string")]
    private $status;

    #[ORM\Column(type:"datetime")]
    private $createdAt;

}
