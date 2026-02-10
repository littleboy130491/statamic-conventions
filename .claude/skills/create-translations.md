# Create Translations

Create Statamic translation entry files for non-default sites in a multisite project.

This skill is for **collection entry translations only**. Taxonomy term translations are handled within the term file itself using the `localizations` key — use the `create-translations-terms` skill for that.

## Scope

**You MUST only create files at:**
- `content/collections/{collection}/{non-default-site}/{slug}.md`
- `content/collections/{collection}/{non-default-site}/{date}.{slug}.md`

These paths are for non-default sites only. Do NOT create entries for the default site — use the `create-entries` skill for those.

Do NOT create, edit, or modify any other files — including but not limited to:
- Default site entries (`content/collections/{collection}/{default-site}/`) — use `create-entries` skill instead
- Taxonomy term files (`content/taxonomies/`) — use `create-terms` skill instead (term translations are stored in the same file, not as separate files)
- Blueprint files (`resources/blueprints/`) — use `create-blueprints` skill instead
- Collection config files (`content/collections/*.yaml`) — use `create-collections` skill instead
- Taxonomy config files (`content/taxonomies/*.yaml`) — use `create-taxonomies` skill instead
- View/template files (`resources/views/`)
- Config files (`config/`)
- Fieldset files (`resources/fieldsets/`)
- Routes, controllers, or any PHP files
- CSS, JS, or frontend assets

You may **read** other project files (e.g., `resources/sites.yaml`, blueprints, default site entries) to inform your work, but do not modify them.

If the task requires changes outside the allowed paths, stop and inform the user — do not make those changes yourself.

## Quick Start

1. **Detect multisite** — Read `resources/sites.yaml` to identify default and non-default sites (read only)
2. **Read the blueprint** — Read the blueprint to identify which fields are `localizable: true` (read only)
3. **Read the origin entry** — Read the default site entry to get the origin `id` (read only)
4. Create translation `.md` files for each non-default site

## Workflow

### Step 1: Detect Multisite

Read `resources/sites.yaml` — **read only, do not modify**:
- Identify the default site and all non-default site keys
- If the project is single-site, stop — translations are not applicable

### Step 2: Read the Blueprint

Read the blueprint at `resources/blueprints/collections/{collection}/{blueprint}.yaml` — **read only, do not modify**:
- Identify which fields have `localizable: true` — only these fields should appear in translation entries
- Fields without `localizable: true` are shared from the origin and must NOT be duplicated

### Step 3: Read the Origin Entry

Read the default site entry — **read only, do not modify**:
- Get the `id` value — this becomes the `origin` in the translation entry
- Get the slug from the filename — the translation must use the same slug

### Step 4: Create Translation Entries

For each non-default site, create a translation entry:

**Path:** `content/collections/{collection}/{non-default-site}/{slug}.md`

Use the **same slug** as the default site entry for the filename.

**Translation entry structure:**
```yaml
---
id: {new-unique-uuid}
origin: {default-site-entry-id}
title: {translated title}
{localizable_field}: {translated value}
---
{translated markdown body if blueprint has a content field}
```

**Required fields:**
- `id` — Generate a new unique UUID v4. Must not duplicate any existing ID.
- `origin` — The `id` of the default site entry from Step 3.
- `title` — Translated title.

**Include only:**
- Fields marked `localizable: true` in the blueprint, with translated values.
- Markdown body content (translated) if the blueprint has a `bard` or `markdown` content field.

**Do NOT include:**
- `sites` — only default site entries have `sites`
- `blueprint` — inherited from origin
- Non-localizable fields — shared from the origin entry automatically

### Example

**Default site entry (read only — do not create or modify):** `content/collections/pages/english/about.md`
```yaml
---
id: 550e8400-e29b-41d4-a716-446655440010
title: About Us
blueprint: about
sites:
  - english
  - indonesian
published: true
hero_lead: Building trust since 1999
---
Welcome to our company...
```

**Translation entry (this is what you create):** `content/collections/pages/indonesian/about.md`
```yaml
---
id: 550e8400-e29b-41d4-a716-446655440011
origin: 550e8400-e29b-41d4-a716-446655440010
title: Tentang Kami
hero_lead: Membangun kepercayaan sejak 1999
---
Selamat datang di perusahaan kami...
```

Only `title` and `hero_lead` appear because they are `localizable: true` in the blueprint. Non-localizable fields like `published` are shared from the origin and excluded.

## Rules

1. **Only create** `.md` entry files under non-default site directories. No other files.
2. **Do not create or edit default site entries** — use `create-entries` skill instead.
3. **Do not create or edit taxonomy term files** — use `create-terms` skill instead. Taxonomy term translations are stored within the term file using the `localizations` key, not as separate files.
4. **Do not create or edit blueprints** — use `create-blueprints` skill instead.
5. **Do not create or edit collection configs** — use `create-collections` skill instead.
6. **Do not create or edit taxonomy configs** — use `create-taxonomies` skill instead.
7. **Do not create or edit templates, config, routes, PHP, or frontend files.**
8. You may read any project file to inform your work (blueprints, sites config, origin entries), but do not modify them.
9. Output `.md` files with valid YAML frontmatter.
10. Every translation entry MUST have a new unique UUID v4 — never reuse the origin's ID.
11. Always set `origin` to the default site entry's `id`.
12. Use the same slug as the default site entry for the filename.
13. Only include fields marked `localizable: true` in the blueprint. Do not duplicate non-localizable fields.
14. Do NOT include `sites` or `blueprint` in translation entries.

## Accuracy Checks

Before finishing, verify:
- [ ] Translation entry is `.md` with valid YAML frontmatter
- [ ] File path is under a non-default site directory
- [ ] `id` is a new unique UUID v4 (not the same as the origin)
- [ ] `origin` matches the default site entry's `id`
- [ ] Filename slug matches the default site entry's slug
- [ ] Only `localizable: true` fields from the blueprint are included
- [ ] `sites` and `blueprint` fields are NOT present in the translation entry
- [ ] No taxonomy term files were created (term translations use the `localizations` key within the term file)
- [ ] No default site entries, blueprints, configs, or other out-of-scope files were created or edited