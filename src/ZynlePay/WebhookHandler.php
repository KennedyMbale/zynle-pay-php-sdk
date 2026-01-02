<?php

namespace ZynlePay;

class WebhookHandler
{
    public function handle(array $payload): array
    {
        // Validate the payload
        if (!isset($payload['response_code']) || !isset($payload['reference_no'])) {
            throw new \InvalidArgumentException('Invalid webhook payload');
        }

        $responseCode = $payload['response_code'];
        $referenceNo = $payload['reference_no'];

        // Process based on response code
        if ($responseCode == '100') {
            // Payment successful
            return [
                'status' => 'success',
                'reference_no' => $referenceNo,
                'action' => 'complete_payment'
            ];
        } elseif ($responseCode == '120') {
            // Payment initiated (from process_payment)
            return [
                'status' => 'initiated',
                'reference_no' => $referenceNo,
                'action' => 'await_confirmation'
            ];
        } else {
            // Payment failed or other status
            return [
                'status' => 'failed',
                'reference_no' => $referenceNo,
                'action' => 'cancel_order',
                'error' => $payload['response_description'] ?? 'Unknown error'
            ];
        }
    }
}
