# ZynlePay PHP SDK

A comprehensive PHP 8+ SDK for integrating with the ZynlePay payment gateway API. Supports both production and sandbox environments with full type safety and comprehensive error handling.

## Features

- ✅ **PHP 8.0+** with strict typing
- ✅ **Production & Sandbox** environment support
- ✅ **Type-safe API** with detailed PHPDoc
- ✅ **Comprehensive error handling** with custom exceptions
- ✅ **Input validation** for all methods
- ✅ **Composer-ready** package structure
- ✅ **Full test coverage** with PHPUnit

## Installation

Install via Composer:

```bash
composer require kennedymbale/zynle-pay-php-sdk
```

## Quick Start

```php
use ZynlePay\Client;
use ZynlePay\MomoDeposit;

// Initialize client for sandbox
$client = new Client(
    merchantId: 'your_merchant_id',
    apiId: 'your_api_id',
    apiKey: 'your_api_key',
    channel: 'momo',
    serviceId: '1002'
);

// Create service instance
$momoDeposit = new MomoDeposit($client);

// Process a payment
try {
    $result = $momoDeposit->runBillPayment(
        senderId: '256700000000',
        referenceNo: uniqid('ref_', true),
        amount: 1000.00,
        description: 'Payment for services'
    );

    echo "Payment initiated: " . $result['transaction_id'];
} catch (ZynlePay\Exception\ApiException $e) {
    echo "Payment failed: " . $e->getMessage();
}
```

## Configuration

### Client Class

```php
$client = new Client(
    string $merchantId,      // Your ZynlePay merchant ID
    string $apiId,          // Your API ID
    string $apiKey,         // Your API key
    string $channel,        // Payment channel (momo, card, bank, etc.)
    string $serviceId = '1002',  // Service ID (default: '1002')
    bool $sandbox = true     // true for sandbox, false for production
);
```

## Services

### MomoDeposit - MOMO Mobile Money Payments

Process MOMO mobile money payments and check payment status.

```php
use ZynlePay\MomoDeposit;

$momoDeposit = new MomoDeposit($client);

// Process payment
$result = $momoDeposit->runBillPayment(
    senderId: '256700000000',               // Sender's phone number
    referenceNo: uniqid('ref_', true),     // Unique reference number
    amount: 1000.00,                        // Amount in float
    description: 'Payment'                   // Optional description
);

// Check payment status
$status = $momoDeposit->checkPaymentStatus('REF123456');
```

### CardDeposit - Credit/Debit Card Processing

Handle credit and debit card payments with full PCI compliance.

```php
use ZynlePay\MomoDeposit;
$momoService = new MomoDeposit($client);

try {
    $result = $momoService->runBillPayment(
        senderId: '09XXXXXXXX',
        referenceNo: uniqid('ref_', true),
        amount: 70,
        description: 'Payment for services'
    );

    echo "<h3>".$result['response_description']."</h3>";
    echo "<p>Transaction ID: " . $result['transaction_id'] . "</p>";
    echo "<p>Operator: " . $result['operator'] . "</p>";
    echo "<p>transaction_date: " . $result['transaction_date'] . "</p>";

} catch (ZynlePay\Exception\ApiException $e) {
    echo "Payment failed: " . $e->getMessage();
}
```

### WalletToBank - Bank Transfers from Wallet

Transfer funds from your wallet to bank accounts.

```php
use ZynlePay\WalletToBank;

$walletToBank = new WalletToBank($client);

// Transfer to bank
$result = $walletToBank->runPayToBank(
    referenceNo: uniqid('ref_', true),
    amount: 500.00,
    description: 'Bank transfer',
    bankName: 'Bank of Example',
    receiverId: '1234567890'
);

// Check transfer status
$status = $walletToBank->checkBankTransferStatus('REF123456');
```

### MomoWithdraw - E-wallet Withdrawals

Withdraw funds to MOMO mobile money accounts.

```php
use ZynlePay\MomoWithdraw;

$momoWithdraw = new MomoWithdraw($client);

// Withdraw to MOMO
$result = $momoWithdraw->runPayToEwallet(
    referenceNo: 'REF123456',
    amount: 200.00,
    receiverId: '256700000000'
);

// Check withdrawal status
$status = $momoWithdraw->checkEwalletTransferStatus('REF123456');
```

### PaymentStatus - Payment Status Checking

Check the status of any payment transaction.

```php
use ZynlePay\PaymentStatus;

$paymentStatus = new PaymentStatus($client);

// Check payment status
try {
    $status = $paymentStatus->checkStatus('REF123456');
    echo "Payment status: " . $status['status'];
} catch (ZynlePay\Exception\ApiException $e) {
    echo "Status check failed: " . $e->getMessage();
}
```

### CheckBalance - Account Balance Inquiry

Check your account balance and available funds.

```php
use ZynlePay\CheckBalance;

$checkBalance = new CheckBalance($client);

// Check account balance
try {
    $balance = $checkBalance->checkBalance();
    echo "Current balance: " . $balance['balance'] . " " . $balance['currency'];
} catch (ZynlePay\Exception\ApiException $e) {
    echo "Balance check failed: " . $e->getMessage();
}
```

### WebhookHandler - Webhook Processing

Handle payment confirmation webhooks from ZynlePay.

```php
use ZynlePay\WebhookHandler;

$webhookHandler = new WebhookHandler();

// Process webhook data
try {
    $result = $webhookHandler->handle($_POST);
    echo "Webhook processed: " . $result['status'];
} catch (ZynlePay\Exception\ApiException $e) {
    echo "Webhook processing failed: " . $e->getMessage();
}
```

## Error Handling

All service methods throw `ZynlePay\Exception\ApiException` for API-related errors:

```php
try {
    $result = $momoDeposit->runBillPayment('256700000000', 'REF123', 100.00);
} catch (ZynlePay\Exception\ApiException $e) {
    // Handle API errors (invalid credentials, network issues, etc.)
    error_log("API Error: " . $e->getMessage());
    error_log("Error Code: " . $e->getCode());
} catch (InvalidArgumentException $e) {
    // Handle validation errors (invalid amount, etc.)
    error_log("Validation Error: " . $e->getMessage());
}
```

## API Reference

### Client Class

- `__construct(string $merchantId, string $apiId, string $apiKey, string $channel, string $serviceId = '1002', ?bool $sandbox = null)`

### MomoDeposit Methods

- `runBillPayment(string $senderId, string $referenceNo, float $amount, string $description = 'Payment'): array`
- `checkPaymentStatus(string $referenceNo): array`

### CardDeposit Methods

- `runTranAuthCapture(string $referenceNo, float $amount, string $cardNumber, string $expiryMonth, string $expiryYear, string $cvv, ...$optional): array`

### WalletToBank Methods

- `runPayToBank(string $referenceNo, float $amount, string $description = 'Bank Transfer', string $bankName = '', string $receiverId = ''): array`
- `checkBankTransferStatus(string $referenceNo): array`

### MomoWithdraw Methods

- `runPayToEwallet(string $referenceNo, float $amount, string $receiverId): array`
- `checkEwalletTransferStatus(string $referenceNo): array`

### PaymentStatus Methods

- `checkPaymentStatus(string $referenceNo): array`

### CheckBalance Methods

- `checkBalance(): array`

### WebhookHandler Methods

- `handle(array $webhookData): array`

## Testing

Run the test suite with PHPUnit:

```bash
composer test
```

Run specific test files:

```bash
./vendor/bin/phpunit tests/MomoDepositTest.php
```

## Requirements

- **PHP**: 8.0 or higher
- **Extensions**: curl, json
- **Composer**: For dependency management
- **PHPUnit**: For testing (development only)

## Support

For issues and questions:

- Check the [ZynlePay API Documentation](https://sandbox.zynlepay.com/api/docs)
- Review the test files for usage examples
- Ensure your credentials and configuration are correct

## License

This SDK is released under the MIT License. See LICENSE file for details.
