# Create Entries

Create Statamic entry files with dummy content for collections and pages.

## Quick Start

1. **Detect multisite first** — Check `resources/sites.yaml`
2. **Check blueprint** — Look in `resources/blueprints/collections/{collection}/` for available blueprints. If no specific blueprint exists, check `resources/blueprints/default.yaml`
3. Create `.md` file with YAML frontmatter + optional markdown content
4. Generate UUID for `id` field
5. **Multisite only** — Create full content entries for the default site first, then create translation entries for each non-default site

## Entry Paths

**Single site:**
- Standard: `content/collections/{collection}/{slug}.md`
- Dated: `content/collections/{collection}/{date}.{slug}.md`

**Multisite:**
- Standard: `content/collections/{collection}/{site}/{slug}.md`
- Dated: `content/collections/{collection}/{site}/{date}.{slug}.md`

## Entry Structure

1. Read the blueprint file at `resources/blueprints/collections/{collection}/{blueprint}.yaml`
2. Use the fields defined in the blueprint to build the YAML frontmatter
3. Add `id` (UUID) and `title` as required fields
4. Populate each field from the blueprint with appropriate dummy content matching its fieldtype
5. If the blueprint has a `bard` or `markdown` field named `content`, place that content after the closing `---` as markdown body

```yaml
---
id: {generated-uuid}
title: {entry title}
blueprint: {blueprint handle}
{field_from_blueprint}: {value matching fieldtype}
---
{markdown body if blueprint has a content field}
```


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


## Translating Entries -> MULTISITE ONLY

To create a translation entry for a non-default site:

1. Use the **same slug** as the default site entry for the filename
2. Generate a **new unique UUID** for the `id` field — must not duplicate any existing ID
3. Set `origin` to the `id` of the default site entry
4. Read the blueprint to identify which fields are `localizable: true`
5. Only include localizable fields with translated values
6. Fields not included will fall back to the origin entry

**Default site entry:** `content/collections/pages/english/about.md`
```yaml
---
id: 550e8400-e29b-41d4-a716-446655440010
title: About Us
blueprint: about
published: true
hero_lead: Building trust since 1999
---
Welcome to our company...
```

**Translation entry:** `content/collections/pages/indonesian/about.md`
```yaml
---
id: 550e8400-e29b-41d4-a716-446655440011
origin: 550e8400-e29b-41d4-a716-446655440010
title: Tentang Kami
hero_lead: Membangun kepercayaan sejak 1999
---
Selamat datang di perusahaan kami...
```

Only translate fields marked `localizable: true` in the blueprint. Non-localizable fields are shared across all sites and must NOT be duplicated in translation entries.

## Boundaries

- Do NOT create blueprints here — Use `create-blueprints`
- Do NOT create collection config here — Use `create-collections`
- Filename is slug-based, NOT UUID-based

## Accuracy Checks

- Entry file is `.md` with YAML frontmatter
- UUID in `id` field, slug in filename
- Dated entries: date in both filename and frontmatter