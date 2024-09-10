<?php

namespace App\Tests\Service;

use App\Service\PaymentDataValidator;
use App\Validator\CardNumber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentDataValidatorTest extends TestCase
{
    private PaymentDataValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a mock for the CardNumber constraint
        $cardNumberConstraint = $this->createMock(CardNumber::class);
        $this->validator = new PaymentDataValidator($cardNumberConstraint);
    }

    public function testValidateValidData()
    {
        $data = [
            'amount' => 100,
            'currency' => 'USD',
            'card_number' => '4111111111111111',
            'exp_year' => date('Y') + 1,
            'exp_month' => 12,
            'cvv' => '123',
        ];

        // No exceptions should be thrown
        $this->validator->validate($data);
        $this->addToAssertionCount(1); // This means the test passed
    }

    public function testValidateInvalidData()
    {
        $this->expectException(\InvalidArgumentException::class);

        $data = [
            'amount' => -100, // Invalid amount
            'currency' => 'USD',
            'card_number' => '123', // Invalid card number
            'exp_year' => date('Y') - 1, // Expired year
            'exp_month' => 13, // Invalid month
            'cvv' => '12', // Invalid CVV
        ];

        $this->validator->validate($data);
    }
}
