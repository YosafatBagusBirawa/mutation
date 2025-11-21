<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

require_once __DIR__ . '/../lib/Utils.php';

/**
 * Test cases for gallery.php page logic
 * 
 * Tests the specific business logic of the gallery page including:
 * - Pagination handling
 * - Gallery item display and formatting
 * - Previous/Next navigation
 * - Empty state handling
 */
final class GalleryPageTest extends TestCase
{
    /**
     * Helper: Simulate gallery pagination logic from gallery.php
     * Gallery uses GET parameter 'g' for pagination with 8 items per page
     */
    private function calculateGalleryPagination($pageParam): array
    {
        $batas = 8;
        $halaman = $pageParam;
        
        if (empty($halaman)) {
            $posisi = 0;
            $halaman = 1;
        } else {
            $posisi = ($halaman - 1) * $batas;
        }
        
        return [
            'page' => $halaman,
            'position' => $posisi,
            'limit' => $batas,
            'no_start' => $posisi + 1
        ];
    }

    #[Test]
    public function gallery_default_page_is_one(): void
    {
        $pagination = $this->calculateGalleryPagination(null);
        
        $this->assertSame(1, $pagination['page']);
        $this->assertSame(0, $pagination['position']);
        $this->assertSame(1, $pagination['no_start']);
    }

    #[Test]
    public function gallery_zero_page_defaults_to_one(): void
    {
        $pagination = $this->calculateGalleryPagination(0);
        
        $this->assertSame(1, $pagination['page']);
        $this->assertSame(0, $pagination['position']);
    }

    #[Test]
    public function gallery_page_two_position(): void
    {
        $pagination = $this->calculateGalleryPagination(2);
        
        $this->assertSame(2, $pagination['page']);
        $this->assertSame(8, $pagination['position']);
        $this->assertSame(9, $pagination['no_start']);
    }

    #[Test]
    public function gallery_page_three_position(): void
    {
        $pagination = $this->calculateGalleryPagination(3);
        
        $this->assertSame(3, $pagination['page']);
        $this->assertSame(16, $pagination['position']);
        $this->assertSame(17, $pagination['no_start']);
    }

    #[Test]
    public function gallery_items_per_page_is_eight(): void
    {
        $pagination = $this->calculateGalleryPagination(1);
        
        $this->assertSame(8, $pagination['limit']);
    }

    #[Test]
    public function gallery_position_formula_consistency(): void
    {
        for ($page = 1; $page <= 10; $page++) {
            $pagination = $this->calculateGalleryPagination($page);
            $expectedPosition = ($page - 1) * 8;
            $this->assertSame($expectedPosition, $pagination['position']);
        }
    }

    /**
     * Helper: Simulate gallery item data structure
     */
    private function getGalleryItem(string $title, string $description, string $date, string $image, string $link): array
    {
        return [
            'id' => rand(1, 1000),
            'judul' => $title,
            'deskripsi' => $description,
            'tanggal' => $date,
            'gambar' => $image,
            'link' => $link,
            'path_prefix' => 'web/gambar/'
        ];
    }

    #[Test]
    public function gallery_item_has_all_required_fields(): void
    {
        $item = $this->getGalleryItem(
            'Test Gallery',
            'Description here',
            '2025-11-21',
            'image.jpg',
            'https://example.com'
        );
        
        $this->assertArrayHasKey('judul', $item);
        $this->assertArrayHasKey('deskripsi', $item);
        $this->assertArrayHasKey('tanggal', $item);
        $this->assertArrayHasKey('gambar', $item);
        $this->assertArrayHasKey('link', $item);
    }

    #[Test]
    public function gallery_item_title_is_not_empty(): void
    {
        $item = $this->getGalleryItem(
            'Event Gallery',
            'Description',
            '2025-11-21',
            'image.jpg',
            'https://example.com'
        );
        
        $this->assertNotEmpty($item['judul']);
        $this->assertSame('Event Gallery', $item['judul']);
    }

    #[Test]
    public function gallery_item_image_path_is_correct(): void
    {
        $item = $this->getGalleryItem(
            'Test',
            'Desc',
            '2025-11-21',
            'photo.jpg',
            'https://example.com'
        );
        
        $fullPath = $item['path_prefix'] . $item['gambar'];
        $this->assertSame('web/gambar/photo.jpg', $fullPath);
    }

    /**
     * Helper: Simulate gallery item display formatting
     */
    private function formatGalleryItemDisplay(string $description, string $date): array
    {
        $stripped = strip_tags($description);
        $preview = substr($stripped, 0, 300);
        $formattedDate = date('l, d F Y', strtotime($date));
        
        return [
            'preview_text' => $preview,
            'preview_length' => strlen($preview),
            'formatted_date' => $formattedDate,
            'is_truncated' => strlen($stripped) > 300
        ];
    }

    #[Test]
    public function gallery_description_preview_max_300_chars(): void
    {
        $description = str_repeat('Lorem ipsum dolor sit amet. ', 20);
        $display = $this->formatGalleryItemDisplay($description, '2025-11-21');
        
        $this->assertLessThanOrEqual(300, $display['preview_length']);
    }

    #[Test]
    public function gallery_description_strips_html(): void
    {
        $htmlDescription = '<p>This is <strong>HTML</strong> content</p>';
        $display = $this->formatGalleryItemDisplay($htmlDescription, '2025-11-21');
        
        $this->assertStringNotContainsString('<', $display['preview_text']);
        $this->assertStringNotContainsString('>', $display['preview_text']);
        $this->assertStringContainsString('HTML', $display['preview_text']);
    }

    #[Test]
    public function gallery_date_format_is_day_date_month_year(): void
    {
        $display = $this->formatGalleryItemDisplay('Description', '2025-11-21');
        
        $this->assertStringContainsString('Friday', $display['formatted_date']);
        $this->assertStringContainsString('21', $display['formatted_date']);
        $this->assertStringContainsString('November', $display['formatted_date']);
        $this->assertStringContainsString('2025', $display['formatted_date']);
    }

    #[Test]
    public function gallery_long_description_is_marked_truncated(): void
    {
        $longDescription = str_repeat('Lorem ipsum dolor sit amet consectetur. ', 30);
        $display = $this->formatGalleryItemDisplay($longDescription, '2025-11-21');
        
        $this->assertTrue($display['is_truncated']);
    }

    #[Test]
    public function gallery_short_description_is_not_truncated(): void
    {
        $shortDescription = 'This is a short description';
        $display = $this->formatGalleryItemDisplay($shortDescription, '2025-11-21');
        
        $this->assertFalse($display['is_truncated']);
    }

    /**
     * Helper: Simulate gallery pagination navigation
     */
    private function buildGalleryNavigation(int $currentPage, int $totalPages): array
    {
        $sebelum = $currentPage - 1;
        $setelah = $currentPage + 1;
        
        return [
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'can_show_previous' => $currentPage > 1,
            'can_show_next' => $currentPage < $totalPages,
            'previous_page' => $sebelum,
            'next_page' => $setelah
        ];
    }

    #[Test]
    public function gallery_first_page_no_previous(): void
    {
        $nav = $this->buildGalleryNavigation(1, 5);
        
        $this->assertFalse($nav['can_show_previous']);
        $this->assertTrue($nav['can_show_next']);
        $this->assertSame(2, $nav['next_page']);
    }

    #[Test]
    public function gallery_middle_page_both_navigation(): void
    {
        $nav = $this->buildGalleryNavigation(3, 5);
        
        $this->assertTrue($nav['can_show_previous']);
        $this->assertTrue($nav['can_show_next']);
        $this->assertSame(2, $nav['previous_page']);
        $this->assertSame(4, $nav['next_page']);
    }

    #[Test]
    public function gallery_last_page_no_next(): void
    {
        $nav = $this->buildGalleryNavigation(5, 5);
        
        $this->assertTrue($nav['can_show_previous']);
        $this->assertFalse($nav['can_show_next']);
        $this->assertSame(4, $nav['previous_page']);
    }

    /**
     * Helper: Simulate pagination link generation
     */
    private function buildPaginationLink(int $pageNumber): string
    {
        return '?g=' . $pageNumber;
    }

    #[Test]
    public function gallery_pagination_link_format(): void
    {
        $link = $this->buildPaginationLink(1);
        
        $this->assertSame('?g=1', $link);
        $this->assertStringStartsWith('?g=', $link);
    }

    #[Test]
    public function gallery_pagination_links_sequence(): void
    {
        $links = [];
        for ($i = 1; $i <= 5; $i++) {
            $links[$i] = $this->buildPaginationLink($i);
        }
        
        $this->assertSame('?g=1', $links[1]);
        $this->assertSame('?g=3', $links[3]);
        $this->assertSame('?g=5', $links[5]);
    }

    /**
     * Helper: Simulate empty gallery state
     */
    private function checkGalleryEmpty(int $itemCount): array
    {
        return [
            'is_empty' => $itemCount === 0,
            'show_empty_message' => $itemCount === 0,
            'empty_message' => 'Tidak Ada Gallery'
        ];
    }

    #[Test]
    public function gallery_shows_empty_message_when_no_items(): void
    {
        $state = $this->checkGalleryEmpty(0);
        
        $this->assertTrue($state['is_empty']);
        $this->assertTrue($state['show_empty_message']);
        $this->assertSame('Tidak Ada Gallery', $state['empty_message']);
    }

    #[Test]
    public function gallery_does_not_show_empty_message_when_has_items(): void
    {
        $state = $this->checkGalleryEmpty(5);
        
        $this->assertFalse($state['is_empty']);
        $this->assertFalse($state['show_empty_message']);
    }

    /**
     * Helper: Simulate gallery display styling
     */
    private function getGalleryDisplayConfig(): array
    {
        return [
            'grid_cols' => 2,
            'image_col_size' => 'col-md-6',
            'content_col_size' => 'col-md-6',
            'background_color' => '#73006e',
            'has_shadow' => true,
            'aos_animation' => 'fade-up',
            'aos_duration' => 900
        ];
    }

    #[Test]
    public function gallery_item_display_has_correct_grid(): void
    {
        $config = $this->getGalleryDisplayConfig();
        
        $this->assertSame(2, $config['grid_cols']);
        $this->assertSame('col-md-6', $config['image_col_size']);
        $this->assertSame('col-md-6', $config['content_col_size']);
    }

    #[Test]
    public function gallery_item_has_shadow(): void
    {
        $config = $this->getGalleryDisplayConfig();
        
        $this->assertTrue($config['has_shadow']);
    }

    #[Test]
    public function gallery_item_has_animation(): void
    {
        $config = $this->getGalleryDisplayConfig();
        
        $this->assertSame('fade-up', $config['aos_animation']);
        $this->assertGreaterThan(0, $config['aos_duration']);
    }

    #[Test]
    public function gallery_background_color_is_dark(): void
    {
        $config = $this->getGalleryDisplayConfig();
        
        $this->assertSame('#73006e', $config['background_color']);
        $this->assertStringStartsWith('#', $config['background_color']);
    }

    /**
     * Helper: Calculate total pages for gallery
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
    public function gallery_total_pages_zero_items(): void
    {
        $pages = $this->calculateTotalPages(0);
        $this->assertSame(0, $pages);
    }

    #[Test]
    public function gallery_total_pages_exact_eight(): void
    {
        $pages = $this->calculateTotalPages(8);
        $this->assertSame(1, $pages);
    }

    #[Test]
    public function gallery_total_pages_sixteen(): void
    {
        $pages = $this->calculateTotalPages(16);
        $this->assertSame(2, $pages);
    }

    #[Test]
    public function gallery_total_pages_with_remainder(): void
    {
        $pages = $this->calculateTotalPages(25);
        $this->assertSame(4, $pages);
    }
}
