# Create Entries

Create Statamic entry files with dummy content for collections and pages.

## Scope

**You MUST only create files at:**
- Single site: `content/collections/{collection}/{slug}.md` or `content/collections/{collection}/{date}.{slug}.md`
- Multisite (default site only): `content/collections/{collection}/{default-site-handle}/{slug}.md` or `content/collections/{collection}/{default-site-handle}/{date}.{slug}.md`

This skill is for **collection entries only**. Do NOT create taxonomy terms — use the `create-terms` skill instead.

Do NOT create, edit, or modify any other files — including but not limited to:
- Taxonomy term files (`content/taxonomies/{taxonomy}/`) — use `create-terms` skill instead
- Translation entries for non-default sites — use `create-translations` skill instead
- Blueprint files (`resources/blueprints/`) — use `create-blueprints` skill instead
- Collection config files (`content/collections/*.yaml`) — use `create-collections` skill instead
- Taxonomy config files (`content/taxonomies/*.yaml`) — use `create-taxonomies` skill instead
- View/template files (`resources/views/`)
- Config files (`config/`)
- Fieldset files (`resources/fieldsets/`)
- Routes, controllers, or any PHP files
- CSS, JS, or frontend assets

You may **read** other project files (e.g., `resources/sites.yaml`, blueprints, collection configs, existing terms) to inform your work, but do not modify them.

If the task requires changes outside the allowed paths, stop and inform the user — do not make those changes yourself.

## Quick Start

1. **Detect multisite first** — Read `resources/sites.yaml` (read only)
2. **Check collection config** — Read `content/collections/{collection}.yaml` (read only) to determine if the collection uses dated entries (`date: true`). See [Detecting Dated Collections](#detecting-dated-collections)
3. **Read the blueprint** — Read the blueprint at `resources/blueprints/collections/{collection}/` (read only). If no specific blueprint exists, check `resources/blueprints/default.yaml`
4. **Validate relationship fields** — For any relationship field (`entries`, `terms`, `users`), verify referenced items exist before using them. See [Relationship Field Validation](#relationship-field-validation)
5. Create `.md` file with YAML frontmatter + optional markdown content
6. Generate UUID for `id` field
7. **Multisite only** — Create entries for the default site only. For non-default site translations, use the `create-translations` skill

## Detecting Dated Collections

Read the collection config at `content/collections/{collection}.yaml` — **read only, do not modify**.

If the config contains `date: true`, the collection uses dated entries and the filename MUST use the `{YYYY-MM-DD-HHmm}.{slug}.md` format. If `date: true` is not present, use the standard `{slug}.md` format.

Example collection config (`content/collections/posts.yaml`):
```yaml
title: Posts
date: true
sort_dir: desc
```

## Entry Paths

**Single site:**
- Standard (no `date: true`): `content/collections/{collection}/{slug}.md`
- Dated (`date: true`): `content/collections/{collection}/{YYYY-MM-DD-HHmm}.{slug}.md`

**Multisite (default site only):**
- Standard (no `date: true`): `content/collections/{collection}/{default-site-handle}/{slug}.md`
- Dated (`date: true`): `content/collections/{collection}/{default-site-handle}/{YYYY-MM-DD-HHmm}.{slug}.md`

Do NOT create entry files under non-default site directories — use the `create-translations` skill for those.

## Entry Structure

1. Read the blueprint file at `resources/blueprints/collections/{collection}/{blueprint}.yaml` — **read only, do not modify**
2. Use the fields defined in the blueprint to build the YAML frontmatter
3. Add `id` (UUID) and `title` as required fields
4. Populate each field from the blueprint with appropriate dummy content matching its fieldtype
5. If the blueprint has a `bard` or `markdown` field named `content`, place that content after the closing `---` as markdown body
6. **Always include `updated_at` and `updated_by`** — see [Updated At / Updated By](#updated-at--updated-by)

**Single site:**
```yaml
---
id: {generated-uuid}
title: {entry title}
blueprint: {blueprint handle}
{field_from_blueprint}: {value matching fieldtype}
updated_at: {unix-timestamp-now}
updated_by: {super-user-id}
---
{markdown body if blueprint has a content field}
```

**Multisite (default site entry):**
```yaml
---
id: {generated-uuid}
title: {entry title}
blueprint: {blueprint handle}
{field_from_blueprint}: {value matching fieldtype}
updated_at: {unix-timestamp-now}
updated_by: {super-user-id}
---
{markdown body if blueprint has a content field}
```


## Relationship Field Validation

When a blueprint field is a relationship type (e.g., `entries`, `terms`, `users`), you MUST verify that the referenced items actually exist before using them as values.

**Check these locations based on the relationship type:**

| Relationship type | Where to check (single site) | Where to check (multisite) |
|---|---|---|
| **Collection entries** | `content/collections/{collection_handle}/` — `.md` files | `content/collections/{collection_handle}/{site_handle}/` — `.md` files |
| **Taxonomy terms** | `content/taxonomies/{taxonomy_handle}/` — `.yaml` files | `content/taxonomies/{taxonomy_handle}/` — `.yaml` files |
| **Users** | `users/` — `.yaml` files | `users/` — `.yaml` files |

**Steps:**
1. Identify relationship fields in the blueprint (fieldtypes like `entries`, `terms`, `users`, or any field with a `collections`, `taxonomies`, or `type: users` config)
2. Read the relevant directory to discover existing items — **read only, do not modify or create**
3. Only reference items that actually exist — never invent slugs or IDs that don't correspond to real files
4. If no valid items exist to reference, leave the field empty or as an empty array (`[]`)

Do NOT create taxonomy terms to satisfy relationship fields — use the `create-terms` skill for that. If terms do not exist and are needed, ask the user whether they want to create them first.

## Updated At / Updated By

Every entry MUST include these two fields in the frontmatter:

- **`updated_at`** — Unix timestamp (seconds since epoch) of the current time
- **`updated_by`** — The `id` of the super user

**Finding the super user ID:**
1. Read the `users/` directory — it contains `{user_email_address}.yaml` files
2. Find the first `.yaml` file that has the field `super: true`
3. Use the `id` field value from that user file as `updated_by`

Example user file (`users/admin@example.com.yaml`):
```yaml
---
id: a1b2c3d4-e5f6-7890-abcd-ef1234567890
name: Admin User
super: true
---
```

Resulting entry fields:
```yaml
updated_at: 1770714989
updated_by: a1b2c3d4-e5f6-7890-abcd-ef1234567890
```

## Generating UUIDs

Use standard UUID v4 format:
```
550e8400-e29b-41d4-a716-446655440000
```

Each entry MUST have a unique ID.

## Dated Entries

**Filename format:** `{YYYY-MM-DD-HHmm}.{slug}.md` — always use today's date and current time.

Example: `2026-02-09-1700.my-first-post.md`

**Date/time fields** use Unix timestamps (seconds since epoch).

```yaml
---
id: unique-uuid
title: My First Post
updated_at: 1770714989
---
```

## Rules

1. **Only create** `.md` entry files at the paths listed in [Entry Paths](#entry-paths). No other files.
2. **Do not create taxonomy terms** — use `create-terms` skill instead.
3. **Do not create translation entries** for non-default sites — use `create-translations` skill instead.
4. **Do not create or edit blueprints** — use `create-blueprints` skill instead.
5. **Do not create or edit collection configs** — use `create-collections` skill instead.
6. **Do not create or edit taxonomy configs** — use `create-taxonomies` skill instead.
7. **Do not create or edit templates, config, routes, PHP, or frontend files.**
8. You may read any project file to inform your work (blueprints, sites config, collection configs, existing terms/entries), but do not modify them.
9. Output `.md` files with valid YAML frontmatter.
10. Every entry MUST have a unique UUID v4 in the `id` field.
11. Filename is slug-based, NOT UUID-based.
12. Relationship fields must only reference items that actually exist — never invent references.

## Accuracy Checks

Before finishing, verify:
- [ ] Entry file is `.md` with valid YAML frontmatter
- [ ] File path matches the correct pattern from [Entry Paths](#entry-paths)
- [ ] `id` field contains a unique UUID v4
- [ ] Filename uses slug, not UUID
- [ ] Dated entries have date in both filename and frontmatter
- [ ] Relationship fields only reference items that actually exist in the project
- [ ] No taxonomy term files were created (use `create-terms` for those)
- [ ] No translation entries for non-default sites were created (use `create-translations` for those)
- [ ] No blueprints, collection configs, templates, or other out-of-scope files were created or edited