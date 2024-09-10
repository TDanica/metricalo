<?php

namespace App\Service\PaymentStrategy;

use GuzzleHttp\Client;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PaymentStrategyFactory
{
    private $parameterBag;
    private $strategies;

    public function __construct(ParameterBagInterface $parameterBag, array $strategies)
    {
        $this->parameterBag = $parameterBag;
        $this->strategies = $strategies;
    }

    public function getStrategy(string $provider): PaymentStrategy
    {
        $config = $this->parameterBag->get("payment.$provider");

        if (!$config || !isset($this->strategies[$provider])) {
            throw new InvalidArgumentException("No configuration or strategy found for provider: $provider");
        }

        $client = new Client(['base_uri' => $config['base_uri']]);
        $strategyClass = $this->strategies[$provider];

        return new $strategyClass($client, $config['api_key'], $config['endpoint']);
    }
}

