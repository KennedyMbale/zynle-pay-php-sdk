<?php

declare(strict_types=1);

namespace ZynlePay\Exception;

use InvalidArgumentException;

class InvalidConfigurationException extends InvalidArgumentException
{
    public function __construct(string $message = 'Invalid configuration provided')
    {
        parent::__construct($message);
    }
}
