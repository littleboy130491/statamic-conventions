# Create Schema: Navigation

Create navigation schema files in `schemas/` that define menu structure and items. These schemas are consumed by the `create-navigations` downstream skill.

## Scope

**You MUST only create or edit `.md` files within the `schemas/` directory at the project root.**

Do NOT create any Statamic files (navigation configs, trees, blueprints, PHP, templates). Those are the job of the `create-navigations` downstream skill.

You may **read** any project file to inform your decisions, but do not modify them.

## Quick Start

1. **Detect multisite** — Read `resources/sites.yaml` (read only)
2. **Detect existing collections** — Read `content/collections/` to know which collections and entries exist
3. **Ask the user** what navigations they need and what items each should contain
4. **Write one schema file per navigation** to `schemas/`

## Workflow

### Step 1: Detect Multisite

Read `resources/sites.yaml` (or `config/statamic/sites.php`) — **read only, do not modify**.

- Single site → single tree
- Multisite → tree per site, note sites in schema

### Step 2: Detect Existing Collections & Entries

Read `content/collections/` to understand what collections exist. Then scan entry files to know available slugs. This lets you validate that entry references in the navigation tree are correct.

Also check `schemas/*.md` for any collection schemas not yet created — those entries may not exist yet but will be created by downstream skills.

### Step 3: Gather Requirements

Ask the user (or infer from their request):

- Navigation handle and display title (e.g., `header` / "Header Navigation")
- Max nesting depth
- Which collections the nav should be able to pull entries from
- The full tree structure: which items, what order, what nesting

### Step 4: Write Schema File

Create `schemas/{handle}_nav.md` using the format below.

## Schema File Format

**Filename:** `schemas/{handle}_nav.md`

```
schema_name: {handle}
schema_type: navigation
title: {Display Title}
max_depth: {number}
collections: - {collection1} - {collection2}
taxonomies: {- taxonomy1 - taxonomy2, only if term items exist}
multisite: {true/false}
sites: {- site1 - site2, only if multisite: true}

tree:
- {Label} | {type} | {reference}
  - {Child Label} | {type} | {reference}
    - {Grandchild Label} | {type} | {reference}
```

### Item Types

Each tree item uses the format: `{Label} | {type} | {reference}`

| Type | Reference Format | Description |
|------|-----------------|-------------|
| `entry` | `{collection}/{slug}` | Links to a Statamic entry. Downstream skill resolves the UUID. |
| `archive` | `{collection}` | Links to a collection's mounted archive page (e.g., `/blog`). Downstream skill resolves the mount entry UUID. |
| `term` | `{taxonomy}/{slug}` | Links to a taxonomy term page. Downstream skill resolves the URL from the taxonomy's route config. |
| `link` | `{url}` | External or hardcoded URL. |
| `text` | _(omit)_ | Non-clickable label, typically a section header with children. |

**Why `term` is a `link` under the hood:** Statamic's navigation system only supports entries, hardcoded URLs, and text natively — it does NOT support taxonomy terms as a node type ([open feature request](https://github.com/statamic/ideas/issues/581)). The `term` type in this schema is a convenience that tells the downstream skill to look up the term's URL from the taxonomy route config and generate a hardcoded `link` node. This way you don't have to manually figure out the URL.

### Tree Indentation

- Top-level items: `- Item`
- Children (depth 2): `  - Item` (2 spaces)
- Grandchildren (depth 3): `    - Item` (4 spaces)
- Each additional depth: +2 spaces

The tree depth must not exceed `max_depth`.

## Examples

### Simple Header Nav

```
schema_name: header
schema_type: navigation
title: Header Navigation
max_depth: 1
collections: - pages
multisite: false

tree:
- Home | entry | pages/home
- About | entry | pages/about
- Services | entry | pages/services
- Contact | entry | pages/contact
```

### Header Nav with Dropdowns

```
schema_name: header
schema_type: navigation
title: Header Navigation
max_depth: 2
collections: - pages - services
multisite: false

tree:
- Home | entry | pages/home
- About | entry | pages/about
- Services | archive | services
  - Web Development | entry | services/web-development
  - Mobile Apps | entry | services/mobile-apps
  - Consulting | entry | services/consulting
- Blog | archive | blog_posts
- Contact | entry | pages/contact
```

### Footer Nav with Columns (Text Headers)

```
schema_name: footer
schema_type: navigation
title: Footer Navigation
max_depth: 2
collections: - pages
multisite: false

tree:
- Company | text
  - About | entry | pages/about
  - Team | entry | pages/team
  - Careers | entry | pages/careers
- Resources | text
  - Blog | archive | blog_posts
  - Documentation | link | https://docs.example.com
  - FAQ | entry | pages/faq
- Legal | text
  - Privacy Policy | entry | pages/privacy-policy
  - Terms of Service | entry | pages/terms-of-service
```

### Multisite Navigation

```
schema_name: header
schema_type: navigation
title: Header Navigation
max_depth: 2
collections: - pages - services
multisite: true
sites: - english - indonesian

tree:
- Home | entry | pages/home
- About | entry | pages/about
- Services | archive | services
  - Web Development | entry | services/web-development
  - Mobile Apps | entry | services/mobile-apps
- Contact | entry | pages/contact
```

For multisite, the downstream skill creates one tree file per site using the same structure. Entry UUIDs may differ per site if entries are localized.

### Nav with Taxonomy Terms

```
schema_name: header
schema_type: navigation
title: Header Navigation
max_depth: 2
collections: - pages
taxonomies: - categories
multisite: false

tree:
- Home | entry | pages/home
- About | entry | pages/about
- Blog | archive | blog_posts
  - News | term | categories/news
  - Tutorials | term | categories/tutorials
  - Reviews | term | categories/reviews
- Contact | entry | pages/contact
```

The downstream skill reads the taxonomy route from `content/taxonomies/categories.yaml` (e.g., `/categories/{slug}`) and generates hardcoded URL links like `/categories/news`.

### Nav with External Links

```
schema_name: header
schema_type: navigation
title: Header Navigation
max_depth: 2
collections: - pages
multisite: false

tree:
- Home | entry | pages/home
- About | entry | pages/about
- Blog | link | https://blog.example.com
- GitHub | link | https://github.com/example
- Contact | entry | pages/contact
```

## Rules

1. **Only write to** `schemas/*_nav.md`. No other files.
2. **One file per navigation** — e.g., `schemas/header_nav.md`, `schemas/footer_nav.md`.
3. **Always detect multisite first.** If multisite, include `sites` in the schema.
4. **Use snake_case** for handles.
5. **Validate entry references** — every `entry` type item must reference a `{collection}/{slug}` that exists in `content/collections/` or is defined in a `schemas/*.md` collection schema.
6. **Validate archive references** — every `archive` type item must reference a collection that has `has_archive: true` and a `mount` defined (either existing or in schemas).
7. **Validate term references** — every `term` type item must reference a `{taxonomy}/{slug}` where the taxonomy exists in `content/taxonomies/` or in a `schemas/*.md` taxonomy schema. List referenced taxonomies in the `taxonomies` header.
8. **Tree depth must not exceed `max_depth`.**
9. **Ask the user if unclear** — don't guess menu structure. The user knows what pages they want in their nav.
10. **After writing**, tell the user the file(s) created and remind them to run `create-navigations` next.

## Accuracy Checks

Before finishing, verify:
- [ ] Schema file is in `schemas/` with `_nav.md` suffix
- [ ] Has `schema_name`, `schema_type: navigation`, `title`, `max_depth`
- [ ] Has `collections` listing all collections referenced in tree items
- [ ] If multisite, has `sites` list
- [ ] Every `entry` item has valid `{collection}/{slug}` reference
- [ ] Every `archive` item references a collection with an archive/mount
- [ ] Every `term` item has valid `{taxonomy}/{slug}` reference
- [ ] If `term` items exist, `taxonomies` header lists all referenced taxonomies
- [ ] Tree indentation does not exceed `max_depth`
- [ ] No Statamic files were created — only schema `.md` files
