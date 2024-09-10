<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Psr\Log\LoggerInterface;

class RequestDataHandler
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Parses and validates JSON request data.
     */
    public function parseRequestData(Request $request): array
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->warning('Invalid JSON received: ' . json_last_error_msg());
            throw new BadRequestHttpException('Invalid JSON payload: ' . json_last_error_msg());
        }

        return $data;
    }
}
