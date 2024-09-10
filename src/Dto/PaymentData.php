<?php

namespace App\Dto;

use App\Validator\Constraints\CardNumber;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentData
{
    /**
     * @Assert\NotBlank
     * @Assert\GreaterThan(value=0)
     */
    public float $amount;

    /**
     * @Assert\NotBlank
     * @Assert\Choice(choices={"USD", "EUR", "GBP"})
     */
    public string $currency;

    /**
     * @Assert\NotBlank
     * @CardNumber
     * @Assert\Length(min=13, max=19)
     */
    public string $cardNumber;

    /**
     * @Assert\NotBlank
     * @Assert\Range(min=1, max=12)
     */
    public ?int $expMonth;

    /**
     * @Assert\NotBlank
     * @Assert\Range(min=2023)  // Adjust the minimum year dynamically if needed
     */
    public ?int $expYear;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=4)
     */
    public ?string $cvv;
}
