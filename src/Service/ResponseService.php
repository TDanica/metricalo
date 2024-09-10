<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseService
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function createSuccessResponse($data, int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        return new JsonResponse($data, $status);
    }

    public function createErrorResponse(string $message, int $status, \Exception $exception = null): JsonResponse
    {
        $errorResponse = ['error' => $message];
        if ($exception) {
            $errorResponse['details'] = $exception->getMessage();
            $this->logger->error('Error occurred: ' . $exception->getMessage());
        }
        return new JsonResponse($errorResponse, $status);
    }
}
