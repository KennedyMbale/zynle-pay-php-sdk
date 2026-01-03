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
     * @param string $callbackUrl Optional callback URL for payment notifications
     * @param string $successUrl Optional success URL for user redirect after successful payment
     * @param string $failUrl Optional fail URL for user redirect after failed payment
     * @throws ApiException
     */
    public function runPayToBank(
        string $referenceNo,
        float $amount,
        string $description,
        string $bankName,
        string $receiverId,
        ?string $callbackUrl = null,
        ?string $successUrl = null,
        ?string $failUrl = null
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

        if ($callbackUrl !== null) {
            $data['callback_url'] = $callbackUrl;
        }
        if ($successUrl !== null) {
            $data['success_url'] = $successUrl;
        }
        if ($failUrl !== null) {
            $data['fail_url'] = $failUrl;
        }

        return $this->client->request('POST', $data)['response'];
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

        return $this->client->request('POST', $data)['response'];
    }
}
