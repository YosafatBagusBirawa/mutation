<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UtilsTest extends TestCase
{
    #[Test]
    public function generateSlug_converts_spaces_to_dashes(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::generateSlug('Berita Terbaru Desa');
        $this->assertSame('berita-terbaru-desa', $result);
    }

    #[Test]
    public function generateSlug_lowercases_input(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::generateSlug('DATA DESA SUKASENANG');
        $this->assertSame('data-desa-sukasenang', $result);
    }

    #[Test]
    public function generateSlug_handles_empty_string(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::generateSlug('');
        $this->assertSame('', $result);
    }

    #[Test]
    public function calculatePagination_first_page(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::calculatePagination(1, 10);
        $this->assertSame(1, $result['page']);
        $this->assertSame(0, $result['position']);
        $this->assertSame(10, $result['itemsPerPage']);
    }

    #[Test]
    public function calculatePagination_third_page(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::calculatePagination(3, 8);
        $this->assertSame(3, $result['page']);
        $this->assertSame(16, $result['position']);
        $this->assertSame(8, $result['itemsPerPage']);
    }

    #[Test]
    public function calculatePagination_negative_page_defaults_to_one(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::calculatePagination(-1, 10);
        $this->assertSame(1, $result['page']);
        $this->assertSame(0, $result['position']);
    }

    #[Test]
    public function calculatePagination_zero_page_defaults_to_one(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::calculatePagination(0, 10);
        $this->assertSame(1, $result['page']);
        $this->assertSame(0, $result['position']);
    }

    #[Test]
    public function formatDisplayDate_formats_correctly(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::formatDisplayDate('2025-11-14');
        $this->assertSame('14 November 2025', $result);
    }

    #[Test]
    public function formatDisplayDate_with_various_formats(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::formatDisplayDate('2025-01-01');
        $this->assertSame('01 January 2025', $result);
    }

    #[Test]
    public function extractDay_returns_day_number(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::extractDay('2025-11-14');
        $this->assertSame('14', $result);
    }

    #[Test]
    public function extractDay_with_single_digit(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::extractDay('2025-11-05');
        $this->assertSame('05', $result);
    }

    #[Test]
    public function extractMonth_returns_month_name(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::extractMonth('2025-11-14');
        $this->assertSame('November', $result);
    }

    #[Test]
    public function extractMonth_january(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::extractMonth('2025-01-15');
        $this->assertSame('January', $result);
    }

    #[Test]
    public function isFirstPage_true_for_page_one(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $this->assertTrue(Utils::isFirstPage(1));
    }

    #[Test]
    public function isFirstPage_true_for_zero(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $this->assertTrue(Utils::isFirstPage(0));
    }

    #[Test]
    public function isFirstPage_false_for_page_two(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $this->assertFalse(Utils::isFirstPage(2));
    }

    #[Test]
    public function hasNextPage_true_when_current_less_than_total(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $this->assertTrue(Utils::hasNextPage(2, 5));
    }

    #[Test]
    public function hasNextPage_false_when_on_last_page(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $this->assertFalse(Utils::hasNextPage(5, 5));
    }

    #[Test]
    public function hasNextPage_false_when_current_exceeds_total(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $this->assertFalse(Utils::hasNextPage(10, 5));
    }

    #[Test]
    public function buildPaginationUrl_with_valid_page(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::buildPaginationUrl(3);
        $this->assertSame('?id=3', $result);
    }

    #[Test]
    public function buildPaginationUrl_with_negative_page_defaults_to_one(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::buildPaginationUrl(-5);
        $this->assertSame('?id=1', $result);
    }

    #[Test]
    public function buildPaginationUrl_with_zero_page_defaults_to_one(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::buildPaginationUrl(0);
        $this->assertSame('?id=1', $result);
    }

    #[Test]
    public function calculateTotalPages_single_page(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::calculateTotalPages(5, 10);
        $this->assertSame(1, $result);
    }

    #[Test]
    public function calculateTotalPages_multiple_pages(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::calculateTotalPages(25, 8);
        $this->assertSame(4, $result);
    }

    #[Test]
    public function calculateTotalPages_exact_division(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::calculateTotalPages(30, 10);
        $this->assertSame(3, $result);
    }

    #[Test]
    public function calculateTotalPages_zero_items(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::calculateTotalPages(0, 10);
        $this->assertSame(0, $result);
    }

    #[Test]
    public function calculateTotalPages_invalid_items_per_page(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::calculateTotalPages(10, 0);
        $this->assertSame(1, $result);
    }

    #[Test]
    public function truncateText_within_limit(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $text = 'Short text here';
        $result = Utils::truncateText($text, 250);
        $this->assertSame($text, $result);
    }

    #[Test]
    public function truncateText_exceeds_limit(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $text = 'This is a very long text that should be truncated because it exceeds the specified length limit and should be cut off with an ellipsis at the end of the string';
        $result = Utils::truncateText($text, 50);
        $this->assertStringEndsWith('...', $result);
        $this->assertLessThanOrEqual(53, strlen($result));
    }

    #[Test]
    public function truncateText_strips_html_tags(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $text = '<p>This is <strong>HTML</strong> content</p>';
        $result = Utils::truncateText($text, 100);
        $this->assertStringNotContainsString('<', $result);
        $this->assertStringNotContainsString('>', $result);
    }

    #[Test]
    public function truncateText_default_length(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $text = str_repeat('a', 300);
        $result = Utils::truncateText($text);
        $this->assertLessThanOrEqual(253, strlen($result));
    }

    #[Test]
    public function getCommentCountLabel_returns_string(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::getCommentCountLabel(5);
        $this->assertIsString($result);
        $this->assertSame('5', $result);
    }

    #[Test]
    public function getCommentCountLabel_with_zero(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::getCommentCountLabel(0);
        $this->assertSame('0', $result);
    }

    #[Test]
    public function getCommentCountLabel_with_large_number(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::getCommentCountLabel(999);
        $this->assertSame('999', $result);
    }
}
