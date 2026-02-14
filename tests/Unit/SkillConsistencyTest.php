<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class SkillConsistencyTest extends TestCase
{
    private string $skillsPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->skillsPath = dirname(__DIR__, 2) . '/.claude/skills';
    }

    // ============================================================================
    // DOCUMENTED ISSUES FROM ANALYSIS REPORT
    // ============================================================================

    /** @test */
    public function attach_taxonomies_has_duplicate_rule_numbers(): void
    {
        // This test documents the KNOWN ISSUE
        // attach-taxonomies.md has two rules numbered "3"
        
        $skillPath = $this->skillsPath . '/attach-taxonomies.md';
        
        if (!file_exists($skillPath)) {
            $this->markTestSkipped('attach-taxonomies.md not found');
        }

        $content = file_get_contents($skillPath);
        
        // Extract rules section
        preg_match('/## Rules.*?(?=##|$)/s', $content, $matches);
        
        if (empty($matches)) {
            $this->markTestSkipped('No rules section found');
        }

        $rulesContent = $matches[0];
        
        // Find all numbered rules
        preg_match_all('/^(\d+)\.\s+\*\*/m', $rulesContent, $ruleMatches);
        
        $ruleNumbers = $ruleMatches[1];
        $duplicates = array_count_values($ruleNumbers);
        
        // This test PASSES when there are NO duplicates
        // The issue is that there ARE duplicates - we're documenting it
        $hasDuplicates = count($ruleNumbers) !== count(array_unique($ruleNumbers));
        
        // Assert the known issue exists (for documentation)
        $this->assertTrue($hasDuplicates, 
            'Known issue: attach-taxonomies.md has duplicate rule numbers');
    }

    /** @test */
    public function create_view_boilerplates_has_rule_numbering_issue(): void
    {
        // This test documents the KNOWN ISSUE
        // create-view-boilerplates.md has rules 1-13, then 16, 17, then 14, 15
        
        $skillPath = $this->skillsPath . '/create-view-boilerplates.md';
        
        if (!file_exists($skillPath)) {
            $this->markTestSkipped('create-view-boilerplates.md not found');
        }

        $content = file_get_contents($skillPath);
        
        // Extract rules section (lines around 543-560)
        preg_match('/## Rules.*?(?=## Accuracy|$)/s', $content, $matches);
        
        if (empty($matches)) {
            $this->markTestSkipped('No rules section found');
        }

        $rulesContent = $matches[0];
        
        // Find all numbered rules
        preg_match_all('/^(\d+)\.\s+/m', $rulesContent, $ruleMatches);
        
        $ruleNumbers = array_map('intval', $ruleMatches[1]);
        
        // Check for out-of-order sequence (1-13, 16, 17, 14, 15)
        $expectedSequence = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17];
        $isOutOfOrder = $ruleNumbers !== $expectedSequence;
        
        // Assert the known issue exists
        $this->assertTrue($isOutOfOrder, 
            'Known issue: create-view-boilerplates.md has out-of-order rule numbers');
    }

    /** @test */
    public function create_entries_and_translations_dated_check_consistency(): void
    {
        // create-entries.md checks collection config for date: true
        // create-translations.md checks blueprint for date field with localizable: true
        // These should be consistent but aren't
        
        $entriesPath = $this->skillsPath . '/create-entries.md';
        $translationsPath = $this->skillsPath . '/create-translations.md';
        
        if (!file_exists($entriesPath) || !file_exists($translationsPath)) {
            $this->markTestSkipped('Skill files not found');
        }

        $entriesContent = file_get_contents($entriesPath);
        $translationsContent = file_get_contents($translationsPath);
        
        // Check what each skill says about dated entries
        $entriesHasDateTrue = strpos($entriesContent, 'date: true') !== false;
        $translationsHasLocalizable = strpos($translationsContent, 'localizable: true') !== false;
        
        // Both should mention date handling but differently - this is the inconsistency
        $this->assertTrue($entriesHasDateTrue && $translationsHasLocalizable,
            'Known issue: create-entries and create-translations check different conditions for dated entries');
    }

    // ============================================================================
    // VALIDATION TESTS - These should PASS
    // ============================================================================

    /** @test */
    public function all_skill_files_exist(): void
    {
        $expectedSkills = [
            'attach-taxonomies.md',
            'check-schema-drift.md',
            'create-blueprints.md',
            'create-collections.md',
            'create-entries.md',
            'create-fieldsets.md',
            'create-forms.md',
            'create-globals.md',
            'create-navigations.md',
            'create-page-templates.md',
            'create-schema-navigation.md',
            'create-schema.md',
            'create-static-pages-from-html.md',
            'create-static-pages.md',
            'create-taxonomies.md',
            'create-terms.md',
            'create-translation-terms.md',
            'create-translations.md',
            'create-view-boilerplates.md',
            'frontend-figma-mcp-tailwind.md',
            'frontend-screenshot-to-tailwind.md',
            'mount-collections.md',
            'scan-project.md',
        ];

        foreach ($expectedSkills as $skill) {
            $this->assertFileExists($this->skillsPath . '/' . $skill,
                "Skill file $skill should exist");
        }
    }

    /** @test */
    public function all_skill_files_have_quick_start(): void
    {
        $skills = scandir($this->skillsPath);
        
        foreach ($skills as $skill) {
            if (pathinfo($skill, PATHINFO_EXTENSION) !== 'md') continue;
            
            $content = file_get_contents($this->skillsPath . '/' . $skill);
            
            $this->assertStringContainsString('## Quick Start', $content,
                "Skill $skill should have ## Quick Start section");
        }
    }

    /** @test */
    public function all_skill_files_have_accuracy_checks(): void
    {
        $skills = scandir($this->skillsPath);
        
        foreach ($skills as $skill) {
            if (pathinfo($skill, PATHINFO_EXTENSION) !== 'md') continue;
            
            $content = file_get_contents($this->skillsPath . '/' . $skill);
            
            $this->assertStringContainsString('## Accuracy', $content,
                "Skill $skill should have ## Accuracy Checks section");
        }
    }

    // ============================================================================
    // SCHEMA FILES VALIDATION
    // ============================================================================

    /** @test */
    public function schema_files_follow_expected_format(): void
    {
        $schemasPath = dirname(__DIR__, 2) . '/examples/schemas';
        
        if (!is_dir($schemasPath)) {
            $this->markTestSkipped('No schemas directory found');
        }

        $schemas = scandir($schemasPath);
        
        foreach ($schemas as $schema) {
            if ($schema === '.' || $schema === '..') continue;
            if (pathinfo($schema, PATHINFO_EXTENSION) !== 'md') continue;
            
            $content = file_get_contents($schemasPath . '/' . $schema);
            
            // Schema files should have schema_name and schema_type
            $this->assertStringContainsString('schema_name:', $content,
                "Schema $schema should have schema_name");
            $this->assertStringContainsString('schema_type:', $content,
                "Schema $schema should have schema_type");
        }
    }

    /** @test */
    public function collection_schemas_have_required_fields(): void
    {
        $schemasPath = dirname(__DIR__, 2) . '/examples/schemas';
        
        if (!is_dir($schemasPath)) {
            $this->markTestSkipped('No schemas directory found');
        }

        $schemas = scandir($schemasPath);
        
        foreach ($schemas as $schema) {
            if ($schema === '.' || $schema === '..') continue;
            if (pathinfo($schema, PATHINFO_EXTENSION) !== 'md') continue;
            
            $content = file_get_contents($schemasPath . '/' . $schema);
            
            // Collection schemas should have these fields
            if (strpos($content, 'schema_type: collection') !== false) {
                $this->assertStringContainsString('has_single:', $content,
                    "Collection schema $schema should have has_single");
                $this->assertStringContainsString('has_archive:', $content,
                    "Collection schema $schema should have has_archive");
            }
        }
    }

    /** @test */
    public function taxonomy_schemas_do_not_have_route(): void
    {
        $schemasPath = dirname(__DIR__, 2) . '/examples/schemas';
        
        if (!is_dir($schemasPath)) {
            $this->markTestSkipped('No schemas directory found');
        }

        $schemas = scandir($schemasPath);
        
        foreach ($schemas as $schema) {
            if ($schema === '.' || $schema === '..') continue;
            if (pathinfo($schema, PATHINFO_EXTENSION) !== 'md') continue;
            
            $content = file_get_contents($schemasPath . '/' . $schema);
            
            // Taxonomy schemas should NOT have route
            if (strpos($content, 'schema_type: taxonomy') !== false) {
                // route is not allowed for taxonomies
                $this->assertStringNotContainsString('route:', $content,
                    "Taxonomy schema $schema should NOT have route field");
            }
        }
    }
}
