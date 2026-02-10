# Create Entries

Create Statamic entry files with dummy content for collections and pages.

## Scope

**You MUST only create files at:**
- Single site: `content/collections/{collection}/{slug}.md` or `content/collections/{collection}/{date}.{slug}.md`
- Multisite (default site only): `content/collections/{collection}/{default-site-handle}/{slug}.md` or `content/collections/{collection}/{default-site-handle}/{date}.{slug}.md`

Do NOT create, edit, or modify any other files — including but not limited to:
- Translation entries for non-default sites — use `create-translations` skill instead
- Blueprint files (`resources/blueprints/`) — use `create-blueprints` skill instead
- Collection config files (`content/collections/*.yaml`) — use `create-collections` skill instead
- Taxonomy config files (`content/taxonomies/`) — use `create-taxonomies` skill instead
- View/template files (`resources/views/`)
- Config files (`config/`)
- Fieldset files (`resources/fieldsets/`)
- Routes, controllers, or any PHP files
- CSS, JS, or frontend assets

You may **read** other project files (e.g., `resources/sites.yaml`, blueprints, collection configs) to inform your work, but do not modify them.

If the task requires changes outside the allowed paths, stop and inform the user — do not make those changes yourself.

## Quick Start

1. **Detect multisite first** — Read `resources/sites.yaml` (read only)
2. **Read the blueprint** — Read the blueprint at `resources/blueprints/collections/{collection}/` (read only). If no specific blueprint exists, check `resources/blueprints/default.yaml`
3. Create `.md` file with YAML frontmatter + optional markdown content
4. Generate UUID for `id` field
5. **Multisite only** — Create entries for the default site only. For non-default site translations, use the `create-translations` skill

## Entry Paths

**Single site:**
- Standard: `content/collections/{collection}/{slug}.md`
- Dated: `content/collections/{collection}/{date}.{slug}.md`

**Multisite (default site only):**
- Standard: `content/collections/{collection}/{default-site-handle}/{slug}.md`
- Dated: `content/collections/{collection}/{default-site-handle}/{date}.{slug}.md`

Do NOT create entry files under non-default site directories — use the `create-translations` skill for those.

## Entry Structure

1. Read the blueprint file at `resources/blueprints/collections/{collection}/{blueprint}.yaml` — **read only, do not modify**
2. Use the fields defined in the blueprint to build the YAML frontmatter
3. Add `id` (UUID) and `title` as required fields
4. Populate each field from the blueprint with appropriate dummy content matching its fieldtype
5. If the blueprint has a `bard` or `markdown` field named `content`, place that content after the closing `---` as markdown body

**Single site:**
```yaml
---
id: {generated-uuid}
title: {entry title}
blueprint: {blueprint handle}
{field_from_blueprint}: {value matching fieldtype}
---
{markdown body if blueprint has a content field}
```

**Multisite (default site entry):**
```yaml
---
id: {generated-uuid}
title: {entry title}
blueprint: {blueprint handle}
sites:
  - {site_1}
  - {site_2}
{field_from_blueprint}: {value matching fieldtype}
---
{markdown body if blueprint has a content field}
```

The `sites` field lists all site keys from `resources/sites.yaml` where this entry should be available. Only include `sites` on default site entries.

## Generating UUIDs

Use standard UUID v4 format:
```
550e8400-e29b-41d4-a716-446655440000
```

Each entry MUST have a unique ID.

## Dated Entries

**Filename format:** `{YYYY-MM-DD}.{slug}.md`

Example: `2024-01-15.my-first-post.md`

```yaml
---
id: unique-uuid
title: My First Post
date: '2024-01-15'
---
```

## Rules

1. **Only create** `.md` entry files at the paths listed in [Entry Paths](#entry-paths). No other files.
2. **Do not create translation entries** for non-default sites — use `create-translations` skill instead.
3. **Do not create or edit blueprints** — use `create-blueprints` skill instead.
4. **Do not create or edit collection configs** — use `create-collections` skill instead.
5. **Do not create or edit taxonomy configs** — use `create-taxonomies` skill instead.
6. **Do not create or edit templates, config, routes, PHP, or frontend files.**
7. You may read any project file to inform your work (blueprints, sites config, collection configs), but do not modify them.
8. Output `.md` files with valid YAML frontmatter.
9. Every entry MUST have a unique UUID v4 in the `id` field.
10. Filename is slug-based, NOT UUID-based.
11. If multisite is enabled, include the `sites` field listing all site keys. Only default site entries get `sites`.

## Accuracy Checks

Before finishing, verify:
- [ ] Entry file is `.md` with valid YAML frontmatter
- [ ] File path matches the correct pattern from [Entry Paths](#entry-paths)
- [ ] `id` field contains a unique UUID v4
- [ ] Filename uses slug, not UUID
- [ ] Dated entries have date in both filename and frontmatter
- [ ] If multisite: entry is under the default site directory only, with `sites` field listing all site keys
- [ ] No translation entries for non-default sites were created (use `create-translations` for those)
- [ ] No blueprints, collection configs, templates, or other out-of-scope files were created or edited