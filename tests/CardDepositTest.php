<?php

declare(strict_types=1);

namespace ZynlePay\Tests;

use PHPUnit\Framework\TestCase;
use ZynlePay\Client;
use ZynlePay\CardDeposit;
use ZynlePay\Exception\ApiException;

class CardDepositTest extends TestCase
{
    private CardDeposit $cardDeposit;
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client(
            merchantId: 'test_merchant_id',
            apiId: 'test_api_id',
            apiKey: 'test_api_key',
            channel: 'card',
            serviceId: '1003',
            sandbox: true
        );
        $this->cardDeposit = new CardDeposit($this->client);
    }

    public function testRunTranAuthCaptureSuccess(): void
    {
        $expectedResponse = ['status' => 'success', 'transaction_id' => '12345'];

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);

        $cardDeposit = new CardDeposit($clientMock);

        $result = $cardDeposit->runTranAuthCapture(
            $referenceNo = "ORD-2024-001234",  // Unique transaction reference
            $amount = 1500.75,                 // Amount in currency units
            $cardNumber = "4111111111111111", // Test Visa card number
            $expiryMonth = "12",               // Month (01-12)
            $expiryYear = "2025",              // Year (YYYY)
            $cvv = "123",                      // 3-digit CVV for Visa/MC
            $nameOnCard = "JOHN DOE",          // Name as on card
            $description = "Laptop Purchase",  // Transaction description
            $currency = 'ZMW',                 // Zambian Kwacha (default)
            $firstName = "John",
            $lastName = "Doe",
            $address = "123 Main Street, Kabulonga",
            $email = "john.doe@example.com",
            $phone = "+260971234567",          // Zambian mobile format
            $city = "Lusaka",
            $state = "Lusaka Province",
            $zip_code = "10101",               // Default Zambian postal code
            $country = "Zambia"               // Default country
        );

        $this->assertEquals($expectedResponse, $result);
    }

    public function testRunTranAuthCaptureWithOptionalParams(): void
    {
        $expectedResponse = ['status' => 'success'];

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);

        $cardDeposit = new CardDeposit($clientMock);

        $result = $cardDeposit->runTranAuthCapture(
            $referenceNo = "ORD-2024-001234",  // Unique transaction reference
            $amount = 1500.75,                 // Amount in currency units
            $cardNumber = "4111111111111111", // Test Visa card number
            $expiryMonth = "12",               // Month (01-12)
            $expiryYear = "2025",              // Year (YYYY)
            $cvv = "123",                      // 3-digit CVV for Visa/MC
            $nameOnCard = "JOHN DOE",          // Name as on card
            $description = "Laptop Purchase",  // Transaction description
            $currency = 'ZMW',                 // Zambian Kwacha (default)
            $firstName = "John",
            $lastName = "Doe",
            $address = "123 Main Street, Kabulonga",
            $email = "john.doe@example.com",
            $phone = "+260971234567",          // Zambian mobile format
            $city = "Lusaka",
            $state = "Lusaka Province",
            $zip_code = "10101",               // Default Zambian postal code
            $country = "Zambia"               // Default country
        );

        $this->assertEquals($expectedResponse, $result);
    }

    public function testApiExceptionHandling(): void
    {
        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->willThrowException(new ApiException('Card payment failed'));

        $cardDeposit = new CardDeposit($clientMock);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Card payment failed');

        $cardDeposit->runTranAuthCapture(
            $referenceNo = "ORD-2024-001234",  // Unique transaction reference
            $amount = 1500.75,                 // Amount in currency units
            $cardNumber = "4111111111111111", // Test Visa card number
            $expiryMonth = "12",               // Month (01-12)
            $expiryYear = "2025",              // Year (YYYY)
            $cvv = "123",                      // 3-digit CVV for Visa/MC
            $nameOnCard = "JOHN DOE",          // Name as on card
            $description = "Laptop Purchase",  // Transaction description
            $currency = 'ZMW',                 // Zambian Kwacha (default)
            $firstName = "John",
            $lastName = "Doe",
            $address = "123 Main Street, Kabulonga",
            $email = "john.doe@example.com",
            $phone = "+260971234567",          // Zambian mobile format
            $city = "Lusaka",
            $state = "Lusaka Province",
            $zip_code = "10101",               // Default Zambian postal code
            $country = "Zambia"               // Default country
        );
    }
}
