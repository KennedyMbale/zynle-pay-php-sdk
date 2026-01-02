<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use ZynlePay\Client;
use ZynlePay\MomoDeposit;
use ZynlePay\CardDeposit;
use ZynlePay\WalletToBank;
use ZynlePay\MomoWithdraw;
use ZynlePay\PaymentStatus;
use ZynlePay\CheckBalance;

class ClientTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client(
            merchantId: 'test_merchant',
            apiId: 'test_api_id',
            apiKey: 'test_api_key',
            channel: 'momo',
            serviceId: '1002'
        );
    }

    public function testClientInitialization(): void
    {
        $this->assertInstanceOf(Client::class, $this->client);
    }

    public function testMomoDepositService(): void
    {
        $service = new MomoDeposit($this->client);
        $this->assertInstanceOf(MomoDeposit::class, $service);
    }

    public function testCardDepositService(): void
    {
        $service = new CardDeposit($this->client);
        $this->assertInstanceOf(CardDeposit::class, $service);
    }

    public function testWalletToBankService(): void
    {
        $service = new WalletToBank($this->client);
        $this->assertInstanceOf(WalletToBank::class, $service);
    }

    public function testMomoWithdrawService(): void
    {
        $service = new MomoWithdraw($this->client);
        $this->assertInstanceOf(MomoWithdraw::class, $service);
    }

    public function testPaymentStatusService(): void
    {
        $service = new PaymentStatus($this->client);
        $this->assertInstanceOf(PaymentStatus::class, $service);
    }

    public function testCheckBalanceService(): void
    {
        $service = new CheckBalance($this->client);
        $this->assertInstanceOf(CheckBalance::class, $service);
    }
}
