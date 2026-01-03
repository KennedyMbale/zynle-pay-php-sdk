<?php

namespace ZynlePay;

use ZynlePay\Exception\ApiException;

class CheckBalance
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Check account balance
     *
     * @return array API response containing balance information
     * @throws Exception\ApiException
     */
    public function checkBalance(): array
    {
        $data = [
            'method' => 'checkBalance'
        ];

        return $this->client->request('POST', $data)['response'];
    }
}
