<?php

declare(strict_types=1);

namespace ZynlePay;

use ZynlePay\Exception\ApiException;

class MomoDeposit
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Process a bill payment via MOMO
     *
     * @param string $callbackUrl Optional callback URL for payment notifications
     * @param string $successUrl Optional success URL for user redirect after successful payment
     * @param string $failUrl Optional fail URL for user redirect after failed payment
     * @throws ApiException
     */
    public function runBillPayment(
        string $senderId,
        string $referenceNo,
        float $amount,
        string $description = 'Payment',
        ?string $callbackUrl = null,
        ?string $successUrl = null,
        ?string $failUrl = null
    ): array {
        $data = [
            'method' => 'runBillPayment',
            'sender_id' => $senderId,
            'reference_no' => $referenceNo,
            'amount' => $amount,
            'request_id' => uniqid('req_', true),
        ];

        if ($callbackUrl !== null) {
            $data['callback_url'] = $callbackUrl;
        }
        if ($successUrl !== null) {
            $data['success_url'] = $successUrl;
        }
        if ($failUrl !== null) {
            $data['fail_url'] = $failUrl;
        }

        return $this->client->request('POST', $data);
    }

    /**
     * Check payment status
     *
     * @throws ApiException
     */
    public function checkPaymentStatus(string $referenceNo): array
    {
        $data = [
            'method' => 'checkPaymentStatus',
            'reference_no' => $referenceNo,
            'request_id' => uniqid('status_', true),
        ];

        return $this->client->request('POST', $data)['response'];
    }
}
