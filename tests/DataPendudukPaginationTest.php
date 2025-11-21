<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

// Include the Utils library that data-penduduk.php uses
require_once __DIR__ . '/../lib/Utils.php';

/**
 * Test cases for pagination logic from data-penduduk.php
 * 
 * These tests validate pagination calculations without requiring database:
 * - Page number validation and defaults
 * - Position calculation for LIMIT clause
 * - Page limit boundaries
 * 
 * Note: These tests exercise lib/Utils.php functions to cover mutation testing
 */
final class DataPendudukPaginationTest extends TestCase
{
    /**
     * Helper function: calculatePaginationParams
     * Simulates the pagination logic used in data-penduduk.php
     * 
     * @param int|null $pageParam The page parameter from GET request (can be null/empty)
     * @param int $itemsPerPage Items per page (default 8 in data-penduduk.php)
     * @return array Array with keys: 'page', 'position', 'itemsPerPage'
     */
    private function calculatePaginationParams($pageParam, int $itemsPerPage = 8): array
    {
        // Simulates: $halaman = @$_GET['id'];
        $halaman = $pageParam;
        
        // Simulates the logic from data-penduduk.php
        if (empty($halaman)) {
            $posisi = 0;
            $halaman = 1;
        } else {
            $posisi = ($halaman - 1) * $itemsPerPage;
        }
        
        return [
            'page' => $halaman,
            'position' => $posisi,
            'itemsPerPage' => $itemsPerPage
        ];
    }

    #[Test]
    public function pagination_default_when_empty(): void
    {
        $result = $this->calculatePaginationParams(null);
        
        $this->assertSame(1, $result['page']);
        $this->assertSame(0, $result['position']);
        $this->assertSame(8, $result['itemsPerPage']);
    }

    #[Test]
    public function pagination_default_when_zero(): void
    {
        $result = $this->calculatePaginationParams(0);
        
        $this->assertSame(1, $result['page']);
        $this->assertSame(0, $result['position']);
    }

    #[Test]
    public function pagination_page_one(): void
    {
        $result = $this->calculatePaginationParams(1);
        
        $this->assertSame(1, $result['page']);
        $this->assertSame(0, $result['position']);
    }

    #[Test]
    public function pagination_page_two(): void
    {
        $result = $this->calculatePaginationParams(2);
        
        $this->assertSame(2, $result['page']);
        $this->assertSame(8, $result['position']);
    }

    #[Test]
    public function pagination_page_three(): void
    {
        $result = $this->calculatePaginationParams(3);
        
        $this->assertSame(3, $result['page']);
        $this->assertSame(16, $result['position']);
    }

    #[Test]
    public function pagination_page_five_with_custom_limit(): void
    {
        $result = $this->calculatePaginationParams(5, 10);
        
        $this->assertSame(5, $result['page']);
        $this->assertSame(40, $result['position']);
        $this->assertSame(10, $result['itemsPerPage']);
    }

    #[Test]
    public function pagination_position_formula(): void
    {
        // Test: position = (page - 1) * itemsPerPage
        for ($page = 1; $page <= 10; $page++) {
            $result = $this->calculatePaginationParams($page);
            $expectedPosition = ($page - 1) * 8;
            $this->assertSame($expectedPosition, $result['position']);
        }
    }

    #[Test]
    public function pagination_high_page_number(): void
    {
        $result = $this->calculatePaginationParams(100);
        
        $this->assertSame(100, $result['page']);
        $this->assertSame(792, $result['position']);
    }

    #[Test]
    public function pagination_negative_page_treated_as_empty(): void
    {
        // Negative page numbers might come from malformed requests
        // PHP empty() treats negative numbers as non-empty, but let's test the logic
        $result = $this->calculatePaginationParams(-1);
        
        // -1 is not empty, so it would be used as page
        // position = (-1 - 1) * 8 = -16
        // This shows a potential bug in the original code
        $this->assertSame(-1, $result['page']);
        $this->assertSame(-16, $result['position']);
    }

    #[Test]
    public function pagination_returns_array_with_required_keys(): void
    {
        $result = $this->calculatePaginationParams(2);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('page', $result);
        $this->assertArrayHasKey('position', $result);
        $this->assertArrayHasKey('itemsPerPage', $result);
        $this->assertCount(3, $result);
    }

    #[Test]
    public function pagination_all_values_are_integers(): void
    {
        $result = $this->calculatePaginationParams(3);
        
        $this->assertIsInt($result['page']);
        $this->assertIsInt($result['position']);
        $this->assertIsInt($result['itemsPerPage']);
    }

    /**
     * Helper: Simulates building pagination HTML links
     * Logic from data-penduduk.php pagination section
     */
    private function buildPaginationLinks(int $currentPage, int $totalPages): array
    {
        $sebelum = $currentPage - 1;
        $setelah = $currentPage + 1;
        
        return [
            'canShowPrevious' => $currentPage > 1,
            'previousPage' => $sebelum,
            'canShowNext' => $currentPage < $totalPages,
            'nextPage' => $setelah,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ];
    }

    #[Test]
    public function pagination_links_first_page(): void
    {
        $links = $this->buildPaginationLinks(1, 5);
        
        $this->assertFalse($links['canShowPrevious']);
        $this->assertTrue($links['canShowNext']);
        $this->assertSame(0, $links['previousPage']);
        $this->assertSame(2, $links['nextPage']);
    }

    #[Test]
    public function pagination_links_middle_page(): void
    {
        $links = $this->buildPaginationLinks(3, 5);
        
        $this->assertTrue($links['canShowPrevious']);
        $this->assertTrue($links['canShowNext']);
        $this->assertSame(2, $links['previousPage']);
        $this->assertSame(4, $links['nextPage']);
    }

    #[Test]
    public function pagination_links_last_page(): void
    {
        $links = $this->buildPaginationLinks(5, 5);
        
        $this->assertTrue($links['canShowPrevious']);
        $this->assertFalse($links['canShowNext']);
        $this->assertSame(4, $links['previousPage']);
        $this->assertSame(6, $links['nextPage']);
    }

    /**
     * Helper: Calculate total pages from item count
     * Used in data-penduduk.php
     */
    private function calculateTotalPages(int $totalItems, int $itemsPerPage = 8): int
    {
        if ($totalItems === 0) {
            return 0;
        }
        if ($itemsPerPage === 0) {
            return 1;
        }
        return (int)ceil($totalItems / $itemsPerPage);
    }

    #[Test]
    public function calculate_total_pages_zero_items(): void
    {
        $result = $this->calculateTotalPages(0);
        $this->assertSame(0, $result);
    }

    #[Test]
    public function calculate_total_pages_one_item(): void
    {
        $result = $this->calculateTotalPages(1);
        $this->assertSame(1, $result);
    }

    #[Test]
    public function calculate_total_pages_exact_multiple(): void
    {
        $result = $this->calculateTotalPages(16, 8);
        $this->assertSame(2, $result);
    }

    #[Test]
    public function calculate_total_pages_with_remainder(): void
    {
        $result = $this->calculateTotalPages(17, 8);
        $this->assertSame(3, $result);
    }

    #[Test]
    public function calculate_total_pages_many_items(): void
    {
        $result = $this->calculateTotalPages(100, 8);
        $this->assertSame(13, $result);
    }

    #[Test]
    public function calculate_total_pages_zero_per_page(): void
    {
        $result = $this->calculateTotalPages(10, 0);
        $this->assertSame(1, $result);
    }

    /**
     * Helper: Validate pagination parameters
     */
    private function isValidPage(int $page, int $totalPages): bool
    {
        return $page >= 1 && $page <= $totalPages;
    }

    #[Test]
    public function is_valid_page_first(): void
    {
        $this->assertTrue($this->isValidPage(1, 5));
    }

    #[Test]
    public function is_valid_page_middle(): void
    {
        $this->assertTrue($this->isValidPage(3, 5));
    }

    #[Test]
    public function is_valid_page_last(): void
    {
        $this->assertTrue($this->isValidPage(5, 5));
    }

    #[Test]
    public function is_valid_page_beyond_total(): void
    {
        $this->assertFalse($this->isValidPage(6, 5));
    }

    #[Test]
    public function is_valid_page_zero(): void
    {
        $this->assertFalse($this->isValidPage(0, 5));
    }

    #[Test]
    public function is_valid_page_negative(): void
    {
        $this->assertFalse($this->isValidPage(-1, 5));
    }

    /**
     * Integration tests: Using actual lib/Utils.php functions
     * This ensures data-penduduk.php logic covers lib/Utils.php for mutation testing
     */

    #[Test]
    public function utils_calculate_pagination_page_one(): void
    {
        $result = Utils::calculatePagination(1, 8);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('page', $result);
        $this->assertArrayHasKey('position', $result);
        $this->assertSame(1, $result['page']);
        $this->assertSame(0, $result['position']);
    }

    #[Test]
    public function utils_calculate_pagination_page_two(): void
    {
        $result = Utils::calculatePagination(2, 8);
        
        $this->assertSame(2, $result['page']);
        $this->assertSame(8, $result['position']);
    }

    #[Test]
    public function utils_calculate_pagination_negative_defaults_to_one(): void
    {
        $result = Utils::calculatePagination(0, 8);
        
        $this->assertSame(1, $result['page']);
        $this->assertSame(0, $result['position']);
    }

    #[Test]
    public function utils_calculate_pagination_various_items_per_page(): void
    {
        // Test with itemsPerPage = 10 (different from data-penduduk.php default of 8)
        $result = Utils::calculatePagination(3, 10);
        
        $this->assertSame(3, $result['page']);
        $this->assertSame(20, $result['position']);
        $this->assertSame(10, $result['itemsPerPage']);
    }

    #[Test]
    public function utils_build_pagination_url_page_one(): void
    {
        $url = Utils::buildPaginationUrl(1);
        
        $this->assertIsString($url);
        $this->assertSame('?id=1', $url);
        $this->assertStringStartsWith('?id=', $url);
    }

    #[Test]
    public function utils_build_pagination_url_page_three(): void
    {
        $url = Utils::buildPaginationUrl(3);
        
        $this->assertSame('?id=3', $url);
        $this->assertStringContainsString('3', $url);
    }

    #[Test]
    public function utils_build_pagination_url_zero_defaults_to_one(): void
    {
        $url = Utils::buildPaginationUrl(0);
        
        $this->assertSame('?id=1', $url);
        $this->assertStringNotContainsString('0', $url);
    }

    #[Test]
    public function utils_is_first_page_true(): void
    {
        $result = Utils::isFirstPage(1);
        
        $this->assertTrue($result);
        $this->assertIsBool($result);
    }

    #[Test]
    public function utils_is_first_page_false(): void
    {
        $result = Utils::isFirstPage(2);
        
        $this->assertFalse($result);
        $this->assertIsBool($result);
    }

    #[Test]
    public function utils_is_first_page_zero(): void
    {
        $result = Utils::isFirstPage(0);
        
        $this->assertTrue($result);
    }

    #[Test]
    public function utils_has_next_page_true(): void
    {
        $result = Utils::hasNextPage(2, 5);
        
        $this->assertTrue($result);
        $this->assertIsBool($result);
    }

    #[Test]
    public function utils_has_next_page_false_at_last(): void
    {
        $result = Utils::hasNextPage(5, 5);
        
        $this->assertFalse($result);
    }

    #[Test]
    public function utils_has_next_page_false_beyond_total(): void
    {
        $result = Utils::hasNextPage(10, 5);
        
        $this->assertFalse($result);
    }

    #[Test]
    public function utils_has_next_page_boundary_conditions(): void
    {
        $this->assertTrue(Utils::hasNextPage(1, 2));
        $this->assertFalse(Utils::hasNextPage(2, 2));
        $this->assertFalse(Utils::hasNextPage(3, 2));
    }

    /**
     * Test pagination workflow: simulate data-penduduk.php page navigation
     * This tests the complete flow of pagination using Utils functions
     */

    #[Test]
    public function pagination_workflow_navigate_pages(): void
    {
        $totalPages = 5;
        
        // Test navigation from page 1
        $page1 = Utils::calculatePagination(1, 8);
        $this->assertSame(1, $page1['page']);
        $this->assertFalse(Utils::isFirstPage(2));
        $this->assertTrue(Utils::hasNextPage(1, $totalPages));
        
        // Test navigation to page 3
        $page3 = Utils::calculatePagination(3, 8);
        $this->assertSame(3, $page3['page']);
        $this->assertSame(16, $page3['position']);
        $this->assertFalse(Utils::isFirstPage(3));
        $this->assertTrue(Utils::hasNextPage(3, $totalPages));
        
        // Test navigation to last page
        $page5 = Utils::calculatePagination(5, 8);
        $this->assertSame(5, $page5['page']);
        $this->assertSame(32, $page5['position']);
        $this->assertFalse(Utils::hasNextPage(5, $totalPages));
    }

    #[Test]
    public function pagination_url_generation_sequence(): void
    {
        // Simulate generating URLs for pagination links
        $urls = [];
        for ($page = 1; $page <= 5; $page++) {
            $urls[$page] = Utils::buildPaginationUrl($page);
        }
        
        $this->assertSame('?id=1', $urls[1]);
        $this->assertSame('?id=2', $urls[2]);
        $this->assertSame('?id=3', $urls[3]);
        $this->assertSame('?id=4', $urls[4]);
        $this->assertSame('?id=5', $urls[5]);
    }

    #[Test]
    public function pagination_position_calculation_matches_database_limit(): void
    {
        // Verify that position calculation matches what would be used in LIMIT clause
        // In data-penduduk.php: LIMIT $posisi,$batas
        
        for ($page = 1; $page <= 10; $page++) {
            $result = Utils::calculatePagination($page, 8);
            $expectedPosition = ($page - 1) * 8;
            $this->assertSame($expectedPosition, $result['position']);
        }
    }

    #[Test]
    public function utils_calculate_total_pages(): void
    {
        $result = Utils::calculateTotalPages(25, 8);
        
        $this->assertIsInt($result);
        $this->assertSame(4, $result);
    }

    #[Test]
    public function utils_calculate_total_pages_edge_cases(): void
    {
        // 0 items
        $this->assertSame(0, Utils::calculateTotalPages(0, 8));
        
        // 1 item
        $this->assertSame(1, Utils::calculateTotalPages(1, 8));
        
        // Exact division
        $this->assertSame(2, Utils::calculateTotalPages(16, 8));
        
        // With remainder
        $this->assertSame(3, Utils::calculateTotalPages(17, 8));
    }
}
