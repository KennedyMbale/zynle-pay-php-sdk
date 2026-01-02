<?php

declare(strict_types=1);

namespace ZynlePay\Tests;

use PHPUnit\Framework\TestCase;
use ZynlePay\Client;
use ZynlePay\WalletToBank;
use ZynlePay\Exception\ApiException;
use InvalidArgumentException;

class WalletToBankTest extends TestCase
{
    private WalletToBank $walletToBank;
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client(
            merchantId: 'test_merchant_id',
            apiId: 'test_api_id',
            apiKey: 'test_api_key',
            channel: 'bank',
            serviceId: '1004',
            sandbox: true
        );
        $this->walletToBank = new WalletToBank($this->client);
    }

    public function testRunPayToBankSuccess(): void
    {
        $expectedResponse = ['status' => 'success', 'transaction_id' => '12345'];

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);

        $walletToBank = new WalletToBank($clientMock);

        $result = $walletToBank->runPayToBank(
            'REF123',
            100.00,
            'Bank transfer',
            'Test Bank',
            'receiver123'
        );

        $this->assertEquals($expectedResponse, $result);
    }

    public function testRunPayToBankWithDefaults(): void
    {
        $expectedResponse = ['status' => 'success'];

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);

        $walletToBank = new WalletToBank($clientMock);

        $result = $walletToBank->runPayToBank('REF123', 100.00);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testRunPayToBankInvalidAmount(): void
    {
        $clientMock = $this->createMock(Client::class);
        $walletToBank = new WalletToBank($clientMock);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount must be greater than 0');

        $walletToBank->runPayToBank('REF123', 0);
    }

    public function testCheckBankTransferStatus(): void
    {
        $expectedResponse = ['status' => 'completed'];

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);

        $walletToBank = new WalletToBank($clientMock);

        $result = $walletToBank->checkBankTransferStatus('REF123');

        $this->assertEquals($expectedResponse, $result);
    }

    public function testApiExceptionHandling(): void
    {
        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->willThrowException(new ApiException('Bank transfer failed'));

        $walletToBank = new WalletToBank($clientMock);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Bank transfer failed');

        $walletToBank->runPayToBank('REF123', 100.00);
    }
}
