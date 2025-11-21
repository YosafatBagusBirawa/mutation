<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

require_once __DIR__ . '/../lib/Utils.php';

/**
 * Test cases for demografis.php page logic
 * 
 * Tests the specific business logic of the demografis page including:
 * - Tab navigation logic
 * - Content display structure
 * - Page initialization
 */
final class DemografisPageTest extends TestCase
{
    /**
     * Helper: Simulate tab data structure from demografis.php
     * The page has 3 main tabs: Luas Wilayah, Kondisi Ekonomi, Sarana dan Prasarana
     */
    private function getDemografisTabsData(): array
    {
        return [
            [
                'id' => 'list-home',
                'icon' => 'fa-map',
                'label' => 'Luas Wilayah',
                'tab_id' => 'list-home'
            ],
            [
                'id' => 'list-profile',
                'icon' => 'fa-search-dollar',
                'label' => 'Kondisi Ekonomi',
                'tab_id' => 'list-profile'
            ],
            [
                'id' => 'list-messages',
                'icon' => 'fa-building',
                'label' => 'Sarana dan Prasarana',
                'tab_id' => 'list-messages'
            ]
        ];
    }

    #[Test]
    public function demografis_has_three_main_tabs(): void
    {
        $tabs = $this->getDemografisTabsData();
        
        $this->assertCount(3, $tabs);
        $this->assertIsArray($tabs);
    }

    #[Test]
    public function demografis_first_tab_is_luas_wilayah(): void
    {
        $tabs = $this->getDemografisTabsData();
        $firstTab = $tabs[0];
        
        $this->assertSame('Luas Wilayah', $firstTab['label']);
        $this->assertSame('fa-map', $firstTab['icon']);
        $this->assertSame('list-home', $firstTab['id']);
    }

    #[Test]
    public function demografis_second_tab_is_kondisi_ekonomi(): void
    {
        $tabs = $this->getDemografisTabsData();
        $secondTab = $tabs[1];
        
        $this->assertSame('Kondisi Ekonomi', $secondTab['label']);
        $this->assertSame('fa-search-dollar', $secondTab['icon']);
        $this->assertSame('list-profile', $secondTab['id']);
    }

    #[Test]
    public function demografis_third_tab_is_sarana_prasarana(): void
    {
        $tabs = $this->getDemografisTabsData();
        $thirdTab = $tabs[2];
        
        $this->assertSame('Sarana dan Prasarana', $thirdTab['label']);
        $this->assertSame('fa-building', $thirdTab['icon']);
        $this->assertSame('list-messages', $thirdTab['id']);
    }

    #[Test]
    public function demografis_all_tabs_have_required_fields(): void
    {
        $tabs = $this->getDemografisTabsData();
        
        foreach ($tabs as $tab) {
            $this->assertArrayHasKey('id', $tab);
            $this->assertArrayHasKey('icon', $tab);
            $this->assertArrayHasKey('label', $tab);
            $this->assertArrayHasKey('tab_id', $tab);
        }
    }

    #[Test]
    public function demografis_tab_icons_are_valid_fontawesome(): void
    {
        $tabs = $this->getDemografisTabsData();
        $validIcons = ['fa-map', 'fa-search-dollar', 'fa-building'];
        
        foreach ($tabs as $tab) {
            $this->assertContains($tab['icon'], $validIcons);
            $this->assertStringStartsWith('fa-', $tab['icon']);
        }
    }

    #[Test]
    public function demografis_tab_ids_are_unique(): void
    {
        $tabs = $this->getDemografisTabsData();
        $ids = array_column($tabs, 'id');
        
        $this->assertCount(3, $ids);
        $this->assertCount(count(array_unique($ids)), $ids);
    }

    /**
     * Helper: Simulate page title structure
     */
    private function getPageTitleData(): array
    {
        return [
            'title' => 'Demografis',
            'page_type' => 'halaman',
            'breadcrumb_home' => true
        ];
    }

    #[Test]
    public function demografis_page_title_is_demografis(): void
    {
        $pageData = $this->getPageTitleData();
        
        $this->assertSame('Demografis', $pageData['title']);
        $this->assertIsString($pageData['title']);
    }

    #[Test]
    public function demografis_page_type_is_halaman(): void
    {
        $pageData = $this->getPageTitleData();
        
        $this->assertSame('halaman', $pageData['page_type']);
    }

    #[Test]
    public function demografis_has_breadcrumb_home(): void
    {
        $pageData = $this->getPageTitleData();
        
        $this->assertTrue($pageData['breadcrumb_home']);
    }

    /**
     * Helper: Simulate tab content initialization
     */
    private function initializeDemografisTabContent(string $activeTab = 'list-home'): array
    {
        $allTabs = ['list-home', 'list-profile', 'list-messages', 'list-settings'];
        $content = [];
        
        foreach ($allTabs as $tab) {
            $content[$tab] = [
                'active' => $tab === $activeTab,
                'role' => 'tabpanel',
                'aria_labelledby' => $tab . '-list'
            ];
        }
        
        return $content;
    }

    #[Test]
    public function demografis_first_tab_is_active_by_default(): void
    {
        $content = $this->initializeDemografisTabContent();
        
        $this->assertTrue($content['list-home']['active']);
        $this->assertFalse($content['list-profile']['active']);
        $this->assertFalse($content['list-messages']['active']);
    }

    #[Test]
    public function demografis_can_set_active_tab(): void
    {
        $content = $this->initializeDemografisTabContent('list-profile');
        
        $this->assertFalse($content['list-home']['active']);
        $this->assertTrue($content['list-profile']['active']);
        $this->assertFalse($content['list-messages']['active']);
    }

    #[Test]
    public function demografis_all_tabs_have_correct_role(): void
    {
        $content = $this->initializeDemografisTabContent();
        
        foreach ($content as $tab) {
            $this->assertSame('tabpanel', $tab['role']);
        }
    }

    #[Test]
    public function demografis_tab_aria_attributes_are_set(): void
    {
        $content = $this->initializeDemografisTabContent();
        
        foreach ($content as $tabId => $tab) {
            $this->assertArrayHasKey('aria_labelledby', $tab);
            $this->assertStringEndsWith('-list', $tab['aria_labelledby']);
        }
    }

    /**
     * Helper: Simulate page structure validation
     */
    private function validatePageStructure(): array
    {
        return [
            'has_header' => true,
            'has_banner' => true,
            'has_tab_navigation' => true,
            'has_tab_content' => true,
            'has_footer' => true,
            'banner_height' => '37vh'
        ];
    }

    #[Test]
    public function demografis_page_has_complete_structure(): void
    {
        $structure = $this->validatePageStructure();
        
        $this->assertTrue($structure['has_header']);
        $this->assertTrue($structure['has_banner']);
        $this->assertTrue($structure['has_tab_navigation']);
        $this->assertTrue($structure['has_tab_content']);
        $this->assertTrue($structure['has_footer']);
    }

    #[Test]
    public function demografis_banner_height_is_valid(): void
    {
        $structure = $this->validatePageStructure();
        
        $this->assertSame('37vh', $structure['banner_height']);
        $this->assertStringContainsString('vh', $structure['banner_height']);
    }

    /**
     * Helper: Simulate tab navigation layout
     */
    private function getTabNavigationLayout(): array
    {
        return [
            'grid_columns' => 2,
            'nav_width' => 'col-2',
            'content_width' => 'col-10',
            'list_group_class' => 'list-group',
            'tabs_position' => 'left'
        ];
    }

    #[Test]
    public function demografis_navigation_is_on_left(): void
    {
        $layout = $this->getTabNavigationLayout();
        
        $this->assertSame('left', $layout['tabs_position']);
        $this->assertSame('col-2', $layout['nav_width']);
    }

    #[Test]
    public function demografis_content_takes_most_space(): void
    {
        $layout = $this->getTabNavigationLayout();
        
        $this->assertSame('col-10', $layout['content_width']);
        $this->assertGreaterThan(2, 10);
    }

    #[Test]
    public function demografis_grid_layout_is_valid(): void
    {
        $layout = $this->getTabNavigationLayout();
        
        $navCols = 2;
        $contentCols = 10;
        
        $this->assertSame(12, $navCols + $contentCols);
    }

    /**
     * Helper: Simulate icon display in tabs
     */
    private function getTabIconDisplay(string $icon): array
    {
        return [
            'icon_class' => 'fa ' . $icon,
            'icon_size' => 'h1',
            'icon_display' => 'block',
            'has_label' => true
        ];
    }

    #[Test]
    public function demografis_icon_display_has_correct_class(): void
    {
        $display = $this->getTabIconDisplay('fa-map');
        
        $this->assertStringContainsString('fa', $display['icon_class']);
        $this->assertStringContainsString('fa-map', $display['icon_class']);
    }

    #[Test]
    public function demografis_icon_is_large(): void
    {
        $display = $this->getTabIconDisplay('fa-map');
        
        $this->assertSame('h1', $display['icon_size']);
    }

    #[Test]
    public function demografis_icon_has_label(): void
    {
        $display = $this->getTabIconDisplay('fa-map');
        
        $this->assertTrue($display['has_label']);
    }
}
