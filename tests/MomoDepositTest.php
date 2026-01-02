<?php

declare(strict_types=1);

namespace ZynlePay\Tests;

use PHPUnit\Framework\TestCase;
use ZynlePay\Client;
use ZynlePay\MomoDeposit;
use ZynlePay\Exception\ApiException;

class MomoDepositTest extends TestCase
{
    private MomoDeposit $momoDeposit;
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client(
            merchantId: 'test_merchant_id',
            apiId: 'test_api_id',
            apiKey: 'test_api_key',
            channel: 'momo',
            serviceId: '1002',
            sandbox: true
        );
        $this->momoDeposit = new MomoDeposit($this->client);
    }

    public function testRunBillPaymentSuccess(): void
    {
        $expectedResponse = ['status' => 'success', 'transaction_id' => '12345'];

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->with('POST', $this->callback(function ($data) {
                return isset($data['method']) && $data['method'] === 'runBillPayment' &&
                    isset($data['sender_id']) && $data['sender_id'] === 'sender123' &&
                    isset($data['reference_no']) && $data['reference_no'] === 'REF123' &&
                    isset($data['amount']) && $data['amount'] === 100.00;
            }))
            ->willReturn($expectedResponse);

        $momoDeposit = new MomoDeposit($clientMock);

        $result = $momoDeposit->runBillPayment('sender123', 'REF123', 100.00);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testRunBillPaymentWithDescription(): void
    {
        $expectedResponse = ['status' => 'success'];

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->with('POST', $this->callback(function ($data) {
                return isset($data['description']) && $data['description'] === 'Custom payment';
            }))
            ->willReturn($expectedResponse);

        $momoDeposit = new MomoDeposit($clientMock);

        $result = $momoDeposit->runBillPayment('sender123', 'REF123', 100.00, 'Custom payment');

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCheckPaymentStatus(): void
    {
        $expectedResponse = ['status' => 'completed'];

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->with('POST', $this->callback(function ($data) {
                return isset($data['method']) && $data['method'] === 'checkPaymentStatus' &&
                    isset($data['reference_no']) && $data['reference_no'] === 'REF123';
            }))
            ->willReturn($expectedResponse);

        $momoDeposit = new MomoDeposit($clientMock);

        $result = $momoDeposit->checkPaymentStatus('REF123');

        $this->assertEquals($expectedResponse, $result);
    }

    public function testApiExceptionHandling(): void
    {
        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->willThrowException(new ApiException('API Error'));

        $momoDeposit = new MomoDeposit($clientMock);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('API Error');

        $momoDeposit->runBillPayment('sender123', 'REF123', 100.00);
    }
}
