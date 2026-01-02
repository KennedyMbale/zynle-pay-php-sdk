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
            'REF123',
            100.00,
            '4111111111111111',
            '12',
            '2025',
            '123'
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
            'REF123',
            100.00,
            '4111111111111111',
            '12',
            '2025',
            '123',
            'John Doe',
            'john@example.com',
            '123 Main St'
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
            'REF123',
            100.00,
            '4111111111111111',
            '12',
            '2025',
            '123'
        );
    }
}
