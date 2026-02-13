# Create Taxonomies

Create or update Statamic taxonomy configuration with proper multisite support.

## Scope

**You MUST only create or edit files at `content/taxonomies/{handle}.yaml`.**

Do NOT create, edit, or modify any other files — including but not limited to:
- Collection config files (`content/collections/`) — use `attach-taxonomies` skill to link taxonomies to collections
- Blueprint files (`resources/blueprints/`) — use `create-blueprints` skill instead
- Term/content files (`content/taxonomies/{taxonomy}/`) — use `create-terms` skill instead
- Entry files (`content/collections/{collection}/`) — use `create-entries` skill instead
- View/template files (`resources/views/`)
- Config files (`config/`)
- Fieldset files (`resources/fieldsets/`)
- Routes, controllers, or any PHP files
- CSS, JS, or frontend assets

You may **read** other project files (e.g., `resources/sites.yaml`, existing collection configs) to inform your work, but do not modify them.

If the task requires changes outside the allowed path, stop and inform the user — do not make those changes yourself.

## Quick Start

1. **Detect multisite first** — Read `resources/sites.yaml` (read only)
2. Create `content/taxonomies/{handle}.yaml` with taxonomy config

## Workflow

### Step 1: Detect Multisite

Read `resources/sites.yaml` (or `config/statamic/sites.php`) — **read only, do not modify**:
- Single site: One site key defined
- Multisite: Multiple site keys (e.g., `english`, `indonesian`)

### Step 2: Read Schema (if available)

If a schema file exists at `schemas/{handle}.md`, read it to determine the `has_single` value. This controls which fields to include:

- **`has_single: false`** → Omit `template`, `layout`, and `preview_targets`. Terms have no public pages.

If no schema file exists, default to `has_single: true`.

### Step 3: Create Taxonomy Config

**Path:** `content/taxonomies/{handle}.yaml` — this is the only file you create in this step.

Use the appropriate template below. Copy it exactly, then replace `{handle}` with the actual taxonomy handle and `{Title}` with the display name. Do NOT skip any fields.

#### Template A: Multisite + has_single: true (default)

Use when multisite is detected AND `has_single` is `true` or not specified.

```yaml
title: '{Title}'
sites:
  - {site_key_1}
  - {site_key_2}
template: '{handle}/show'
layout: '{handle}/layout'
revisions: true
preview_targets:
  -
    label: Entry
    url: '{permalink}'
    refresh: true
```

#### Template B: Single site + has_single: true (default)

Use when only one site is detected AND `has_single` is `true` or not specified.

```yaml
title: '{Title}'
template: '{handle}/show'
layout: '{handle}/layout'
revisions: true
preview_targets:
  -
    label: Entry
    url: '{permalink}'
    refresh: true
```

#### Template C: Multisite + has_single: false

Use when multisite is detected AND `has_single: false`. Omits `template`, `layout`, `preview_targets`.

```yaml
title: '{Title}'
sites:
  - {site_key_1}
  - {site_key_2}
revisions: true
```

#### Template D: Single site + has_single: false

Use when only one site is detected AND `has_single: false`. Omits `template`, `layout`, `preview_targets`.

```yaml
title: '{Title}'
revisions: true
```

#### Template notes

- Replace `{site_key_1}`, `{site_key_2}`, etc. with actual site keys from `resources/sites.yaml`
- Taxonomies do NOT use a `route` field — Statamic handles taxonomy term routing automatically

## Rules

1. **Only write to** `content/taxonomies/{handle}.yaml`. No other files.
2. **Do not attach taxonomies to collections** — use `attach-taxonomies` skill instead.
3. **Do not create blueprints** — use `create-blueprints` skill instead.
4. **Do not create terms or entries** — use `create-entries` skill instead.
5. **Do not create or edit collection configs, templates, routes, PHP, or frontend files.**
6. You may read any project file to inform your work, but do not modify files outside the allowed path.
7. Output valid YAML only.
8. Always detect multisite in Step 1 before writing the taxonomy config. If multisite is enabled, you MUST include the `sites` field.
9. Always use the complete YAML template from Step 3. Do NOT skip any fields.
10. **If `has_single: false`** — do NOT include `template`, `layout`, or `preview_targets`.
11. **Never include a `route` field** — Statamic handles taxonomy routing automatically.

## Accuracy Checks

Before finishing, verify:
- [ ] File is valid YAML
- [ ] File path is `content/taxonomies/{handle}.yaml`
- [ ] All fields from the matching Step 3 template are present (no fields skipped)
- [ ] If `has_single: true` (or not specified): `template`, `layout`, and `preview_targets` are present
- [ ] If `has_single: false`: `template`, `layout`, and `preview_targets` are NOT present
- [ ] No `route` field is present (taxonomies never use `route`)
- [ ] If multisite is enabled: `sites` lists all site keys
- [ ] No collection configs, blueprints, entries, templates, or other out-of-scope files were created or edited