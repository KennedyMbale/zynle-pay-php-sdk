<?php

declare(strict_types=1);

namespace ZynlePay\Exception;

class InvalidArgumentException extends \InvalidArgumentException
{
    public function __construct(string $message = 'Invalid argument provided')
    {
        parent::__construct($message);
    }
}
