<?php

declare(strict_types=1);

namespace ZynlePay;

use ZynlePay\Exception\ApiException;

class MomoService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Process a bill payment via MOMO
     *
     * @throws ApiException
     */
    public function runBillPayment(
        string $senderId,
        string $referenceNo,
        float $amount,
        string $description = 'Payment'
    ): array {
        $data = [
            'method' => 'runBillPayment',
            'sender_id' => $senderId,
            'reference_no' => $referenceNo,
            'amount' => $amount,
            'request_id' => uniqid('req_', true),
        ];

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

        return $this->client->request('POST', $data);
    }
}
