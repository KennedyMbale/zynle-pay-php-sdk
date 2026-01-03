<?php

declare(strict_types=1);

namespace ZynlePay;

use ZynlePay\Exception\ApiException;
use ZynlePay\Exception\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use ZynlePay\Validation\PaymentStatusValidator;

class PaymentStatus
{
    private Client $client;
    private ?LoggerInterface $logger;
    private ?PaymentStatusValidator $validator;

    public function __construct(
        Client $client,
        ?LoggerInterface $logger = null,
        ?PaymentStatusValidator $validator = null
    ) {
        $this->client = $client;
        $this->logger = $logger;
        $this->validator = $validator ?? new PaymentStatusValidator();
    }

    /**
     * Check payment transaction status
     *
     * @param string $referenceNo The reference number of the transaction
     * @return array API response containing payment status information
     * @throws ApiException
     * @throws InvalidArgumentException
     */
    public function checkStatus(string $referenceNo): array
    {
        // Validate input
        if (empty($referenceNo)) {
            throw new InvalidArgumentException('Reference number cannot be empty');
        }

        if (!$this->validator->isValidReferenceNo($referenceNo)) {
            throw new InvalidArgumentException('Invalid reference number format');
        }

        // Prepare request data
        $requestId = $this->generateRequestId();
        $data = [
            'method' => 'checkPaymentStatus',
            'reference_no' => $referenceNo,
            'request_id' => $requestId,
            'timestamp' => time(),
        ];

        // Log request
        $this->logRequest($data);

        try {
            $response = $this->client->request('POST', $data, '/paymentstatus');

            // Validate response structure
            if (!isset($response['response'])) {
                throw new ApiException('Invalid API response structure');
            }

            $responseData = $response['response'];

            // Log successful response
            $this->logResponse($referenceNo, $requestId, $responseData, true);

            // Optionally validate response data
            $this->validateResponse($responseData);

            return $responseData;
        } catch (ApiException $e) {
            // Log error
            $this->logResponse($referenceNo, $requestId, ['error' => $e->getMessage()], false);

            // Re-throw the exception for upstream handling
            throw $e;
        }
    }

    /**
     * Check payment status with retry mechanism
     *
     * @param string $referenceNo
     * @param int $maxRetries Maximum number of retry attempts
     * @param int $retryDelay Delay between retries in milliseconds
     * @return array
     * @throws ApiException
     */
    public function checkStatusWithRetry(
        string $referenceNo,
        int $maxRetries = 3,
        int $retryDelay = 1000
    ): array {
        $attempt = 1;
        $lastException = null;

        while ($attempt <= $maxRetries) {
            try {
                return $this->checkStatus($referenceNo);
            } catch (ApiException $e) {
                $lastException = $e;

                // Log retry attempt
                $this->logger?->warning(
                    sprintf(
                        'Payment status check attempt %d failed for reference %s: %s',
                        $attempt,
                        $referenceNo,
                        $e->getMessage()
                    )
                );

                // Check if we should retry (e.g., network errors, timeouts)
                if (!$this->shouldRetry($e) || $attempt === $maxRetries) {
                    break;
                }

                // Wait before retrying
                usleep($retryDelay * 1000);
                $attempt++;
            }
        }

        throw $lastException ?? new ApiException('Failed to check payment status after retries');
    }

    /**
     * Generate a unique request ID
     */
    private function generateRequestId(): string
    {
        return uniqid('status_', true) . '_' . bin2hex(random_bytes(4));
    }

    /**
     * Log the request
     */
    private function logRequest(array $data): void
    {
        $this->logger?->info('Payment status check request', [
            'reference_no' => $data['reference_no'],
            'request_id' => $data['request_id'],
            'timestamp' => $data['timestamp'],
        ]);
    }

    /**
     * Log the response
     */
    private function logResponse(
        string $referenceNo,
        string $requestId,
        array $response,
        bool $success
    ): void {
        $context = [
            'reference_no' => $referenceNo,
            'request_id' => $requestId,
            'success' => $success,
        ];

        if ($success) {
            $context['status'] = $response['status'] ?? 'unknown';
            $this->logger?->info('Payment status check response', $context);
        } else {
            $context['error'] = $response['error'] ?? 'Unknown error';
            $this->logger?->error('Payment status check failed', $context);
        }
    }

    /**
     * Validate API response
     */
    private function validateResponse(array $response): void
    {
        if (!isset($response['status'])) {
            throw new ApiException('Missing status in API response');
        }

        // Add additional validation as needed based on API documentation
        $requiredFields = ['transaction_id', 'amount', 'currency'];
        foreach ($requiredFields as $field) {
            if (!isset($response[$field])) {
                throw new ApiException("Missing required field in response: {$field}");
            }
        }
    }

    /**
     * Determine if a request should be retried based on the exception
     */
    private function shouldRetry(ApiException $exception): bool
    {
        $message = $exception->getMessage();

        // Retry on network-related errors
        $retryableErrors = [
            'timeout',
            'connection',
            'network',
            'temporarily',
            'retry',
            '503',
            '504',
        ];

        foreach ($retryableErrors as $error) {
            if (stripos($message, $error) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get cached status (if implementing caching)
     */
    public function getCachedStatus(string $referenceNo, int $ttl = 60): ?array
    {
        // Implement caching logic if needed
        // This could use Redis, Memcached, or other cache mechanisms
        return null;
    }

    /**
     * Check if status indicates successful payment
     */
    public static function isSuccessful(array $statusResponse): bool
    {
        return isset($statusResponse['status']) &&
            strtolower($statusResponse['status']) === 'success';
    }

    /**
     * Check if status indicates pending payment
     */
    public static function isPending(array $statusResponse): bool
    {
        return isset($statusResponse['status']) &&
            strtolower($statusResponse['status']) === 'pending';
    }

    /**
     * Check if status indicates failed payment
     */
    public static function isFailed(array $statusResponse): bool
    {
        return isset($statusResponse['status']) &&
            strtolower($statusResponse['status']) === 'failed';
    }
}
