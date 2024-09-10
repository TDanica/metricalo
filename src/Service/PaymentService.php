<?php

namespace App\Service;

use App\Service\PaymentStrategy\PaymentStrategyFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class PaymentService
{
    private $logger;
    private $strategyFactory;
    private $cache;
    private $validator;

    public function __construct(LoggerInterface $logger, PaymentStrategyFactory $strategyFactory, AdapterInterface $cache, PaymentDataValidator $validator)
    {
        $this->logger = $logger;
        $this->strategyFactory = $strategyFactory;
        $this->cache = $cache;
        $this->validator = $validator;
    }

    public function processPayment(string $provider, array $data): array
    {
        try {
            $this->validator->validate($data);

            $strategy = $this->strategyFactory->getStrategy($provider);
            $cacheKey = $this->getCacheKey($provider, $data);

            $cachedResponse = $this->cache->getItem($cacheKey);
            if ($cachedResponse->isHit()) {
                return $cachedResponse->get();
            }

            $response = $strategy->charge(
                $data['amount'],
                $data['currency'],
                $data['card_number'],
                $data['exp_year'] ?? null,
                $data['exp_month'] ?? null,
                $data['cvv'] ?? null
            );

            $cachedResponse->set($response);
            $this->cache->save($cachedResponse);

            return $response;
        } catch (\InvalidArgumentException $e) {
            $this->logger->warning('Validation failed: ' . $e->getMessage());
            throw $e;
        } catch (\RuntimeException $e) {
            $this->logger->error('Payment processing failed: ' . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error('An unexpected error occurred: ' . $e->getMessage());
            throw new \RuntimeException('Payment processing failed due to an unexpected error.', 0, $e);
        }
    }

    private function getCacheKey(string $provider, array $data): string
    {
        return sprintf(
            'payment_%s_%s_%s_%s_%s_%s_%s',
            $provider,
            md5($data['amount']),
            md5($data['currency']),
            md5($data['card_number']),
            md5($data['exp_year'] ?? 'none'),
            md5($data['exp_month'] ?? 'none'),
            md5($data['cvv'] ?? 'none')
        );
    }
}
