<?php

namespace ZynlePay\Tests;

use PHPUnit\Framework\TestCase;
use ZynlePay\Client;
use ZynlePay\EwalletService;
use ZynlePay\Exception\ApiException;
use InvalidArgumentException;

class EwalletServiceTest extends TestCase
{
    private EwalletService $ewalletService;
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client(
            'test_merchant_id',
            'test_api_id',
            'test_api_key',
            'ewallet',
            '1005',
            true
        );
        $this->ewalletService = new EwalletService($this->client);
    }

    public function testRunPayToEwalletSuccess()
    {
        // Mock the client request method
        $expectedResponse = ['status' => 'success', 'transaction_id' => '12345'];

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->with('POST', [
                'method' => 'runPayToEwallet',
                'receiver_id' => 'receiver123',
                'reference_no' => 'REF123',
                'amount' => 100.00
            ])
            ->willReturn($expectedResponse);

        $ewalletService = new EwalletService($clientMock);

        $result = $ewalletService->runPayToEwallet('REF123', 100.00, 'receiver123');

        $this->assertEquals($expectedResponse, $result);
    }

    public function testRunPayToEwalletInvalidReference()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reference number cannot be empty');

        $this->ewalletService->runPayToEwallet('', 100.00, 'receiver123');
    }

    public function testRunPayToEwalletInvalidAmount()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount must be greater than 0');

        $this->ewalletService->runPayToEwallet('REF123', 0, 'receiver123');
    }

    public function testRunPayToEwalletInvalidReceiver()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Receiver ID cannot be empty');

        $this->ewalletService->runPayToEwallet('REF123', 100.00, '');
    }

    public function testCheckEwalletTransferStatus()
    {
        $expectedResponse = ['status' => 'completed'];

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->with('POST', [
                'method' => 'checkEwalletTransferStatus',
                'reference_no' => 'REF123'
            ])
            ->willReturn($expectedResponse);

        $ewalletService = new EwalletService($clientMock);

        $result = $ewalletService->checkEwalletTransferStatus('REF123');

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCheckEwalletTransferStatusInvalidReference()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reference number cannot be empty');

        $this->ewalletService->checkEwalletTransferStatus('');
    }
}
