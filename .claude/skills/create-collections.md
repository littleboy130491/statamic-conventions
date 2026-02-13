# Create Collections

Create or update Statamic collection configuration with proper multisite support.

## Scope

**You MUST only create or edit files at `content/collections/{handle}.yaml`.**

Do NOT create, edit, or modify any other files — including but not limited to:
- Blueprint files (`resources/blueprints/`) — use `create-blueprints` skill instead
- Entry/content files (`content/collections/{collection}/`) — use `create-entries` skill instead
- Taxonomy files (`content/taxonomies/`) — use `create-taxonomies` skill to create, `attach-taxonomies` skill to link
- Mount page entries or mount config — use `mount-collections` skill instead
- View/template files (`resources/views/`)
- Config files (`config/`)
- Fieldset files (`resources/fieldsets/`)
- Routes, controllers, or any PHP files
- CSS, JS, or frontend assets

You may **read** other project files (e.g., `resources/sites.yaml`, `config/statamic/sites.php`, existing entries) to inform your work, but do not modify them.

If the task requires changes outside the allowed paths, stop and inform the user — do not make those changes yourself.

## Quick Start

1. **Detect multisite first** — Read `resources/sites.yaml` (read only)
2. Create `content/collections/{handle}.yaml` with collection config

## Workflow

### Step 1: Detect Multisite

Read `resources/sites.yaml` (or `config/statamic/sites.php`) — **read only, do not modify**:
- Single site: One site key defined
- Multisite: Multiple site keys (e.g., `english`, `indonesian`)

### Step 2: Read Schema (if available)

If a schema file exists at `schemas/{handle}.md`, read it to determine `has_single` and `has_archive` values. These control which fields to include:

- **`has_single: false`** → Omit `route`, `template`, `layout`, and `preview_targets`. Entries have no public pages.
- **`has_archive: false`** → Do NOT mount. Do not suggest mounting to the user.

If no schema file exists, default both to `true`.

### Step 3: Create Collection Config

**Path:** `content/collections/{handle}.yaml` — this is the only file you create in this step.

Use the appropriate template below. Copy it exactly, then replace `{handle}` with the actual collection handle and `{Title}` with the display name. Do NOT skip any fields.

#### Template A: Multisite + has_single: true (default)

Use when multisite is detected AND `has_single` is `true` or not specified.

```yaml
title: '{Title}'
icon: collections
sites:
  - {site_key_1}
  - {site_key_2}
propagate: true
template: '{handle}/show'
layout: '{handle}/layout'
route: '/{handle}/{slug}'
revisions: true
date: true
sort_by: date
sort_dir: desc
date_behavior:
  past: public
  future: private
preview_targets:
  -
    label: Entry
    url: '{permalink}'
    refresh: true
structure:
  root: false
  max_depth: 1
  slugs: true
```

#### Template B: Single site + has_single: true (default)

Use when only one site is detected AND `has_single` is `true` or not specified.

```yaml
title: '{Title}'
icon: collections
template: '{handle}/show'
layout: '{handle}/layout'
route: '/{handle}/{slug}'
revisions: true
date: true
sort_by: date
sort_dir: desc
date_behavior:
  past: public
  future: private
preview_targets:
  -
    label: Entry
    url: '{permalink}'
    refresh: true
structure:
  root: false
  max_depth: 1
  slugs: true
```

#### Template C: Multisite + has_single: false

Use when multisite is detected AND `has_single: false`. Omits `route`, `template`, `layout`, `preview_targets`.

```yaml
title: '{Title}'
icon: collections
sites:
  - {site_key_1}
  - {site_key_2}
propagate: true
revisions: true
date: true
sort_by: date
sort_dir: desc
date_behavior:
  past: public
  future: private
structure:
  root: false
  max_depth: 1
  slugs: true
```

#### Template D: Single site + has_single: false

Use when only one site is detected AND `has_single: false`. Omits `route`, `template`, `layout`, `preview_targets`.

```yaml
title: '{Title}'
icon: collections
revisions: true
date: true
sort_by: date
sort_dir: desc
date_behavior:
  past: public
  future: private
structure:
  root: false
  max_depth: 1
  slugs: true
```

#### Template notes

- Replace `{site_key_1}`, `{site_key_2}`, etc. with actual site keys from `resources/sites.yaml`
- `max_depth`: Use `1` for flat collections (blog, news). Use `3` or more for hierarchical collections (pages)
- **Taxonomies:** You may append a `taxonomies` list (e.g., `taxonomies:\n  - categories\n  - tags`). Before adding a taxonomy handle, check if it exists at `content/taxonomies/{handle}.yaml` (read only). If it does not exist, ask the user whether to create it using the `create-taxonomies` skill

## Rules

1. **Only write to** `content/collections/{handle}.yaml`. No other files.
2. **Do not create blueprints** — use `create-blueprints` skill instead.
3. **Do not create entries** — use `create-entries` skill instead.
4. **Do not mount collections** — use `mount-collections` skill instead. If `has_archive: false` in the schema, do NOT suggest mounting.
5. **Do not create taxonomy files** — use `create-taxonomies` skill instead. To attach taxonomies to existing collections, use `attach-taxonomies` skill. If a taxonomy in the `taxonomies` list does not exist yet, ask the user before proceeding.
6. **Do not create or edit templates, config, routes, PHP, or frontend files.**
7. You may read any project file to inform your work, but do not modify files outside the allowed path.
8. Output valid YAML only.
9. Always detect multisite in Step 1 before writing the collection config. If multisite is enabled, you MUST include `sites` and `propagate` fields.
10. Always use the complete YAML template from Step 3. Do NOT skip any fields.
11. **If `has_single: false`** — do NOT include `route`, `template`, `layout`, or `preview_targets`.
12. **If `has_archive: false`** — do NOT mount. Do not suggest or ask about mounting.

## Accuracy Checks

Before finishing, verify:
- [ ] File is valid YAML
- [ ] File path is `content/collections/{handle}.yaml`
- [ ] All fields from the matching Step 3 template are present (no fields skipped)
- [ ] If `has_single: true` (or not specified): `route`, `template`, `layout`, and `preview_targets` are present
- [ ] If `has_single: false`: `route`, `template`, `layout`, and `preview_targets` are NOT present
- [ ] `date_behavior` and `structure` blocks are included exactly as specified
- [ ] If multisite is enabled: `sites` lists all site keys and `propagate: true` is set
- [ ] No blueprints, entries, templates, config, or other out-of-scope files were created or edited