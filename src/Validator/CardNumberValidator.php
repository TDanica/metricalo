<?php

// src/Validator/CardNumberValidator.php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CardNumberValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$this->isValidCardNumber($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }

    private function isValidCardNumber(string $cardNumber): bool
    {
        // Implement Luhn's algorithm for validation
        $cardNumber = preg_replace('/\D/', '', $cardNumber);
        $sum = 0;
        $length = strlen($cardNumber);
        $parity = $length % 2;

        for ($i = 0; $i < $length; $i++) {
            $digit = (int) $cardNumber[$i];
            if ($i % 2 == $parity) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }

        return $sum % 10 === 0;
    }
}
