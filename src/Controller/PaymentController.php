<?php

namespace App\Controller;

use App\Service\PaymentService;
use App\Service\PaymentDataValidator;
use App\Service\ResponseService;
use App\Service\RequestDataHandler;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PaymentController extends AbstractController
{
    private PaymentService $paymentService;
    private PaymentDataValidator $validator;
    private ResponseService $responseService;
    private RequestDataHandler $requestDataHandler;
    private LoggerInterface $logger;
    private array $validProviders;

    public function __construct(
        PaymentService $paymentService,
        PaymentDataValidator $validator,
        ResponseService $responseService,
        RequestDataHandler $requestDataHandler,
        LoggerInterface $logger,
        ParameterBagInterface $parameterBag
    ) {
        $this->paymentService = $paymentService;
        $this->validator = $validator;
        $this->responseService = $responseService;
        $this->requestDataHandler = $requestDataHandler;
        $this->logger = $logger;
        $this->validProviders = $parameterBag->get('valid_providers');
    }

    /**
     * @Route("/app/example/{provider}", methods={"POST"})
     */
    public function processPayment(Request $request, string $provider): JsonResponse
    {
        if (!in_array($provider, $this->validProviders, true)) {
            return $this->responseService->createErrorResponse('Invalid payment provider.', JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $data = $this->requestDataHandler->parseRequestData($request);
            $this->validator->validate($data);

            $response = $this->paymentService->processPayment($provider, $data);
            return $this->responseService->createSuccessResponse($response);
        } catch (\InvalidArgumentException $e) {
            $this->logger->warning('Validation error: ' . $e->getMessage());
            return $this->responseService->createErrorResponse('Validation failed', JsonResponse::HTTP_BAD_REQUEST, $e);
        } catch (\Exception $e) {
            $this->logger->error('Processing error: ' . $e->getMessage());
            return $this->responseService->createErrorResponse('An unexpected error occurred.', JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }
}
