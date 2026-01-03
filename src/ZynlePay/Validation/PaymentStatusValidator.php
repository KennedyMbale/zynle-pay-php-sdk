<?php

declare(strict_types=1);

namespace ZynlePay\Validation;

class PaymentStatusValidator
{
    /**
     * Validate reference number format
     */
    public function isValidReferenceNo(string $referenceNo): bool
    {
        // Adjust validation rules based on your actual reference number format
        if (empty($referenceNo)) {
            return false;
        }

        // Example: Minimum length, alphanumeric with dashes/underscores
        if (strlen($referenceNo) < 5 || strlen($referenceNo) > 100) {
            return false;
        }

        // Allow alphanumeric, dashes, and underscores
        return preg_match('/^[a-zA-Z0-9_-]+$/', $referenceNo) === 1;
    }
}
