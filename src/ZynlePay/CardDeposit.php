<?php

namespace ZynlePay;

class CardService
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
        string $email = 'support@zynle.com',
        string $phone = '0955000679',
        string $city = 'Lusaka',
        string $state = 'Lusaka',
        string $country = 'ZM'
    ): array {
        $data = [
            'method' => 'runTranAuthCapture',
            'nameoncard' => $nameOnCard,
            'reference_no' => $referenceNo,
            'request_id' => uniqid(),
            'transaction_id' => uniqid(),
            'amount' => $amount,
            'description' => $description,
            'cardnumber' => str_replace([' ', '-'], '', $cardNumber),
            'expiry_month' => $expiryMonth,
            'expiry_year' => $expiryYear,
            'cvv' => $cvv,
            'cur' => $currency,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'address' => $address,
            'email' => $email,
            'phone' => $phone,
            'city' => $city,
            'state' => $state,
            'country' => $country,
        ];

        return $this->client->request('POST', $data);
    }
}
