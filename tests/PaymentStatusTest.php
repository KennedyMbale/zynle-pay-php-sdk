<?php

declare(strict_types=1);

namespace ZynlePay\Tests;

use PHPUnit\Framework\TestCase;
use ZynlePay\Client;
use ZynlePay\PaymentStatus;
use ZynlePay\Exception\ApiException;

class PaymentStatusTest extends TestCase
{
    private PaymentStatus $paymentStatus;
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client(
            merchantId: 'test_merchant_id',
            apiId: 'test_api_id',
            apiKey: 'test_api_key',
            channel: 'general',
            serviceId: '1002',
            sandbox: true
        );
        $this->paymentStatus = new PaymentStatus($this->client);
    }

    public function testCheckStatusSuccess(): void
    {
        $expectedResponse = ['status' => 'completed', 'transaction_id' => '12345'];

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->with('POST', $this->callback(function ($data) {
                return isset($data['method']) && $data['method'] === 'checkPaymentStatus' &&
                    isset($data['reference_no']) && $data['reference_no'] === 'REF123';
            }))
            ->willReturn($expectedResponse);

        $paymentStatus = new PaymentStatus($clientMock);

        $result = $paymentStatus->checkStatus('REF123');

        $this->assertEquals($expectedResponse, $result);
    }

    public function testApiExceptionHandling(): void
    {
        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->willThrowException(new ApiException('Payment status check failed'));

        $paymentStatus = new PaymentStatus($clientMock);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Payment status check failed');

        $paymentStatus->checkStatus('REF123');
    }
}
