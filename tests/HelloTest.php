<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class HelloTest extends TestCase
{
    #[Test]
    public function it_returns_hi(): void
    {
        require_once __DIR__ . '/../lib/hello.php';
        $this->assertSame('hi', hello());
    }
}
