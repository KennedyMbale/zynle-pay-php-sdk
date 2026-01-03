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
        string $nameOnCard,
        string $description,
        string $currency = 'ZMW',
        string $firstName,
        string $lastName,
        string $address,
        string $email,
        string $phone,
        string $city,
        string $state,
        string $zip_code = '10101',
        string $country = 'Zambia',
        ?string $callbackUrl = null,
        ?string $successUrl = null,
        ?string $failUrl = null
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
}
