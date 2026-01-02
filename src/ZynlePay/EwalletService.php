<?php

namespace ZynlePay;

use InvalidArgumentException;

class EwalletService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Process an e-wallet transfer
     *
     * @param string $referenceNo Unique reference number for the transaction
     * @param float $amount Transaction amount
     * @param string $receiverId Receiver's e-wallet ID
     * @return array API response
     * @throws InvalidArgumentException
     * @throws Exception\ApiException
     */
    public function runPayToEwallet(
        string $referenceNo,
        float $amount,
        string $receiverId
    ): array {
        // Validate inputs
        if (empty($referenceNo)) {
            throw new InvalidArgumentException('Reference number cannot be empty');
        }

        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be greater than 0');
        }

        if (empty($receiverId)) {
            throw new InvalidArgumentException('Receiver ID cannot be empty');
        }

        $data = [
            'method' => 'runPayToEwallet',
            'receiver_id' => $receiverId,
            'reference_no' => $referenceNo,
            'amount' => $amount
        ];

        return $this->client->request('POST', $data);
    }

    /**
     * Check e-wallet transfer status
     *
     * @param string $referenceNo Reference number to check
     * @return array API response
     * @throws InvalidArgumentException
     * @throws Exception\ApiException
     */
    public function checkEwalletTransferStatus(string $referenceNo): array
    {
        if (empty($referenceNo)) {
            throw new InvalidArgumentException('Reference number cannot be empty');
        }

        $data = [
            'method' => 'checkEwalletTransferStatus',
            'reference_no' => $referenceNo
        ];

        return $this->client->request('POST', $data);
    }
}
