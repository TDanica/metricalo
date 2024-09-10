<?php

namespace App\Service;

use App\Validator\CardNumber;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class PaymentDataValidator
{
    private $validator;

    public function __construct()
    {
        $this->validator = Validation::createValidator();
    }

    public function validate(array $data): void
    {
        $constraints = new Assert\Collection([
            'amount' => new Assert\GreaterThan(['value' => 0]),
            'currency' => new Assert\Choice(['choices' => ['USD', 'EUR', 'GBP']]),
            'card_number' => [
                new Assert\Length(['min' => 13, 'max' => 19]),
                new CardNumber(), // Use the custom card number constraint here
            ],
            'exp_year' => new Assert\Range(['min' => date('Y')]),
            'exp_month' => new Assert\Range(['min' => 1, 'max' => 12]),
            'cvv' => new Assert\Length(['min' => 3, 'max' => 4]),
        ]);

        $violations = $this->validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $messages = [];
            foreach ($violations as $violation) {
                $messages[] = $violation->getMessage();
            }
            throw new \InvalidArgumentException(implode(', ', $messages));
        }
    }
}
