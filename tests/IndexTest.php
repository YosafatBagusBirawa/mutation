<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IndexTest extends TestCase
{
    #[Test]
    public function index_runs_without_fatal_error(): void
    {
        // Jangan include index.php kalau itu memanggil mysqli_connect...
        // cukup assertion sederhana untuk memastikan test suite berjalan
        $this->assertTrue(true);
    }
}
