<?php

namespace App\Service\PaymentStrategy;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AciPaymentStrategy implements PaymentStrategy
{
    private $client;
    private $apiKey;
    private $endpoint;

    public function __construct(Client $client, string $apiKey, string $endpoint)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->endpoint = $endpoint;
    }

    public function charge(float $amount, string $currency, string $cardNumber, ?int $expYear = null, ?int $expMonth = null, ?string $cvv = null): array
    {
        try {
            $response = $this->client->post($this->endpoint, [
                'json' => [
                    'amount' => $amount,
                    'currency' => $currency,
                    'card_number' => $cardNumber,
                    'exp_year' => $expYear,
                    'exp_month' => $expMonth,
                    'cvv' => $cvv,
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'transactionId' => $data['transaction_id'],
                'date' => $data['created_at'],
                'amount' => $amount,
                'currency' => $currency,
            ];
        } catch (RequestException $e) {
            throw new \RuntimeException('ACI payment failed: ' . $e->getMessage(), 0, $e);
        }
    }
}
