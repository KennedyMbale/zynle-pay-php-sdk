<?php

declare(strict_types=1);

namespace ZynlePay;

use ZynlePay\Exception\ApiException;

class PaymentStatus
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Check payment transaction status
     *
     * @param string $referenceNo The reference number of the transaction
     * @return array API response containing payment status information
     * @throws ApiException
     */
    public function checkStatus(string $referenceNo): array
    {
        $data = [
            'method' => 'checkPaymentStatus',
            'reference_no' => $referenceNo,
            'request_id' => uniqid('status_', true),
        ];

        return $this->client->request('POST', $data, '/paymentstatus')['response'];
    }
}
