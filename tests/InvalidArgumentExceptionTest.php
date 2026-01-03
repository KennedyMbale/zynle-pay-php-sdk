<?php

declare(strict_types=1);

namespace ZynlePay\Tests\Exception;

use PHPUnit\Framework\TestCase;
use ZynlePay\Exception\InvalidArgumentException;

class InvalidArgumentExceptionTest extends TestCase
{
    public function testExceptionMessage(): void
    {
        $exception = new InvalidArgumentException('Custom message');

        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
        $this->assertEquals('Custom message', $exception->getMessage());
    }

    public function testDefaultMessage(): void
    {
        $exception = new InvalidArgumentException();

        $this->assertEquals('Invalid argument provided', $exception->getMessage());
    }
}
