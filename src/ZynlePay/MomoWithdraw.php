<?php

declare(strict_types=1);

namespace ZynlePay;

use InvalidArgumentException;
use ZynlePay\Exception\ApiException;

class MomoWithdraw
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Process an e-wallet transfer (withdraw to MOMO)
     *
     * @throws ApiException
     * @throws InvalidArgumentException
     */
    public function runPayToEwallet(
        string $referenceNo,
        float $amount,
        string $receiverId
    ): array {
        // Validate amount
        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be greater than 0');
        }

        $data = [
            'method' => 'runPayToEwallet',
            'reference_no' => $referenceNo,
            'amount' => $amount,
            'receiver_id' => $receiverId,
            'request_id' => uniqid('ewallet_', true),
        ];

        return $this->client->request('POST', $data);
    }

    /**
     * Check e-wallet transfer status
     *
     * @throws ApiException
     */
    public function checkEwalletTransferStatus(string $referenceNo): array
    {
        $data = [
            'method' => 'checkEwalletTransferStatus',
            'reference_no' => $referenceNo,
            'request_id' => uniqid('ewallet_status_', true),
        ];

        return $this->client->request('POST', $data);
    }
}
