# Create Schema

Figure out the right content schema for a Statamic project and output simple `.md` files to the `schemas/` directory. Each schema file maps to one downstream skill (create-collections, create-blueprints, create-taxonomies, etc.).

## Scope

**You MUST only create or edit `.md` files within the `schemas/` directory at the project root.**

Do NOT create any Statamic files (collections, blueprints, taxonomies, globals, entries, templates, configs, PHP, CSS, JS). Those are the job of downstream skills.

You may **read** any project file to inform your schema decisions, but do not modify them.

## Quick Start

1. **Detect multisite** — Read `resources/sites.yaml` (read only)
2. **Ask the user** what their site needs (or infer from provided sitemap/wireframe/description)
3. **Write one schema file per content type** to `schemas/`

## Workflow

### Step 1: Detect Multisite

Read `resources/sites.yaml` (or `config/statamic/sites.php`) — **read only, do not modify**.

- Single site: One site key → fields are NOT localizable by default
- Multisite: Multiple site keys → text/content/asset fields MUST be marked `localizable`

### Step 2: Gather Requirements

Understand what the site needs. Ask the user if unclear. You need:

- What page types exist (home, about, contact, etc.)
- What repeated content types exist (blog posts, team members, products, etc.)
- What categorization is needed (categories, tags, etc.)
- What site-wide settings are needed (logo, contact info, social links, etc.)
- What forms exist (contact, newsletter, etc.)
- What navigation menus are needed (header, footer, etc.) → **delegate to `create-schema-navigation` skill**

### Step 3: Write Schema Files

Create one `.md` file per content type in `schemas/`. Use the format below.

## Schema File Format

Each file uses a simple flat format: key-value header followed by a pipe-delimited field list.

**Filename:** `schemas/{handle}.md`

### Collection Schema

```
schema_name: {handle}
schema_type: collection
title: {Display Title}
has_single: {true/false}
has_archive: {true/false}
route: /{handle}/{slug} (omit if has_single: false)
dated: {true/false}
structure: {true/false}
structure_max_depth: {number, only if structure: true}
mount: {page slug, omit if has_archive: false or none}
taxonomy_relationship: {- handle1 - handle2, or omit if none}
collection_relationship: {- handle1 - handle2, or omit if none}
multisite: {true/false}
sites: {- site1 - site2, only if multisite: true}

blueprint: {blueprint_handle}
blueprint_description: {one-line description}
fields:
{handle} | {type} | {required/optional} | {localizable, only if multisite} | {notes}
```

**`has_single` / `has_archive` explained (similar to WordPress):**

- **`has_single: true`** — Each entry has its own page with a URL (like WordPress posts/pages). The collection MUST have a `route`. Downstream skills will generate templates and layouts for individual entries.
- **`has_single: false`** — Entries have no individual pages (like WordPress data-only post types, e.g., team members displayed only within other pages). Omit `route`. Downstream skills will NOT create templates or layouts for this collection.
- **`has_archive: true`** — The collection has a listing/index page (like WordPress archive pages). The collection can have a `mount` to a page that serves as the archive.
- **`has_archive: false`** — No listing page exists for this collection. Omit `mount`.

**Example:**

```
schema_name: blog_posts
schema_type: collection
title: Blog Posts
has_single: true
has_archive: true
route: /blog/{slug}
dated: true
structure: false
mount: blog
taxonomy_relationship: - categories - tags
collection_relationship: - team_members
multisite: true
sites: - english - indonesian

blueprint: post
blueprint_description: Standard blog post
fields:
title | text | required | localizable
featured_image | assets | required | localizable | max_files: 1
excerpt | textarea | optional | localizable | character_limit: 300
content | bard | optional | localizable | h2, h3, bold, italic, unorderedlist, link, image
author | entries | optional | | collections: team_members, max_items: 1
```

If a collection has **multiple blueprints**, list them sequentially separated by `---`:

```
schema_name: pages
schema_type: collection
title: Pages
has_single: true
has_archive: false
route: /{parent_uri}/{slug}
dated: false
structure: true
structure_max_depth: 3
collection_relationship: - blog_posts
multisite: true
sites: - english - indonesian

blueprint: default
blueprint_description: Simple content page
fields:
title | text | required | localizable
content | bard | optional | localizable | h2, h3, bold, italic, unorderedlist, link, image

---

blueprint: home
blueprint_description: Homepage with hero
fields:
title | text | required | localizable
hero_heading | text | optional | localizable
hero_lead | textarea | optional | localizable
hero_image | assets | optional | localizable | max_files: 1
hero_cta_text | text | optional | localizable
hero_cta_link | link | optional | | collections: pages
featured_posts | entries | optional | | collections: blog_posts, max_items: 3
```

### Taxonomy Schema

Taxonomies do NOT have routes — Statamic handles taxonomy routing automatically. Term URLs are defined by the taxonomy's own route (`/{taxonomy-slug}/{term-slug}`) and by each collection's route configuration for collection-scoped term pages.

```
schema_name: {handle}
schema_type: taxonomy
title: {Display Title}
has_single: {true/false}
collections: - {collection1} - {collection2}
multisite: {true/false}
sites: {- site1 - site2, only if multisite: true}

blueprint: {handle}
fields:
title | text | required | localizable
{additional fields if any}
```

**`has_single` explained:**

- **`has_single: true`** (default) — Terms have their own public pages. Downstream skills will create `template`, `layout`, and `preview_targets` in the taxonomy config, and generate view boilerplates for term pages.
- **`has_single: false`** — Terms are data-only (used for tagging/filtering but have no public term pages). Downstream skills will omit `template`, `layout`, and `preview_targets`.

**Example:**

```
schema_name: categories
schema_type: taxonomy
title: Categories
has_single: true
collections: - blog_posts - projects
multisite: true
sites: - english - indonesian

blueprint: categories
fields:
title | text | required | localizable
description | textarea | optional | localizable
icon | assets | optional | localizable | max_files: 1
```

### Global Schema

```
schema_name: {handle}
schema_type: global
title: {Display Title}
multisite: {true/false}
sites: {- site1 - site2, only if multisite: true}

fields:
{handle} | {type} | {notes}
```

**Example:**

```
schema_name: site_settings
schema_type: global
title: Site Settings
multisite: true
sites: - english - indonesian

fields:
site_name | text | localizable
tagline | text | localizable
logo | assets | localizable, max_files: 1
email | text | localizable, input_type: email
phone | text | localizable
```

### Form Schema

```
schema_name: {handle}
schema_type: form
title: {Display Title}
store: {true/false}
honeypot: {field_name}
email_to: {recipient}
email_subject: {subject}

fields:
{handle} | {type} | {required/optional} | {notes}
```

**Example:**

```
schema_name: contact
schema_type: form
title: Contact Form
store: true
honeypot: website
email_to: admin@example.com
email_subject: New contact form submission

fields:
name | text | required
email | text | required | input_type: email
subject | select | required | options: general, support, sales
message | textarea | required
```

### Navigation Schema

**Navigation schemas have been moved to a separate skill.** Use the `create-schema-navigation` skill instead — it captures the full menu tree structure (items, nesting, entry/link/text types) that the downstream `create-navigations` skill needs.

### Replicator Fields

When a field is a replicator, list its sets after the field using indented sub-fields:

```
sections | replicator | optional | localizable
  set: hero
    heading | text
    lead | textarea
    image | assets | max_files: 1
    cta_text | text
    cta_link | link
  set: text_block
    content | bard | h2, h3, bold, italic, link
  set: cta
    heading | text
    button_text | text
    button_link | link
```

### Grid Fields

When a field is a grid, list its columns after the field using indented sub-fields:

```
team_members | grid | optional | localizable
  name | text | required
  role | text
  photo | assets | max_files: 1
  bio | textarea
```

## Rules

1. **Only write to** `schemas/*.md`. No other files.
2. **One file per content type** — e.g., `schemas/blog_posts.md`, `schemas/categories.md`, `schemas/site_settings.md`, `schemas/contact.md`. Navigation schemas use `_nav.md` suffix and are created by the `create-schema-navigation` skill.
3. **Always detect multisite first.** If multisite, add `localizable` to all user-facing text, content, and asset fields.
4. **Use snake_case** for all handles.
5. **Use the exact format** shown above — key-value header, pipe-delimited fields.
6. **Omit keys that don't apply** — e.g., don't include `mount:` if there's no mount, don't include `localizable` column if single site, don't include `collection_relationship` if no entries fields reference other collections.
7. **If a collection has `entries` fields referencing other collections**, list those collection handles in `collection_relationship`. This signals downstream skills to create those collections if they don't already exist.
8. **Every collection MUST have `has_single` and `has_archive`.**
   - Collection `has_single: false` → omit `route`. Downstream skills will NOT create templates or layouts for this collection.
   - Collection `has_archive: false` → omit `mount`. Do NOT ask the user whether they want to mount the collection.
9. **Taxonomies do NOT have `route`.** Statamic handles taxonomy routing automatically. Taxonomies DO have `has_single` to control whether terms have public pages.
10. **If requirements are unclear**, ask the user before writing. Do not guess.
11. **After writing all schema files**, tell the user the list of files created and which downstream skills to run.

## Accuracy Checks

Before finishing, verify:
- [ ] All schema files are in `schemas/`
- [ ] Each file has `schema_name` and `schema_type`
- [ ] Each collection schema has `has_single` and `has_archive`
- [ ] Collections with `has_single: false` do NOT have `route`
- [ ] Collections with `has_archive: false` do NOT have `mount`
- [ ] Taxonomy schemas do NOT have `route`
- [ ] Taxonomy schemas with `has_single: true` signal downstream skills to create template/layout/preview_targets
- [ ] Every taxonomy schema has `has_single`
- [ ] Each collection schema has at least one blueprint with fields
- [ ] Multisite schemas have `localizable` on appropriate fields
- [ ] Collections with `entries` fields referencing other collections have matching `collection_relationship` list
- [ ] No Statamic files were created — only schema `.md` files