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

If a schema file exists at `schemas/{handle}.md`, read the taxonomy view fields: `has_index`, `has_show`, `has_collection_index`, `has_collection_show`. All default to `true` if not specified.

- **If ALL 4 are `false`** → Omit `layout` and `preview_targets`. Terms have no public pages.
- **If ANY are `true`** (or not specified, since default is `true`) → Include `layout: {handle}/layout` and `preview_targets`. The presence of `layout` in the config is the signal to other skills that this taxonomy has public views.

If no schema file exists, default to all views enabled.

### Step 3: Create Taxonomy Config

**Path:** `content/taxonomies/{handle}.yaml` — this is the only file you create in this step.

Use the appropriate template below. Copy it exactly, then replace `{handle}` with the actual taxonomy handle and `{Title}` with the display name. Do NOT skip any fields.

#### Template A: Multisite + views enabled (default)

Use when multisite is detected AND any taxonomy view field is `true` (or not specified — all default to `true`).

```yaml
title: '{Title}'
sites:
  - {site_key_1}
  - {site_key_2}
layout: '{handle}/layout'
revisions: true
preview_targets:
  -
    label: Entry
    url: '{permalink}'
    refresh: true
```

#### Template B: Single site + views enabled (default)

Use when only one site is detected AND any taxonomy view field is `true` (or not specified).

```yaml
title: '{Title}'
layout: '{handle}/layout'
revisions: true
preview_targets:
  -
    label: Entry
    url: '{permalink}'
    refresh: true
```

#### Template C: Multisite + all views disabled

Use when multisite is detected AND all 4 taxonomy view fields are `false`. Omits `layout`, `preview_targets`.

```yaml
title: '{Title}'
sites:
  - {site_key_1}
  - {site_key_2}
revisions: true
```

#### Template D: Single site + all views disabled

Use when only one site is detected AND all 4 taxonomy view fields are `false`. Omits `layout`, `preview_targets`.

```yaml
title: '{Title}'
revisions: true
```

#### Template notes

- Replace `{site_key_1}`, `{site_key_2}`, etc. with actual site keys from `resources/sites.yaml`
- Taxonomies do NOT use a `route` field — Statamic handles taxonomy term routing automatically
- Do NOT include `template` or `term_template` — Statamic auto-resolves views based on naming conventions (e.g., `{taxonomy}/show`, `{taxonomy}/index`). The `create-view-boilerplates` skill generates these view files.

#### Taxonomy view system

Statamic supports 4 taxonomy view types that auto-activate when the corresponding view files exist:

| View | URL Pattern | View Path | Purpose |
|------|-------------|-----------|---------|
| Global taxonomy index | `/{taxonomy}` | `{taxonomy}/index` | Lists all terms |
| Global single term | `/{taxonomy}/{term}` | `{taxonomy}/show` | Entries for a term (all collections) |
| Collection taxonomy index | `/{collection}/{taxonomy}` | `{collection}/{taxonomy}/index` | Terms for one collection only |
| Collection single term | `/{collection}/{taxonomy}/{term}` | `{collection}/{taxonomy}/show` | Entries for a term (one collection only) |

- All 4 view types auto-activate when the corresponding view files exist — no `template` or `term_template` config fields needed.
- This skill only sets `layout` and `preview_targets`. Use `create-view-boilerplates` to generate the actual view files.
- Which of the 4 views to generate is controlled by the schema fields: `has_index`, `has_show`, `has_collection_index`, `has_collection_show` (all default to `true`).

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
10. **If all taxonomy view fields are `false`** — do NOT include `layout` or `preview_targets`.
11. **Never include `template`, `term_template`, or `route`** — Statamic auto-resolves views by naming convention and handles routing automatically.

## Accuracy Checks

Before finishing, verify:
- [ ] File is valid YAML
- [ ] File path is `content/taxonomies/{handle}.yaml`
- [ ] All fields from the matching Step 3 template are present (no fields skipped)
- [ ] If any taxonomy view field is `true` (or not specified — default is `true`): `layout` and `preview_targets` are present
- [ ] If all taxonomy view fields are `false`: `layout` and `preview_targets` are NOT present
- [ ] No `template` or `term_template` field is present (Statamic auto-resolves views by naming convention)
- [ ] No `route` field is present (taxonomies never use `route`)
- [ ] If multisite is enabled: `sites` lists all site keys
- [ ] No collection configs, blueprints, entries, templates, or other out-of-scope files were created or edited