<?php

declare(strict_types=1);

namespace ZynlePay\Tests;

use PHPUnit\Framework\TestCase;
use ZynlePay\Client;
use ZynlePay\PaymentStatus;
use ZynlePay\Exception\ApiException;
use ZynlePay\Exception\InvalidArgumentException;
use ZynlePay\Validation\PaymentStatusValidator;
use Psr\Log\LoggerInterface;

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

    public function testCheckStatusWithEmptyReferenceThrowsException(): void
    {
        $clientMock = $this->createMock(Client::class);
        $paymentStatus = new PaymentStatus($clientMock);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reference number cannot be empty');

        $paymentStatus->checkStatus('');
    }

    public function testCheckStatusWithInvalidReferenceThrowsException(): void
    {
        $validatorMock = $this->createMock(PaymentStatusValidator::class);
        $validatorMock->method('isValidReferenceNo')->willReturn(false);

        $clientMock = $this->createMock(Client::class);
        $paymentStatus = new PaymentStatus($clientMock, null, $validatorMock);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid reference number format');

        $paymentStatus->checkStatus('invalid@ref');
    }

    public function testCheckStatusWithRetrySuccess(): void
    {
        $expectedResponse = ['status' => 'success', 'transaction_id' => '12345'];

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->willReturn(['response' => $expectedResponse]);

        $paymentStatus = new PaymentStatus($clientMock);

        $result = $paymentStatus->checkStatusWithRetry('REF123', 3, 100);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCheckStatusWithRetryFailure(): void
    {
        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->exactly(3))
            ->method('request')
            ->willThrowException(new ApiException('Network timeout'));

        $paymentStatus = new PaymentStatus($clientMock);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Failed to check payment status after retries');

        $paymentStatus->checkStatusWithRetry('REF123', 3, 100);
    }

    public function testIsSuccessful(): void
    {
        $this->assertTrue(PaymentStatus::isSuccessful(['status' => 'success']));
        $this->assertFalse(PaymentStatus::isSuccessful(['status' => 'failed']));
        $this->assertFalse(PaymentStatus::isSuccessful(['status' => 'pending']));
    }

    public function testIsPending(): void
    {
        $this->assertTrue(PaymentStatus::isPending(['status' => 'pending']));
        $this->assertFalse(PaymentStatus::isPending(['status' => 'success']));
    }

    public function testIsFailed(): void
    {
        $this->assertTrue(PaymentStatus::isFailed(['status' => 'failed']));
        $this->assertFalse(PaymentStatus::isFailed(['status' => 'success']));
    }

    public function testCheckStatusLogsRequestAndResponse(): void
    {
        $expectedResponse = [
            'status' => 'success',
            'transaction_id' => '12345',
            'amount' => 100.0,
            'currency' => 'USD'
        ];

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->willReturn(['response' => $expectedResponse]);

        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('info')
            ->with('Payment status check request', $this->callback(function ($context) {
                return isset($context['reference_no']) && $context['reference_no'] === 'REF123';
            }));
        $loggerMock->expects($this->once())
            ->method('info')
            ->with('Payment status check response', $this->callback(function ($context) {
                return isset($context['reference_no']) && $context['reference_no'] === 'REF123' &&
                    isset($context['success']) && $context['success'] === true;
            }));

        $paymentStatus = new PaymentStatus($clientMock, $loggerMock);

        $result = $paymentStatus->checkStatus('REF123');

        $this->assertEquals($expectedResponse, $result);
    }
}
