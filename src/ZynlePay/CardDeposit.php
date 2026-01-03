<?php

namespace ZynlePay;

class CardDeposit
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function runTranAuthCapture(
        string $referenceNo,
        float $amount,
        string $cardNumber,
        string $expiryMonth,
        string $expiryYear,
        string $cvv,
        string $nameOnCard = 'Zynle Test',
        string $description = 'test',
        string $currency = 'ZMW',
        string $firstName = 'Zynle',
        string $lastName = 'Test',
        string $address = 'Lusaka',
        string $email,
        string $phone,
        string $city = 'Lusaka',
        string $state = 'Lusaka',
        string $zip_code = '10101',
        string $country = 'ZMB'
    ): array {
        $data = [
            'method' => 'runTranAuthCapture',
            'nameoncard' => $nameOnCard,
            'reference_no' => $referenceNo,
            'request_id' => uniqid(),
            'transaction_id' => uniqid(),
            'amount' => $amount,
            'description' => $description,
            'currency' => $currency,
            'cardnumber' => str_replace([' ', '-'], '', $cardNumber),
            'expiry_month' => $expiryMonth,
            'expiry_year' => $expiryYear,
            'cvv' => $cvv,
            'currency' => $currency,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'address' => $address,
            'email' => $email,
            'phone' => $phone,
            'city' => $city,
            'state' => $state,
            'zip_code' => $zip_code,
            'country' => $country,
        ];

        return $this->client->request('POST', $data)['response'];
    }
}
