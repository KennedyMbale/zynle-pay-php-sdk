<?php

declare(strict_types=1);

namespace ZynlePay\Tests;

use PHPUnit\Framework\TestCase;
use ZynlePay\Client;
use ZynlePay\CheckBalance;
use ZynlePay\Exception\ApiException;

class CheckBalanceTest extends TestCase
{
    private CheckBalance $checkBalance;
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client(
            merchantId: 'test_merchant_id',
            apiId: 'test_api_id',
            apiKey: 'test_api_key',
            channel: 'balance',
            serviceId: '1006',
            sandbox: true
        );
        $this->checkBalance = new CheckBalance($this->client);
    }

    public function testCheckBalanceSuccess(): void
    {
        $expectedResponse = [
            'balance' => 1500.00,
            'currency' => 'ZMW',
            'available_balance' => 1450.00
        ];

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);

        $checkBalance = new CheckBalance($clientMock);

        $result = $checkBalance->checkBalance();

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCheckBalanceZero(): void
    {
        $expectedResponse = [
            'balance' => 0.00,
            'currency' => 'ZMW',
            'available_balance' => 0.00
        ];

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);

        $checkBalance = new CheckBalance($clientMock);

        $result = $checkBalance->checkBalance();

        $this->assertEquals($expectedResponse, $result);
    }

    public function testApiExceptionHandling(): void
    {
        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->willThrowException(new ApiException('Balance check failed'));

        $checkBalance = new CheckBalance($clientMock);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Balance check failed');

        $checkBalance->checkBalance();
    }
}
