<?php
declare(strict_types=1);

/**
 * Utility functions extracted from index.php, data-penduduk.php, datadesa.php
 * for testability and reusability.
 */

class Utils
{
    /**
     * Generate a URL-safe slug from a title
     * Used in index.php (berita links) and datadesa.php (data desa links)
     */
    public static function generateSlug(string $title): string
    {
        return strtolower(str_replace(' ', '-', $title));
    }

    /**
     * Calculate pagination details
     * Returns array with position and page number
     */
    public static function calculatePagination(int $currentPage, int $itemsPerPage): array
    {
        if ($currentPage < 1) {
            $currentPage = 1;
        }

        $position = ($currentPage - 1) * $itemsPerPage;

        return [
            'page' => $currentPage,
            'position' => $position,
            'itemsPerPage' => $itemsPerPage
        ];
    }

    /**
     * Format date for display (day month year)
     * Used in index.php for displaying article dates
     */
    public static function formatDisplayDate(string $dateString): string
    {
        return date('d F Y', strtotime($dateString));
    }

    /**
     * Extract day from date string
     * Used in index.php for pengumuman card display
     */
    public static function extractDay(string $dateString): string
    {
        return date('d', strtotime($dateString));
    }

    /**
     * Extract month from date string
     * Used in index.php for pengumuman card display
     */
    public static function extractMonth(string $dateString): string
    {
        return date('F', strtotime($dateString));
    }

    /**
     * Determine if current page is first page
     */
    public static function isFirstPage(int $currentPage): bool
    {
        return $currentPage <= 1;
    }

    /**
     * Check if page navigation should show next button
     */
    public static function hasNextPage(int $currentPage, int $totalPages): bool
    {
        return $currentPage < $totalPages;
    }

    /**
     * Build pagination URL parameter
     * Used in datadesa.php pagination
     */
    public static function buildPaginationUrl(int $pageNumber): string
    {
        if ($pageNumber < 1) {
            $pageNumber = 1;
        }
        return '?id=' . $pageNumber;
    }

    /**
     * Calculate total number of pages
     */
    public static function calculateTotalPages(int $totalItems, int $itemsPerPage): int
    {
        if ($itemsPerPage <= 0) {
            return 1;
        }
        return (int)ceil($totalItems / $itemsPerPage);
    }

    /**
     * Truncate text and add ellipsis
     * Used for article previews
     */
    public static function truncateText(string $text, int $length = 250): string
    {
        $stripped = strip_tags($text);
        if (strlen($stripped) > $length) {
            return substr($stripped, 0, $length) . '...';
        }
        return $stripped;
    }

    /**
     * Generate comment count badge label
     */
    public static function getCommentCountLabel(int $count): string
    {
        return (string)$count;
    }
}
