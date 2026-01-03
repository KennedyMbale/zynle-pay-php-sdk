<?php

/**
 * ZynlePay Webhook Handler Example
 * Demonstrates how to handle payment confirmation webhooks
 */

require_once __DIR__ . '/../vendor/autoload.php';

use ZynlePay\WebhookHandler;

// Initialize webhook handler
$webhookHandler = new WebhookHandler();

header('Content-Type: application/json');

try {
    // Handle the webhook
    $result = $webhookHandler->handle($_POST);

    // Log the webhook for debugging
    error_log('Webhook received: ' . json_encode($_POST));
    error_log('Webhook processed: ' . json_encode($result));

    // Return success response
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Webhook processed successfully',
        'data' => $result
    ]);
} catch (Exception $e) {
    // Log the error
    error_log('Webhook processing failed: ' . $e->getMessage());

    // Return error response
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZynlePay Webhook Handler Example</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
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

        .webhook-simulator {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        textarea {
            width: 100%;
            height: 200px;
            font-family: monospace;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .response {
            margin-top: 20px;
            padding: 15px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            display: none;
        }

        .error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>

<body>
    <h1>ZynlePay Webhook Handler Example</h1>
    <p>This example demonstrates how to handle payment confirmation webhooks from ZynlePay.</p>

    <div class="container">
        <h2>Webhook Simulator</h2>
        <p>Use this form to simulate webhook data and test the handler:</p>

        <div class="webhook-simulator">
            <h3>Sample Webhook Data</h3>
            <textarea id="webhookData" placeholder="Paste webhook JSON data here...">{
  "reference_no": "REF1234567890",
  "transaction_id": "TXN1234567890",
  "amount": "100.00",
  "currency": "ZMW",
  "status": "completed",
  "payment_method": "momo",
  "timestamp": "2024-01-15T10:30:00Z",
  "signature": "webhook_signature_here"
}</textarea>
        </div>

        <button onclick="simulateWebhook()">Simulate Webhook</button>

        <div id="response" class="response">
            <h3>Response:</h3>
            <pre id="responseContent"></pre>
        </div>
    </div>

    <div class="container">
        <h2>How Webhooks Work</h2>
        <ol>
            <li>ZynlePay sends a POST request to your webhook endpoint</li>
            <li>The webhook handler validates and processes the data</li>
            <li>Your application updates the payment status</li>
            <li>You return a success response (HTTP 200)</li>
        </ol>

        <h3>Webhook Data Structure</h3>
        <ul>
            <li><code>reference_no</code>: Your reference number</li>
            <li><code>transaction_id</code>: ZynlePay transaction ID</li>
            <li><code>amount</code>: Payment amount</li>
            <li><code>currency</code>: Payment currency</li>
            <li><code>status</code>: Payment status (completed, failed, pending)</li>
            <li><code>payment_method</code>: Payment method used</li>
            <li><code>timestamp</code>: When the webhook was sent</li>
            <li><code>signature</code>: Security signature for validation</li>
        </ul>
    </div>

    <script>
        async function simulateWebhook() {
            const webhookData = document.getElementById('webhookData').value;
            const responseDiv = document.getElementById('response');
            const responseContent = document.getElementById('responseContent');

            try {
                const data = JSON.parse(webhookData);

                const response = await fetch('webhook-handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(data)
                });

                const result = await response.json();

                responseDiv.style.display = 'block';
                responseDiv.classList.remove('error');

                responseContent.textContent = JSON.stringify(result, null, 2);

            } catch (error) {
                responseDiv.style.display = 'block';
                responseDiv.classList.add('error');
                responseContent.textContent = 'Error: ' + error.message;
            }
        }
    </script>
</body>

</html>