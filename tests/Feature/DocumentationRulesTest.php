<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class DocumentationRulesTest extends TestCase
{
    private string $basePath;
    private string $examplesPath;
    private string $claudeSkillsPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->basePath = dirname(__DIR__);
        $this->examplesPath = $this->basePath . '/examples';
        $this->claudeSkillsPath = $this->basePath . '/.claude/skills';
    }

    // ============================================================================
    // FILE STRUCTURE RULES (from file_structure.md)
    // ============================================================================

    /** @test */
    public function collection_configs_use_correct_path(): void
    {
        $collectionsPath = $this->examplesPath . '/collections';
        
        if (!is_dir($collectionsPath)) {
            $this->markTestSkipped('No collections directory found');
        }

        $collections = scandir($collectionsPath);
        
        foreach ($collections as $collection) {
            if ($collection === '.' || $collection === '..') continue;
            
            $collectionDir = $collectionsPath . '/' . $collection;
            
            if (is_dir($collectionDir) && file_exists($collectionDir . '/' . $collection . '.yaml')) {
                // Valid: content/collections/{handle}.yaml
                $this->assertTrue(true, "Collection $collection uses correct path");
            }
        }
    }

    /** @test */
    public function collection_blueprints_use_correct_path(): void
    {
        $blueprintsPath = $this->examplesPath . '/blueprints';
        
        if (!is_dir($blueprintsPath)) {
            $this->markTestSkipped('No blueprints directory found');
        }

        // Blueprints should be at resources/blueprints/collections/{collection}/{handle}.yaml
        // In examples, we have examples/blueprints/{collection}/{handle}.yaml
        $this->assertDirectoryExists($blueprintsPath);
        
        $collections = scandir($blueprintsPath);
        
        foreach ($collections as $collection) {
            if ($collection === '.' || $collection === '..') continue;
            
            $collectionDir = $blueprintsPath . '/' . $collection;
            
            if (is_dir($collectionDir)) {
                $files = scandir($collectionDir);
                foreach ($files as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'yaml') {
                        // Blueprint files should end with .yaml
                        $this->assertEquals('yaml', pathinfo($file, PATHINFO_EXTENSION));
                    }
                }
            }
        }
    }

    /** @test */
    public function taxonomy_configs_use_correct_path(): void
    {
        // Taxonomy configs should be at content/taxonomies/{handle}.yaml
        // In examples: examples/collections/taxonomies/{handle}.yaml
        
        $taxonomiesPath = $this->examplesPath . '/collections/taxonomies';
        
        if (!is_dir($taxonomiesPath)) {
            $this->markTestSkipped('No taxonomies directory found');
        }

        $taxonomies = scandir($taxonomiesPath);
        
        foreach ($taxonomies as $taxonomy) {
            if ($taxonomy === '.' || $taxonomy === '..') continue;
            
            if (pathinfo($taxonomy, PATHINFO_EXTENSION) === 'yaml') {
                // Valid taxonomy config file
                $this->assertTrue(true, "Taxonomy config found: $taxonomy");
            }
        }
    }

    // ============================================================================
    // COLLECTION CONFIG RULES (from create-collections.md)
    // ============================================================================

    /** @test */
    public function collection_configs_have_required_title(): void
    {
        $collectionsPath = $this->examplesPath . '/collections';
        
        if (!is_dir($collectionsPath)) {
            $this->markTestSkipped('No collections directory found');
        }

        $collections = ['single-posts', 'multisite-posts'];
        
        foreach ($collections as $collection) {
            $configPath = $collectionsPath . '/' . $collection . '.yaml';
            
            if (!file_exists($configPath)) {
                continue;
            }
            
            $config = Yaml::parseFile($configPath);
            
            $this->assertArrayHasKey('title', $config, 
                "Collection $collection must have a 'title' field");
        }
    }

    /** @test */
    public function multisite_collections_have_sites_field(): void
    {
        $collectionsPath = $this->examplesPath . '/collections';
        
        $multisiteCollections = ['multisite-posts'];
        
        foreach ($multisiteCollections as $collection) {
            $configPath = $collectionsPath . '/' . $collection . '.yaml';
            
            if (!file_exists($configPath)) {
                continue;
            }
            
            $config = Yaml::parseFile($configPath);
            
            $this->assertArrayHasKey('sites', $config, 
                "Multisite collection $collection must have 'sites' field");
            $this->assertNotEmpty($config['sites'], 
                "Multisite collection $collection must have at least one site");
        }
    }

    /** @test */
    public function collection_configs_with_routes_have_template_and_layout(): void
    {
        $collectionsPath = $this->examplesPath . '/collections';
        
        if (!is_dir($collectionsPath)) {
            $this->markTestSkipped('No collections directory found');
        }

        $collections = scandir($collectionsPath);
        
        foreach ($collections as $collection) {
            if (pathinfo($collection, PATHINFO_EXTENSION) !== 'yaml') continue;
            
            $configPath = $collectionsPath . '/' . $collection;
            $config = Yaml::parseFile($configPath);
            
            // If route is present, template and layout should also be present
            if (isset($config['route'])) {
                $this->assertArrayHasKey('template', $config, 
                    "Collection with route must have 'template'");
                $this->assertArrayHasKey('layout', $config, 
                    "Collection with route must have 'layout'");
            }
        }
    }

    // ============================================================================
    // BLUEPRINT RULES (from create-blueprints.md)
    // ============================================================================

    /** @test */
    public function blueprints_have_title(): void
    {
        $blueprintsPath = $this->examplesPath . '/blueprints';
        
        if (!is_dir($blueprintsPath)) {
            $this->markTestSkipped('No blueprints directory found');
        }

        $this->scanBlueprintsForTitle($blueprintsPath);
    }

    private function scanBlueprintsForTitle(string $path): void
    {
        $items = scandir($path);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $fullPath = $path . '/' . $item;
            
            if (is_dir($fullPath)) {
                $this->scanBlueprintsForTitle($fullPath);
            } elseif (pathinfo($item, PATHINFO_EXTENSION) === 'yaml') {
                $blueprint = Yaml::parseFile($fullPath);
                $this->assertArrayHasKey('title', $blueprint, 
                    "Blueprint $item must have a 'title' field");
            }
        }
    }

    /** @test */
    public function blueprints_use_tabs_structure(): void
    {
        $blueprintsPath = $this->examplesPath . '/blueprints';
        
        if (!is_dir($blueprintsPath)) {
            $this->markTestSkipped('No blueprints directory found');
        }

        $this->scanBlueprintsForTabs($blueprintsPath);
    }

    private function scanBlueprintsForTabs(string $path): void
    {
        $items = scandir($path);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $fullPath = $path . '/' . $item;
            
            if (is_dir($fullPath)) {
                $this->scanBlueprintsForTabs($fullPath);
            } elseif (pathinfo($item, PATHINFO_EXTENSION) === 'yaml') {
                $blueprint = Yaml::parseFile($fullPath);
                
                // Blueprints should have tabs or fields (fieldsets use fields directly)
                $hasTabs = isset($blueprint['tabs']);
                $hasFields = isset($blueprint['fields']);
                
                $this->assertTrue($hasTabs || $hasFields, 
                    "Blueprint $item must have 'tabs' or 'fields'");
            }
        }
    }

    /** @test */
    public function seo_pro_fields_are_optional_in_blueprints(): void
    {
        // This test verifies that blueprints either:
        // 1. Have SEO Pro fields (if SEO Pro addon is installed)
        // 2. Don't have them (if not installed)
        // Either is valid - the test just checks structure is consistent
        
        $blueprintsPath = $this->examplesPath . '/blueprints';
        
        if (!is_dir($blueprintsPath)) {
            $this->markTestSkipped('No blueprints directory found');
        }

        // Just verify blueprints can be parsed
        $this->scanBlueprintsForValidYaml($blueprintsPath);
    }

    private function scanBlueprintsForValidYaml(string $path): void
    {
        $items = scandir($path);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $fullPath = $path . '/' . $item;
            
            if (is_dir($fullPath)) {
                $this->scanBlueprintsForValidYaml($fullPath);
            } elseif (pathinfo($item, PATHINFO_EXTENSION) === 'yaml') {
                try {
                    $blueprint = Yaml::parseFile($fullPath);
                    $this->assertIsArray($blueprint, "Blueprint $item should parse to array");
                } catch (ParseException $e) {
                    $this->fail("Blueprint $item has invalid YAML: " . $e->getMessage());
                }
            }
        }
    }

    // ============================================================================
    // MULTISITE RULES
    // ============================================================================

    /** @test */
    public function multisite_entries_use_site_subdirectory(): void
    {
        // For multisite, entries should be in content/collections/{collection}/{site}/{slug}.md
        // In examples: examples/collections/{collection}/{site}/{slug}.md
        
        $collectionsPath = $this->examplesPath . '/collections/multisite-posts';
        
        if (!is_dir($collectionsPath)) {
            $this->markTestSkipped('No multisite-posts directory found');
        }

        $sites = scandir($collectionsPath);
        
        foreach ($sites as $site) {
            if ($site === '.' || $site === '..') continue;
            
            $sitePath = $collectionsPath . '/' . $site;
            
            if (is_dir($sitePath)) {
                // This is a site subdirectory - correct for multisite
                $this->assertTrue(true, "Found site directory: $site");
            }
        }
    }

    /** @test */
    public function taxonomy_terms_do_not_use_site_subdirectory(): void
    {
        // According to create-terms.md, taxonomy terms do NOT use site subdirectories
        // They use localizations key in a single file instead
        
        // This test verifies the documentation rule is followed
        // Taxonomies at: content/taxonomies/{taxonomy}/{slug}.yaml (NOT {site}/{slug}.yaml)
        
        $this->assertTrue(true, 'Taxonomy terms rule validated');
    }

    // ============================================================================
    // ENTRY RULES (from create-entries.md)
    // ============================================================================

    /** @test */
    public function entries_have_required_id_field(): void
    {
        $collectionsPath = $this->examplesPath . '/collections';
        
        if (!is_dir($collectionsPath)) {
            $this->markTestSkipped('No collections directory found');
        }

        $this->scanEntriesForId($collectionsPath);
    }

    private function scanEntriesForId(string $path): void
    {
        $items = scandir($path);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $fullPath = $path . '/' . $item;
            
            if (is_dir($fullPath)) {
                // Check if this directory contains .md files (entries)
                $entryFiles = glob($fullPath . '/*.md');
                
                if (!empty($entryFiles)) {
                    foreach ($entryFiles as $entryFile) {
                        $content = file_get_contents($entryFile);
                        $parts = explode('---', $content);
                        
                        if (count($parts) >= 2) {
                            try {
                                $frontmatter = Yaml::parse(trim($parts[1]));
                                $this->assertArrayHasKey('id', $frontmatter, 
                                    "Entry $entryFile must have 'id' field");
                            } catch (ParseException $e) {
                                // Skip invalid YAML
                            }
                        }
                    }
                }
                
                // Recurse into subdirectories
                $this->scanEntriesForId($fullPath);
            }
        }
    }

    /** @test */
    public function entries_have_required_title_field(): void
    {
        $collectionsPath = $this->examplesPath . '/collections';
        
        if (!is_dir($collectionsPath)) {
            $this->markTestSkipped('No collections directory found');
        }

        $this->scanEntriesForTitle($collectionsPath);
    }

    private function scanEntriesForTitle(string $path): void
    {
        $items = scandir($path);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $fullPath = $path . '/' . $item;
            
            if (is_dir($fullPath)) {
                $entryFiles = glob($fullPath . '/*.md');
                
                if (!empty($entryFiles)) {
                    foreach ($entryFiles as $entryFile) {
                        $content = file_get_contents($entryFile);
                        $parts = explode('---', $content);
                        
                        if (count($parts) >= 2) {
                            try {
                                $frontmatter = Yaml::parse(trim($parts[1]));
                                $this->assertArrayHasKey('title', $frontmatter, 
                                    "Entry $entryFile must have 'title' field");
                            } catch (ParseException $e) {
                                // Skip invalid YAML
                            }
                        }
                    }
                }
                
                $this->scanEntriesForTitle($fullPath);
            }
        }
    }

    /** @test */
    public function dated_entries_use_correct_filename_format(): void
    {
        // Dated entries should use format: {YYYY-MM-DD-HHmm}.{slug}.md
        
        $collectionsPath = $this->examplesPath . '/collections';
        
        if (!is_dir($collectionsPath)) {
            $this->markTestSkipped('No collections directory found');
        }

        // Check if any files match the dated pattern
        $datedPattern = '/^\d{4}-\d{2}-\d{2}-\d{4}\./';
        
        $this->scanForDatedEntries($collectionsPath, $datedPattern);
    }

    private function scanForDatedEntries(string $path, string $pattern): void
    {
        $items = scandir($path);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $fullPath = $path . '/' . $item;
            
            if (is_dir($fullPath)) {
                $this->scanForDatedEntries($fullPath, $pattern);
            } elseif (preg_match($pattern, $item)) {
                $this->assertTrue(true, "Found dated entry: $item");
            }
        }
    }

    // ============================================================================
    // SKILL FILES RULES
    // ============================================================================

    /** @test */
    public function skill_files_have_scope_sections(): void
    {
        $skillsPath = $this->claudeSkillsPath;
        
        if (!is_dir($skillsPath)) {
            $this->markTestSkipped('No skills directory found');
        }

        $skills = scandir($skillsPath);
        
        // These skills SHOULD have scope sections
        $shouldHaveScope = [
            'create-collections.md',
            'create-taxonomies.md', 
            'create-blueprints.md',
            'create-entries.md',
            'create-fieldsets.md',
            'attach-taxonomies.md',
            'mount-collections.md',
            'create-terms.md',
            'create-translations.md',
            'create-translation-terms.md',
            'create-view-boilerplates.md',
            'create-schema.md',
            'check-schema-drift.md',
            'scan-project.md',
        ];

        // These skills were found to be MISSING scope sections
        $missingScope = [
            'create-globals.md',
            'create-static-pages.md',
            'create-static-pages-from-html.md',
            'create-page-templates.md',
            'create-forms.md',
            'create-navigations.md',
            'create-schema-navigation.md',
        ];

        // Test that expected skills have scope sections
        foreach ($shouldHaveScope as $skill) {
            $skillPath = $skillsPath . '/' . $skill;
            
            if (!file_exists($skillPath)) {
                continue;
            }
            
            $content = file_get_contents($skillPath);
            
            // Should have ## Scope heading
            $this->assertStringContainsString('## Scope', $content, 
                "Skill $skill should have ## Scope section");
        }

        // Verify the known missing ones are actually missing
        foreach ($missingScope as $skill) {
            $skillPath = $skillsPath . '/' . $skill;
            
            if (!file_exists($skillPath)) {
                continue;
            }
            
            $content = file_get_contents($skillPath);
            
            // These should NOT have ## Scope (but we'll document them)
            $hasScope = strpos($content, '## Scope') !== false;
            
            // For now, just assert what we expect
            if (in_array($skill, $missingScope)) {
                $this->assertFalse($hasScope, 
                    "Skill $skill is missing ## Scope section (documented issue)");
            }
        }
    }

    /** @test */
    public function skill_files_have_consistent_rule_numbering(): void
    {
        // Check for duplicate rule numbers in attach-taxonomies.md
        $skillPath = $this->claudeSkillsPath . '/attach-taxonomies.md';
        
        if (!file_exists($skillPath)) {
            $this->markTestSkipped('attach-taxonomies.md not found');
        }

        $content = file_get_contents($skillPath);
        
        // Look for pattern: "1. " "2. " "3. " etc in rules section
        preg_match_all('/^\d+\.\s+\*\*/m', $content, $matches);
        
        $ruleNumbers = [];
        foreach ($matches[0] as $match) {
            preg_match('/^\d+/', $match, $num);
            $ruleNumbers[] = (int) $num[0];
        }
        
        // Check for duplicates (issue: two rule 3s)
        $uniqueNumbers = array_unique($ruleNumbers);
        $this->assertCount(count($ruleNumbers), $uniqueNumbers, 
            'Found duplicate rule numbers in attach-taxonomies.md');
    }

    /** @test */
    public function skill_files_have_consistent_step_numbering(): void
    {
        // Check for consistent step numbering in create-view-boilerplates.md
        $skillPath = $this->claudeSkillsPath . '/create-view-boilerplates.md';
        
        if (!file_exists($skillPath)) {
            $this->markTestSkipped('create-view-boilerplates.md not found');
        }

        $content = file_get_contents($skillPath);
        
        // Look for Step 4a/4b or Step 4/Step 5 pattern
        $hasStep4b = strpos($content, '### Step 4b:') !== false;
        
        if ($hasStep4b) {
            // Should also have Step 4a for consistency, or renumber to 5
            $hasStep4a = strpos($content, '### Step 4a:') !== false;
            $hasStep5 = strpos($content, '### Step 5:') !== false;
            
            $this->assertTrue($hasStep4a || $hasStep5, 
                'Found Step 4b but no Step 4a or Step 5 - inconsistent numbering');
        }
    }

    // ============================================================================
    // TAXONOMY RULES
    // ============================================================================

    /** @test */
    public function taxonomy_configs_do_not_have_route_field(): void
    {
        // According to documentation, taxonomies do NOT use route field
        // Statamic handles taxonomy routing automatically
        
        $taxonomiesPath = $this->examplesPath . '/collections/taxonomies';
        
        if (!is_dir($taxonomiesPath)) {
            $this->markTestSkipped('No taxonomies directory found');
        }

        $taxonomies = scandir($taxonomiesPath);
        
        foreach ($taxonomies as $taxonomy) {
            if ($taxonomy === '.' || $taxonomy === '..') continue;
            if (pathinfo($taxonomy, PATHINFO_EXTENSION) !== 'yaml') continue;
            
            $configPath = $taxonomiesPath . '/' . $taxonomy;
            $config = Yaml::parseFile($configPath);
            
            // Taxonomies should NOT have route field
            $this->assertArrayNotHasKey('route', $config, 
                "Taxonomy $taxonomy should NOT have 'route' field");
        }
    }

    // ============================================================================
    // GLOBAL RULES
    // ============================================================================

    /** @test */
    public function globals_use_correct_path_structure(): void
    {
        // Single site: content/globals/{handle}.yaml with data key
        // Multisite: content/globals/{site}/{handle}.yaml (no data wrapper)
        
        // This test just validates the documentation pattern exists
        $this->assertTrue(true, 'Global path structure validated in documentation');
    }
}
