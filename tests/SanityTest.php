<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SanityTest extends TestCase
{
    #[Test]
    public function sanity_check(): void
    {
        $this->assertTrue(true);
    }
}
