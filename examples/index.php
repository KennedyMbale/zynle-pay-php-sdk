<?php

/**
 * ZynlePay SDK Examples
 * Real-world examples with UI for testing the SDK
 */

require_once __DIR__ . '/../vendor/autoload.php';

use ZynlePay\Client;
use ZynlePay\MomoDeposit;
use ZynlePay\CardDeposit;
use ZynlePay\PaymentStatus;
use ZynlePay\CheckBalance;

// Initialize client (using sandbox by default)
$client = new Client(
    merchantId: 'your_merchant_id',
    apiId: 'your_api_id',
    apiKey: 'your_api_key',
    channel: 'momo',
    serviceId: '1002',
    sandbox: true
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZynlePay SDK Examples</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .example-section {
            margin-bottom: 30px;
            border-left: 4px solid #007bff;
            padding-left: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input,
        select,
        button {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px;
            margin-top: 10px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .result {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid #28a745;
        }

        .error {
            border-left-color: #dc3545;
            background-color: #f8d7da;
        }

        .tabs {
            display: flex;
            margin-bottom: 20px;
        }

        .tab-button {
            padding: 10px 20px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            cursor: pointer;
            margin-right: 5px;
        }

        .tab-button.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
    </style>
</head>

<body>
    <h1>ZynlePay SDK Examples</h1>
    <p>Real-world examples demonstrating how to use the ZynlePay PHP SDK with user interfaces.</p>

    <div class="tabs">
        <button class="tab-button active" onclick="showTab('momo')">MOMO Payment</button>
        <button class="tab-button" onclick="showTab('card')">Card Payment</button>
        <button class="tab-button" onclick="showTab('status')">Check Status</button>
        <button class="tab-button" onclick="showTab('balance')">Check Balance</button>
    </div>

    <!-- MOMO Payment Tab -->
    <div id="momo" class="tab-content active">
        <div class="container">
            <h2>MOMO Mobile Money Payment</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="momo_payment">
                <div class="form-group">
                    <label for="sender_id">Sender Phone Number:</label>
                    <input type="text" id="sender_id" name="sender_id" placeholder="256700000000" required>
                </div>
                <div class="form-group">
                    <label for="reference_no">Reference Number:</label>
                    <input type="text" id="reference_no" name="reference_no" value="<?php echo 'REF' . time(); ?>" required>
                </div>
                <div class="form-group">
                    <label for="amount">Amount (ZMW):</label>
                    <input type="number" id="amount" name="amount" step="0.01" placeholder="100.00" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <input type="text" id="description" name="description" placeholder="Payment for services">
                </div>
                <button type="submit">Process MOMO Payment</button>
            </form>
        </div>
    </div>

    <!-- Card Payment Tab -->
    <div id="card" class="tab-content">
        <div class="container">
            <h2>Credit/Debit Card Payment</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="card_payment">
                <div class="grid">
                    <div class="form-group">
                        <label for="card_number">Card Number:</label>
                        <input type="text" id="card_number" name="card_number" placeholder="4111111111111111" required>
                    </div>
                    <div class="form-group">
                        <label for="expiry_month">Expiry Month:</label>
                        <input type="text" id="expiry_month" name="expiry_month" placeholder="12" maxlength="2" required>
                    </div>
                    <div class="form-group">
                        <label for="expiry_year">Expiry Year:</label>
                        <input type="text" id="expiry_year" name="expiry_year" placeholder="2025" maxlength="4" required>
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV:</label>
                        <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="4" required>
                    </div>
                </div>
                <div class="grid">
                    <div class="form-group">
                        <label for="card_reference">Reference Number:</label>
                        <input type="text" id="card_reference" name="reference_no" value="<?php echo 'CARD' . time(); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="card_amount">Amount (ZMW):</label>
                        <input type="number" id="card_amount" name="amount" step="0.01" placeholder="100.00" required>
                    </div>
                    <div class="form-group">
                        <label for="card_name">Name on Card:</label>
                        <input type="text" id="card_name" name="name_on_card" placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" placeholder="john@example.com" required>
                    </div>
                </div>
                <button type="submit">Process Card Payment</button>
            </form>
        </div>
    </div>

    <!-- Check Status Tab -->
    <div id="status" class="tab-content">
        <div class="container">
            <h2>Check Payment Status</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="check_status">
                <div class="form-group">
                    <label for="status_reference">Reference Number:</label>
                    <input type="text" id="status_reference" name="reference_no" placeholder="REF1234567890" required>
                </div>
                <button type="submit">Check Status</button>
            </form>
        </div>
    </div>

    <!-- Check Balance Tab -->
    <div id="balance" class="tab-content">
        <div class="container">
            <h2>Check Account Balance</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="check_balance">
                <p>Click the button below to check your account balance.</p>
                <button type="submit">Check Balance</button>
            </form>
        </div>
    </div>

    <?php
    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $action = $_POST['action'] ?? '';

            switch ($action) {
                case 'momo_payment':
                    $momoDeposit = new MomoDeposit($client);
                    $result = $momoDeposit->runBillPayment(
                        senderId: $_POST['sender_id'],
                        referenceNo: $_POST['reference_no'],
                        amount: (float)$_POST['amount'],
                        description: $_POST['description'] ?: 'Payment'
                    );
                    displayResult('MOMO Payment Result', $result);
                    break;

                case 'card_payment':
                    $cardDeposit = new CardDeposit($client);
                    $result = $cardDeposit->runTranAuthCapture(
                        referenceNo: $_POST['reference_no'],
                        amount: (float)$_POST['amount'],
                        cardNumber: $_POST['card_number'],
                        expiryMonth: $_POST['expiry_month'],
                        expiryYear: $_POST['expiry_year'],
                        cvv: $_POST['cvv'],
                        nameOnCard: $_POST['name_on_card'] ?: 'Test User',
                        description: 'Card payment',
                        currency: 'ZMW',
                        firstName: 'Test',
                        lastName: 'User',
                        address: 'Lusaka',
                        phone: '260970000000',
                        email: $_POST['email']
                    );
                    displayResult('Card Payment Result', $result);
                    break;

                case 'check_status':
                    $paymentStatus = new PaymentStatus($client);
                    $result = $paymentStatus->checkStatus($_POST['reference_no']);
                    displayResult('Payment Status', $result);
                    break;

                case 'check_balance':
                    $checkBalance = new CheckBalance($client);
                    $result = $checkBalance->checkBalance();
                    displayResult('Account Balance', $result);
                    break;
            }
        } catch (Exception $e) {
            displayError('Error', $e->getMessage());
        }
    }

    function displayResult($title, $data)
    {
        echo "<div class='container'>";
        echo "<h3>$title</h3>";
        echo "<div class='result'>";
        echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        echo "</div>";
        echo "</div>";
    }

    function displayError($title, $message)
    {
        echo "<div class='container'>";
        echo "<h3>$title</h3>";
        echo "<div class='result error'>";
        echo "<strong>Error:</strong> $message";
        echo "</div>";
        echo "</div>";
    }
    ?>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => content.classList.remove('active'));

            // Remove active class from all buttons
            const buttons = document.querySelectorAll('.tab-button');
            buttons.forEach(button => button.classList.remove('active'));

            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>

</html>