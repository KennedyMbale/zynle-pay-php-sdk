<?php

declare(strict_types=1);

namespace ZynlePay;

use ZynlePay\Exception\ApiException;

class WalletToBank
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Process a bank transfer payment
     *
     * @throws ApiException
     */
    public function runPayToBank(
        string $referenceNo,
        float $amount,
        string $description,
        string $bankName,
        string $receiverId
    ): array {
        $data = [
            'method' => 'runPayToBank',
            'reference_no' => $referenceNo,
            'amount' => $amount,
            'description' => $description,
            'bank_name' => $bankName,
            'receiver_id' => $receiverId,
            'request_id' => uniqid('bank_', true),
        ];

        return $this->client->request('POST', $data);
    }

    /**
     * Check bank transfer status
     *
     * @throws ApiException
     */
    public function checkBankTransferStatus(string $referenceNo): array
    {
        $data = [
            'method' => 'checkBankTransferStatus',
            'reference_no' => $referenceNo,
            'request_id' => uniqid('bank_status_', true),
        ];

        return $this->client->request('POST', $data);
    }
}
