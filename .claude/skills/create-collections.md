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

If a schema file exists at `schemas/{handle}.md`, read it and derive the collection config from these fields:

| Schema field | Collection config impact |
|--------------|--------------------------|
| `title` | `title` |
| `has_single` | If `false`, omit `route`, `template`, `layout`, and `preview_targets` |
| `route` | Use exactly as the collection `route` when `has_single: true` |
| `dated` | If `true`, add `date: true`, date sorting, and `date_behavior`; if `false`, omit date config |
| `structure` | If `true`, add a `structure` block; if `false`, omit it |
| `structure_max_depth` | Use as `structure.max_depth`; for pages with a homepage/root, use `root: true` |
| `taxonomy_relationship` | Add only existing taxonomy handles to `taxonomies`; missing taxonomies are created by `create-taxonomies` |
| `multisite` / detected sites | If multisite, include `sites` and `propagate: true` |

- **`has_single: false`** → Entries are data-only. Omit `route`, `template`, `layout`, and `preview_targets`.
- **`has_archive: false`** → Do not mount. Do not suggest mounting to the user.
- If no schema file exists, choose conservative defaults: `has_single: true`, `dated: false`, `structure: false`, `route: '/{handle}/{slug}'`.

### Step 3: Create Collection Config

**Path:** `content/collections/{handle}.yaml` — this is the only file you create in this step.

Build the YAML from the schema. Do not include fields just because they appear in an example.

#### Base fields

```yaml
title: '{Title}'
icon: collections
revisions: true
```

For multisite, add:

```yaml
sites:
  - {site_key_1}
  - {site_key_2}
propagate: true
```

If `has_single: true`, add:

```yaml
template: '{handle}/show'
layout: '{handle}/layout'
route: '/{handle}/{slug}'
preview_targets:
  -
    label: Entry
    url: '{permalink}'
    refresh: true
```

Replace `route` with the schema's `route` value if provided.

If `dated: true`, add:

```yaml
date: true
sort_by: date
sort_dir: desc
date_behavior:
  past: public
  future: private
```

If `dated: false`, omit all date fields. You may still set `sort_by: title` or another explicit sort if the schema or user asks for it.

If `structure: true`, add:

```yaml
structure:
  root: false
  max_depth: {structure_max_depth}
  slugs: true
```

For a `pages` collection intended to contain the homepage, use `root: true` and a route like `/{parent_uri}/{slug}`.

If taxonomies already exist and should be attached:

```yaml
taxonomies:
  - categories
  - tags
```

Before adding a taxonomy handle, check if it exists at `content/taxonomies/{handle}.yaml` (read only). If it does not exist, leave attachment to `attach-taxonomies` after `create-taxonomies`.

#### Examples

Pages collection:

```yaml
title: Pages
icon: collections
template: pages/default
layout: layout
route: '/{parent_uri}/{slug}'
revisions: true
structure:
  root: true
  max_depth: 3
  slugs: true
```

Dated posts collection:

```yaml
title: Posts
icon: collections
template: posts/show
layout: layout
route: '/blog/{slug}'
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
```

Data-only testimonials collection:

```yaml
title: Testimonials
icon: collections
revisions: true
```

## Rules

1. **Only write to** `content/collections/{handle}.yaml`. No other files.
2. **Do not create blueprints** — use `create-blueprints` skill instead.
3. **Do not create entries** — use `create-entries` skill instead.
4. **Do not mount collections** — use `mount-collections` skill instead. If `has_archive: false` in the schema, do NOT suggest mounting.
5. **Do not create taxonomy files** — use `create-taxonomies` skill instead. To attach taxonomies to existing collections, use `attach-taxonomies` skill. If a taxonomy in the `taxonomies` list does not exist yet, ask the user before proceeding.
6. **Do not create or edit templates, config, routes, PHP, or frontend files.**
7. You may read any project file to inform your work, but do not modify files outside the allowed path.
8. Output valid YAML only.
9. Always detect multisite in Step 1 before writing the collection config. If multisite is enabled, include `sites` and `propagate` fields.
10. Include only fields supported by the schema and project context. Do not copy optional example fields blindly.
11. **If `has_single: false`** — do NOT include `route`, `template`, `layout`, or `preview_targets`.
12. **If `has_archive: false`** — do NOT mount. Do not suggest or ask about mounting.

## Accuracy Checks

Before finishing, verify:
- [ ] File is valid YAML
- [ ] File path is `content/collections/{handle}.yaml`
- [ ] Config fields match the schema (`dated`, `structure`, `route`, `has_single`, `taxonomy_relationship`)
- [ ] If `has_single: true` (or not specified): `route`, `template`, `layout`, and `preview_targets` are present
- [ ] If `has_single: false`: `route`, `template`, `layout`, and `preview_targets` are NOT present
- [ ] `date_behavior` is present only for dated collections
- [ ] `structure` is present only for structured collections
- [ ] If multisite is enabled: `sites` lists all site keys and `propagate: true` is set
- [ ] No blueprints, entries, templates, config, or other out-of-scope files were created or edited
