# Create Translations for Terms

Add translations to existing Statamic taxonomy term files for multisite projects.

Taxonomy terms store all localization data within a single file using the `localizations` key — unlike collection entries which use separate files per site.

## Scope

**You MUST only edit existing files at:**
- `content/taxonomies/{taxonomy}/{slug}.yaml` — adding only the `localizations` key

Do NOT create new term files — use the `create-terms` skill for that. This skill only adds translations to existing terms.

Do NOT create, edit, or modify any other files — including but not limited to:
- New term files — use `create-terms` skill instead
- Collection entry translation files — use `create-translations` skill instead
- Collection entry files (`content/collections/`) — use `create-entries` skill instead
- Blueprint files (`resources/blueprints/`) — use `create-blueprints` skill instead
- Taxonomy config files (`content/taxonomies/*.yaml` at the root level) — use `create-taxonomies` skill instead
- Collection config files (`content/collections/*.yaml`) — use `create-collections` skill instead
- View/template files (`resources/views/`)
- Config files (`config/`)
- Fieldset files (`resources/fieldsets/`)
- Routes, controllers, or any PHP files
- CSS, JS, or frontend assets

You may **read** other project files (e.g., `resources/sites.yaml`, blueprints) to inform your work, but do not modify them beyond adding `localizations` to term files.

If the task requires changes outside the allowed scope, stop and inform the user — do not make those changes yourself.

## Quick Start

1. **Detect multisite** — Read `resources/sites.yaml` to identify default and non-default sites (read only)
2. **Read the blueprint** — Read the blueprint to identify which fields are `localizable: true` (read only)
3. **Read the existing term** — Read the term file to understand current content (read only until editing)
4. Add the `localizations` key with translated data for each non-default site

## Workflow

### Step 1: Detect Multisite

Read `resources/sites.yaml` — **read only, do not modify**:
- Identify the default site and all non-default site keys
- If the project is single-site, stop — translations are not applicable

### Step 2: Read the Blueprint

Read the blueprint at `resources/blueprints/taxonomies/{taxonomy}/{blueprint}.yaml` — **read only, do not modify**:
- Identify which fields have `localizable: true` — only these fields should appear in localizations
- Fields without `localizable: true` are shared from the top level automatically

### Step 3: Read the Existing Term

Read the term file at `content/taxonomies/{taxonomy}/{slug}.yaml` — verify it exists before editing:
- If the term does not exist, ask the user whether to create it using the `create-terms` skill first

### Step 4: Add Localizations

Edit the existing term file — add **only** the `localizations` key. Do not modify existing top-level fields.

**Structure:**
```yaml
localizations:
  {non-default-site-handle}:
    title: {translated title}
    {localizable_field}: {translated value}
    slug: {translated-slug-or-same-slug}
```

### Example

**Before (created by `create-terms`):**
```yaml
title: Web Design
description: We build modern websites
menu_order: 1
```

**After (with localizations added by this skill):**
```yaml
title: Web Design
description: We build modern websites
menu_order: 1
localizations:
  indonesian:
    title: Desain Web
    description: Kami membangun website modern
    slug: desain-web
```

**Rules for `localizations`:**
- Only include non-default sites inside `localizations`
- Only include fields marked `localizable: true` in the blueprint
- Always include `title` and `slug` in each localization
- Non-localizable fields (e.g., `menu_order` if non-localizable) are shared from the top level automatically
- The default site's data lives at the top level — do NOT move it into `localizations`
- If multiple non-default sites exist, add an entry for each:

```yaml
localizations:
  indonesian:
    title: Desain Web
    description: Kami membangun website modern
    slug: desain-web
  japanese:
    title: ウェブデザイン
    description: 私たちはモダンなウェブサイトを構築します
    slug: web-design
```

## Rules

1. **Only edit** existing `.yaml` term files at `content/taxonomies/{taxonomy}/{slug}.yaml`. Do not create new files.
2. **Only add the `localizations` key.** Do not modify existing top-level fields in the term file.
3. **Do not create new term files** — use `create-terms` skill instead.
4. **Do not create collection entry translations** — use `create-translations` skill instead.
5. **Do not create or edit blueprints** — use `create-blueprints` skill instead.
6. **Do not create or edit taxonomy configs** — use `create-taxonomies` skill instead.
7. **Do not create or edit templates, config, routes, PHP, or frontend files.**
8. You may read any project file to inform your work (blueprints, sites config), but do not modify files beyond adding `localizations`.
9. Only include fields marked `localizable: true` in the blueprint inside each localization.
10. Always include `title` and `slug` in each localization entry.
11. Verify the term file exists before editing. If it does not exist, ask the user first.

## Accuracy Checks

Before finishing, verify:
- [ ] Term file exists at `content/taxonomies/{taxonomy}/{slug}.yaml`
- [ ] Only the `localizations` key was added — existing top-level fields were not modified
- [ ] Each non-default site has an entry under `localizations`
- [ ] Each localization includes `title` and `slug`
- [ ] Only `localizable: true` fields from the blueprint are included in localizations
- [ ] No new term files were created (use `create-terms` for that)
- [ ] No collection entry files, blueprints, configs, or other out-of-scope files were created or edited