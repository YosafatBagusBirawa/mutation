<?php
declare(strict_types=1);

class Utils
{
    public static function calculatePagination(int $currentPage, int $itemsPerPage = 8): array
    {
        if ($itemsPerPage <= 0) {
            $itemsPerPage = 8;
        }

        if ($currentPage <= 0) {
            $page = 1;
        } else {
            $page = $currentPage;
        }

        $position = ($page - 1) * $itemsPerPage;

        return [
            'page' => $page,
            'position' => $position,
            'itemsPerPage' => $itemsPerPage
        ];
    }

    public static function buildPaginationUrl(int $page): string
    {
        if ($page <= 0) {
            $page = 1;
        }
        return '?id=' . $page;
    }

    public static function isFirstPage(int $page): bool
    {
        return $page <= 1;
    }

    public static function hasNextPage(int $page, int $totalPages): bool
    {
        return $page < $totalPages;
    }

    public static function calculateTotalPages(int $totalItems, int $itemsPerPage = 8): int
    {
        if ($totalItems === 0) {
            return 0;
        }

        if ($itemsPerPage === 0) {
            return 1;
        }

        return (int)ceil($totalItems / $itemsPerPage);
    }
}
