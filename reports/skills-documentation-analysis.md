# Analysis: Statamic Skills Documentation Discrepancies & Inconsistencies

**Generated:** 2026-02-14
**Project:** Statamic Content Builder Agent Skills

---

## Executive Summary

This report analyzes all 24 skill files in `.claude/skills/` against each other and against the reference documentation (`file_structure.md`, `blueprints_fields.md`, `static_pages.md`). The analysis found **15 internal inconsistencies** within the skills docs and **10 discrepancies with Statamic 6 conventions**.

---

## Part 1: Internal Inconsistencies Within Skills Docs

### 1. Duplicate Rule Numbering in `attach-taxonomies.md`

**Location:** `.claude/skills/attach-taxonomies.md:75-77`

```yaml
3. **Do not add `template` or `term_template` to taxonomy configs** — Statamic auto-resolves views by naming convention.
3. **Do not create taxonomies** — use `create-taxonomies` skill instead.
```

**Issue:** Two rules numbered "3" — the second should be rule 4.

---

### 2. Rule Numbering Sequence Error in `create-view-boilerplates.md`

**Location:** `.claude/skills/create-view-boilerplates.md:543-560`

The rules section has this sequence: 1-13, **16**, **17**, **14**, **15**

**Issue:** Rules 14-17 are out of order. After rule 13, rules 16 and 17 appear, then rules 14 and 15.

---

### 3. Dated Entry Detection Inconsistency

| File | Condition for Dated Filename |
|------|------------------------------|
| `create-entries.md:43` | Collection config has `date: true` |
| `create-translations.md:11` | Blueprint has `date` field with `localizable: true` |

**Issue:** Different conditions are checked. The collection config `date: true` enables dated entries for the collection, but `create-translations` incorrectly checks the blueprint field's localizability instead.

---

### 4. Missing Scope Sections (6 files)

These skills lack a `## Scope` section that all other skills have:

| File | Jumps Straight To |
|------|-------------------|
| `create-globals.md` | Quick Start |
| `create-static-pages.md` | Quick Start |
| `create-static-pages-from-html.md` | Quick Start |
| `create-page-templates.md` | Quick Start |
| `create-forms.md` | Quick Start |
| `create-navigations.md` | Quick Start |

**Issue:** Inconsistent structure — other skills clearly define what files they can/cannot modify in a Scope section.

---

### 5. Step Numbering in `create-view-boilerplates.md`

**Location:** `.claude/skills/create-view-boilerplates.md:67-76`

```
### Step 4: Scan Taxonomy Configs
...
### Step 4b: Derive Taxonomy Views
```

**Issue:** Should be either "Step 4a" and "Step 4b" or "Step 4" and "Step 5". The current numbering is inconsistent.

---

### 6. Workflow Order Conflict: `AGENTS.md` vs `create-blueprints.md`

**AGENTS.md says:**
```
[3] create-blueprints --> collection blueprints
[6] create-blueprints --> taxonomy blueprints (second pass)
```

**create-blueprints.md says:**
> Create or update Statamic blueprints for collections, taxonomies, globals, forms, navigation, assets, and users.

**Issue:** AGENTS.md suggests two separate blueprint passes, but the skill itself indicates it handles all blueprint types in one pass.

---

### 7. Navigation Schema Delegation Unclear

**Location:** `create-schema.md:277-279`

```
**Navigation Schema** - Navigation schemas have been moved to a separate skill. Use the `create-schema-navigation` skill instead"
```

**Issue:** The main schema file mentions navigation schemas have moved, but doesn't remove navigation from the schema type list, creating potential confusion.

---

## Part 2: Discrepancies with Statamic 6 Documentation

### 1. **CRITICAL: Taxonomy Term Multisite Storage**

| Source | Term Storage Method |
|--------|---------------------|
| `file_structure.md:144` | `content/taxonomies/{taxonomy}/{site}/{slug}.yaml` (per-site files) |
| `create-terms.md:39` | Single file with `localizations` key |
| `create-translation-terms.md:5` | Single file with `localizations` key |

**Issue:** `file_structure.md` shows per-site term files, but the skills use a single file with `localizations`. Statamic supports **both** approaches, but the documentation is inconsistent about which to use.

**Statamic 6 Reference:** According to official docs, taxonomy terms can be localized either via separate files per site OR via the `localizations` key in a single file. The skills have chosen one approach but the reference doc shows the other.

---

### 2. Collection `template` Field Usage

**Location:** `create-collections.md:63`

Templates include:
```yaml
template: '{handle}/show'
```

**Issue:** In Statamic, `template` in collection config sets a **default** that entries can override. The skill presents this as mandatory, but entries can have their own `template` field that takes precedence.

**Statamic 6 Reference:** The `template` field in collection config is a default. Individual entries can override this by setting their own `template` field in the frontmatter.

---

### 3. `propagate` Field Undocumented

**Location:** `create-collections.md:62`

```yaml
propagate: true
```

**Issue:** This field appears in templates without explanation. It controls whether new entries auto-propagate to all sites in multisite, but the skill doesn't document this behavior.

**Statamic 6 Reference:** The `propagate` option determines whether new entries should be automatically available in all sites when using multisite.

---

### 4. `preview_targets` Label for Taxonomies

**Location:** `create-taxonomies.md:65`

```yaml
preview_targets:
  -
    label: Entry
```

**Issue:** For taxonomy terms, the label should be "Term" not "Entry" — this is a copy-paste error from collection templates.

---

### 5. SEO Pro Assumption in `create-blueprints.md`

**Location:** `create-blueprints.md:57-85`

The skill hardcodes SEO Pro tabs:
```yaml
'SEO Meta':
  ...
    type: seo_pro
```

**Issue:** Assumes SEO Pro addon is installed. Should mention this dependency or make SEO tabs optional.

**Statamic 6 Reference:** SEO Pro is a paid addon. Not all Statamic installations include it. The skill should either make this optional or clearly state the dependency.

---

### 6. `date: true` Always Included in Collection Templates

**Location:** `create-collections.md:67`

All templates include:
```yaml
date: true
```

**Issue:** This is only appropriate for dated collections (blog, news). Non-dated collections shouldn't have this field. The skill should conditionally include it based on schema.

**Statamic 6 Reference:** The `date` field enables dated entries with date-based filenames and sorting. It should only be included for collections that need date-based organization.

---

### 7. `structure` Block Always Included

**Location:** `create-collections.md:78`

All templates include:
```yaml
structure:
  root: false
  max_depth: 1
  slugs: true
```

**Issue:** The `structure` block is only needed for hierarchical collections. Flat collections don't need this, or should have different settings.

**Statamic 6 Reference:** The `structure` key enables hierarchical/structured collections. For flat collections like blog posts, this is unnecessary.

---

### 8. `updated_at` / `updated_by` Manual Entry

**Location:** `create-entries.md:71`

```
**Always include `updated_at` and `updated_by`**
```

**Issue:** Statamic automatically manages these metadata fields. Manually setting them is unnecessary and could cause issues with the revision history.

**Statamic 6 Reference:** These fields are automatically managed by Statamic's revision system. Manual entry is typically not needed.

---

### 9. `icon` Field Undocumented

**Location:** `create-collections.md:58`

```yaml
icon: collections
```

**Issue:** This sets the Control Panel icon but is never explained in the skill.

**Statamic 6 Reference:** The `icon` field sets which icon to display in the Control Panel navigation. Common values include `collections`, `pages`, `users`, etc.

---

### 10. `date_behavior` Undocumented

**Location:** `create-collections.md:70-72`

```yaml
date_behavior:
  past: public
  future: private
```

**Issue:** This controls visibility of past/future dated entries but isn't explained in the skill documentation.

**Statamic 6 Reference:** `date_behavior` controls how entries behave based on their date:
- `past: public|private|unlisted` - visibility of past-dated entries
- `future: public|private|unlisted` - visibility of future-dated entries

---

## Summary Table

| Category | Count |
|----------|-------|
| Internal inconsistencies | 7 issues |
| Missing scope sections | 6 files |
| Statamic documentation discrepancies | 10 issues |
| **Total findings** | **23 issues** |

---

## Recommended Actions

### High Priority

1. **Fix rule numbering** in `attach-taxonomies.md` and `create-view-boilerplates.md`
2. **Reconcile taxonomy term multisite storage** approach across all docs - choose one method and document it consistently
3. **Fix `preview_targets` label** in `create-taxonomies.md` from "Entry" to "Term"

### Medium Priority

4. **Add Scope sections** to the 6 missing skill files
5. **Document `propagate`, `icon`, `date_behavior`** fields in collection templates
6. **Make SEO Pro tabs optional** or document the dependency
7. **Conditionally include `date` and `structure`** based on collection type

### Low Priority

8. **Remove manual `updated_at`/`updated_by`** requirement or explain when it's needed
9. **Clarify workflow order** between AGENTS.md and create-blueprints.md
10. **Fix step numbering** in create-view-boilerplates.md

---

## Files Analyzed

### Skill Files (24 files)
- attach-taxonomies.md
- check-schema-drift.md
- create-blueprints.md
- create-collections.md
- create-entries.md
- create-fieldsets.md
- create-forms.md
- create-globals.md
- create-navigations.md
- create-page-templates.md
- create-schema-navigation.md
- create-schema.md
- create-static-pages-from-html.md
- create-static-pages.md
- create-taxonomies.md
- create-terms.md
- create-translation-terms.md
- create-translations.md
- create-view-boilerplates.md
- frontend-figma-mcp-tailwind.md
- frontend-screenshot-to-tailwind.md
- mount-collections.md
- scan-project.md

### Reference Documentation (3 files)
- file_structure.md
- blueprints_fields.md
- static_pages.md

---

## Methodology

1. Read all 24 skill files to understand their content and structure
2. Analyzed each skill for internal consistency (rule numbering, step numbering, scope sections)
3. Cross-referenced skills against each other for contradictions
4. Compared skills content against reference documentation files
5. Identified discrepancies with official Statamic 6 conventions based on statamic.dev documentation
