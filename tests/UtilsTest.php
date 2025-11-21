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
        $this->assertStringNotContainsString(' ', $result);
        $this->assertStringContainsString('-', $result);
        $this->assertNotSame('berita terbaru desa', $result);
    }

    #[Test]
    public function generateSlug_lowercases_input(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::generateSlug('DATA DESA SUKASENANG');
        $this->assertSame('data-desa-sukasenang', $result);
        $this->assertFalse(ctype_upper($result));
        $this->assertNotSame('DATA-DESA-SUKASENANG', $result);
        $this->assertTrue(ctype_lower(str_replace('-', '', $result)));
    }

    #[Test]
    public function generateSlug_handles_empty_string(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::generateSlug('');
        $this->assertSame('', $result);
        $this->assertEmpty($result);
        $this->assertIsString($result);
    }

    #[Test]
    public function generateSlug_preserves_content(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::generateSlug('Hello World');
        $this->assertSame('hello-world', $result);
        $this->assertStringContainsString('hello', $result);
        $this->assertStringContainsString('world', $result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function calculatePagination_first_page(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::calculatePagination(1, 10);
        $this->assertSame(1, $result['page']);
        $this->assertSame(0, $result['position']);
        $this->assertSame(10, $result['itemsPerPage']);
        $this->assertArrayHasKey('page', $result);
        $this->assertArrayHasKey('position', $result);
        $this->assertIsArray($result);
    }

    #[Test]
    public function calculatePagination_third_page(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::calculatePagination(3, 8);
        $this->assertSame(3, $result['page']);
        $this->assertSame(16, $result['position']);
        $this->assertSame(8, $result['itemsPerPage']);
        $this->assertNotSame(2, $result['page']);
        $this->assertGreaterThan(0, $result['position']);
    }

    #[Test]
    public function calculatePagination_position_calculation(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        // Page 2 with 10 items per page should have position 10
        $result = Utils::calculatePagination(2, 10);
        $this->assertSame(10, $result['position']);
        $this->assertSame(2, $result['page']);
        
        // Page 5 with 20 items per page should have position 80
        $result = Utils::calculatePagination(5, 20);
        $this->assertSame(80, $result['position']);
        $this->assertSame(5, $result['page']);
    }

    #[Test]
    public function calculatePagination_negative_page_defaults_to_one(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::calculatePagination(-1, 10);
        $this->assertSame(1, $result['page']);
        $this->assertSame(0, $result['position']);
        $this->assertNotSame(-1, $result['page']);
    }

    #[Test]
    public function calculatePagination_zero_page_defaults_to_one(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::calculatePagination(0, 10);
        $this->assertSame(1, $result['page']);
        $this->assertSame(0, $result['position']);
        $this->assertNotSame(0, $result['page']);
        $this->assertGreaterThan(0, $result['page']);
    }

    #[Test]
    public function formatDisplayDate_formats_correctly(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::formatDisplayDate('2025-11-14');
        $this->assertSame('14 November 2025', $result);
        $this->assertStringContainsString('14', $result);
        $this->assertStringContainsString('November', $result);
        $this->assertStringContainsString('2025', $result);
        $this->assertNotSame('2025-11-14', $result);
    }

    #[Test]
    public function formatDisplayDate_with_various_formats(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::formatDisplayDate('2025-01-01');
        $this->assertSame('01 January 2025', $result);
        $this->assertStringContainsString('January', $result);
        $this->assertStringNotContainsString('Dec', $result);
        $this->assertStringStartsWith('01', $result);
    }

    #[Test]
    public function formatDisplayDate_day_month_order(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::formatDisplayDate('2025-03-15');
        // Should be "day month year" format, not "month day year"
        $this->assertStringStartsWith('15', $result);
        $this->assertStringContainsString('March', $result);
    }

    #[Test]
    public function extractDay_returns_day_number(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::extractDay('2025-11-14');
        $this->assertSame('14', $result);
        $this->assertStringContainsString('14', $result);
        $this->assertNotSame('11', $result);
        $this->assertNotSame('2025', $result);
    }

    #[Test]
    public function extractDay_with_single_digit(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::extractDay('2025-11-05');
        $this->assertSame('05', $result);
        $this->assertStringStartsWith('0', $result);
        $this->assertStringEndsWith('5', $result);
        $this->assertNotSame('5', $result); // Should be padded
    }

    #[Test]
    public function extractDay_consistency(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $day = Utils::extractDay('2025-07-22');
        $this->assertSame('22', $day);
        $this->assertIsString($day);
        $this->assertStringNotContainsString('-', $day);
        $this->assertSame(2, strlen($day));
    }

    #[Test]
    public function extractMonth_returns_month_name(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::extractMonth('2025-11-14');
        $this->assertSame('November', $result);
        $this->assertStringNotContainsString('11', $result);
        $this->assertNotSame('11', $result);
        $this->assertStringContainsString('November', $result);
    }

    #[Test]
    public function extractMonth_january(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::extractMonth('2025-01-15');
        $this->assertSame('January', $result);
        $this->assertNotSame('01', $result);
        $this->assertNotSame('Jan', $result); // Full name, not abbreviated
        $this->assertStringStartsWith('J', $result);
    }

    #[Test]
    public function extractMonth_all_months_are_different(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = Utils::extractMonth(sprintf('2025-%02d-15', $i));
            $months[] = $month;
            $this->assertNotEmpty($month);
            $this->assertIsString($month);
        }
        
        // All months should be unique
        $this->assertCount(12, array_unique($months));
    }

    #[Test]
    public function isFirstPage_true_for_page_one(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::isFirstPage(1);
        $this->assertTrue($result);
        $this->assertIsBool($result);
        $this->assertNotFalse($result);
    }

    #[Test]
    public function isFirstPage_true_for_zero(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::isFirstPage(0);
        $this->assertTrue($result);
        $this->assertIsBool($result);
    }

    #[Test]
    public function isFirstPage_false_for_page_two(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::isFirstPage(2);
        $this->assertFalse($result);
        $this->assertIsBool($result);
        $this->assertNotTrue($result);
    }

    #[Test]
    public function isFirstPage_boundary_conditions(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $this->assertTrue(Utils::isFirstPage(1));
        $this->assertFalse(Utils::isFirstPage(2));
        $this->assertFalse(Utils::isFirstPage(100));
        $this->assertTrue(Utils::isFirstPage(-5)); // Negative should be <= 1
    }

    #[Test]
    public function hasNextPage_true_when_current_less_than_total(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::hasNextPage(2, 5);
        $this->assertTrue($result);
        $this->assertIsBool($result);
        $this->assertNotFalse($result);
    }

    #[Test]
    public function hasNextPage_false_when_on_last_page(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::hasNextPage(5, 5);
        $this->assertFalse($result);
        $this->assertIsBool($result);
        $this->assertNotTrue($result);
    }

    #[Test]
    public function hasNextPage_false_when_current_exceeds_total(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::hasNextPage(10, 5);
        $this->assertFalse($result);
        $this->assertIsBool($result);
    }

    #[Test]
    public function hasNextPage_boundary_testing(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $this->assertTrue(Utils::hasNextPage(1, 5));
        $this->assertTrue(Utils::hasNextPage(4, 5));
        $this->assertFalse(Utils::hasNextPage(5, 5));
        $this->assertFalse(Utils::hasNextPage(6, 5));
    }

    #[Test]
    public function buildPaginationUrl_with_valid_page(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::buildPaginationUrl(3);
        $this->assertSame('?id=3', $result);
        $this->assertStringContainsString('?id=', $result);
        $this->assertStringContainsString('3', $result);
        $this->assertStringStartsWith('?', $result);
    }

    #[Test]
    public function buildPaginationUrl_with_negative_page_defaults_to_one(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::buildPaginationUrl(-5);
        $this->assertSame('?id=1', $result);
        $this->assertStringContainsString('1', $result);
        $this->assertStringNotContainsString('-5', $result);
    }

    #[Test]
    public function buildPaginationUrl_with_zero_page_defaults_to_one(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::buildPaginationUrl(0);
        $this->assertSame('?id=1', $result);
        $this->assertStringNotContainsString('0', $result);
    }

    #[Test]
    public function buildPaginationUrl_format_consistency(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $urls = [
            Utils::buildPaginationUrl(1),
            Utils::buildPaginationUrl(5),
            Utils::buildPaginationUrl(100),
        ];

        foreach ($urls as $url) {
            $this->assertStringStartsWith('?id=', $url);
            $this->assertStringNotContainsString(' ', $url);
        }
    }

    #[Test]
    public function calculateTotalPages_single_page(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::calculateTotalPages(5, 10);
        $this->assertSame(1, $result);
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    #[Test]
    public function calculateTotalPages_multiple_pages(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::calculateTotalPages(25, 8);
        $this->assertSame(4, $result);
        $this->assertGreaterThan(1, $result);
        $this->assertNotSame(3, $result);
    }

    #[Test]
    public function calculateTotalPages_exact_division(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::calculateTotalPages(30, 10);
        $this->assertSame(3, $result);
        $this->assertSame(30 / 10, $result);
        $this->assertNotSame(4, $result);
    }

    #[Test]
    public function calculateTotalPages_zero_items(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::calculateTotalPages(0, 10);
        $this->assertSame(0, $result);
        $this->assertLessThanOrEqual(0, $result);
    }

    #[Test]
    public function calculateTotalPages_invalid_items_per_page(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::calculateTotalPages(10, 0);
        $this->assertSame(1, $result);
        $this->assertNotSame(0, $result);
        $this->assertGreaterThan(0, $result);
    }

    #[Test]
    public function calculateTotalPages_rounding_up(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        // 25 items with 10 per page = 2.5 pages → rounds up to 3
        $result = Utils::calculateTotalPages(25, 10);
        $this->assertSame(3, $result);
        $this->assertGreaterThan(2, $result);
        $this->assertLessThan(4, $result);
    }

    #[Test]
    public function truncateText_within_limit(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $text = 'Short text here';
        $result = Utils::truncateText($text, 250);
        $this->assertSame($text, $result);
        $this->assertStringNotContainsString('...', $result);
        $this->assertNotEmpty($result);
        $this->assertLessThan(250, strlen($result));
    }

    #[Test]
    public function truncateText_exceeds_limit(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $text = 'This is a very long text that should be truncated because it exceeds the specified length limit and should be cut off with an ellipsis at the end of the string';
        $result = Utils::truncateText($text, 50);
        $this->assertStringEndsWith('...', $result);
        $this->assertLessThanOrEqual(53, strlen($result));
        $this->assertLessThan(strlen($text), strlen($result) + 1); // Shorter than original
    }

    #[Test]
    public function truncateText_strips_html_tags(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $text = '<p>This is <strong>HTML</strong> content</p>';
        $result = Utils::truncateText($text, 100);
        $this->assertStringNotContainsString('<', $result);
        $this->assertStringNotContainsString('>', $result);
        $this->assertStringNotContainsString('<p>', $result);
        $this->assertStringNotContainsString('<strong>', $result);
        $this->assertStringContainsString('HTML', $result); // Content preserved
    }

    #[Test]
    public function truncateText_default_length(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $text = str_repeat('a', 300);
        $result = Utils::truncateText($text);
        $this->assertLessThanOrEqual(253, strlen($result));
        $this->assertStringEndsWith('...', $result);
        $this->assertLessThan(300, strlen($result));
    }

    #[Test]
    public function truncateText_preserves_content(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $text = 'Lorem ipsum dolor sit amet';
        $result = Utils::truncateText($text, 15);
        $this->assertStringStartsWith('Lorem', $result);
        $this->assertStringEndsWith('...', $result);
        $this->assertLessThan(strlen($text), strlen($result) + 1);
    }

    #[Test]
    public function getCommentCountLabel_returns_string(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::getCommentCountLabel(5);
        $this->assertIsString($result);
        $this->assertSame('5', $result);
        $this->assertNotSame(5, $result); // Must be string, not int
    }

    #[Test]
    public function getCommentCountLabel_with_zero(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::getCommentCountLabel(0);
        $this->assertSame('0', $result);
        $this->assertIsString($result);
        $this->assertStringContainsString('0', $result);
    }

    #[Test]
    public function getCommentCountLabel_with_large_number(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $result = Utils::getCommentCountLabel(999);
        $this->assertSame('999', $result);
        $this->assertIsString($result);
        $this->assertStringContainsString('999', $result);
    }

    #[Test]
    public function getCommentCountLabel_type_conversion(): void
    {
        require_once __DIR__ . '/../lib/Utils.php';

        $label = Utils::getCommentCountLabel(42);
        $this->assertIsString($label);
        $this->assertSame('42', $label);
        $this->assertNotSame(42, $label); // Different types
        $this->assertSame(42, (int)$label); // Can convert back
    }
}
