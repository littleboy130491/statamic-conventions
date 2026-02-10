# Create Terms

Create Statamic taxonomy term files with dummy content.

## Scope

**You MUST only create files at:**
- `content/taxonomies/{taxonomy}/{slug}.yaml`

This skill is for **taxonomy terms only**. Do NOT create collection entries — use the `create-entries` skill instead. For adding translations to existing terms, use the `create-translations-terms` skill.

Do NOT create, edit, or modify any other files — including but not limited to:
- Collection entry files (`content/collections/`) — use `create-entries` skill instead
- Blueprint files (`resources/blueprints/`) — use `create-blueprints` skill instead
- Taxonomy config files (`content/taxonomies/*.yaml` at the root level) — use `create-taxonomies` skill instead
- Collection config files (`content/collections/*.yaml`) — use `create-collections` skill instead
- View/template files (`resources/views/`)
- Config files (`config/`)
- Fieldset files (`resources/fieldsets/`)
- Routes, controllers, or any PHP files
- CSS, JS, or frontend assets

You may **read** other project files (e.g., `resources/sites.yaml`, blueprints, taxonomy configs) to inform your work, but do not modify them.

If the task requires changes outside the allowed paths, stop and inform the user — do not make those changes yourself.

## Quick Start

1. **Verify taxonomy exists** — Read `content/taxonomies/{taxonomy}.yaml` (read only). If it does not exist, ask the user whether to create it using the `create-taxonomies` skill
2. **Read the blueprint** — Read the blueprint at `resources/blueprints/taxonomies/{taxonomy}/{blueprint}.yaml` (read only). If no specific blueprint exists, terms use `title` only
3. Create `.yaml` term files
4. **Multisite only** — For adding translations to terms, use the `create-translations-terms` skill

## Term Path

`content/taxonomies/{taxonomy}/{slug}.yaml`

Taxonomy terms do NOT use site subdirectories. Do NOT create files at paths like `content/taxonomies/{taxonomy}/{site}/{slug}.yaml` — that is wrong.

## Term Structure

1. Read the blueprint file at `resources/blueprints/taxonomies/{taxonomy}/{blueprint}.yaml` — **read only, do not modify**. If no blueprint exists, terms only need a `title`
2. Use the fields defined in the blueprint to build the YAML content
3. Add `title` as a required field
4. Populate each field from the blueprint with appropriate dummy content matching its fieldtype
5. **Always include `updated_at` and `updated_by`** — see [Updated At / Updated By](#updated-at--updated-by)

```yaml
title: {term title}
{field_from_blueprint}: {value matching fieldtype}
updated_at: {unix-timestamp-now}
updated_by: {super-user-id}
```

### Minimal Term (no blueprint or blueprint with title only)

```yaml
title: {term title}
updated_at: {unix-timestamp-now}
updated_by: {super-user-id}
```

### Example

File: `content/taxonomies/categories/web-design.yaml`

```yaml
title: Web Design
description: We build modern websites
menu_order: 1
updated_at: 1770714989
updated_by: a1b2c3d4-e5f6-7890-abcd-ef1234567890
```

## Updated At / Updated By

Every term MUST include these two fields:

- **`updated_at`** — Unix timestamp (seconds since epoch) of the current time
- **`updated_by`** — The `id` of the super user

**Finding the super user ID:**
1. Read the `users/` directory — it contains `{user_email_address}.yaml` files
2. Find the first `.yaml` file that has the field `super: true`
3. Use the `id` field value from that user file as `updated_by`

## Naming Convention

- Filename is the **slug** of the term: `my-term-name.yaml`
- The slug must be URL-safe: lowercase, hyphens instead of spaces, no special characters
- The `title` field in the YAML is the human-readable display name

## Rules

1. **Only create** `.yaml` term files at `content/taxonomies/{taxonomy}/{slug}.yaml`. No other files.
2. **Do NOT create term files in site subdirectories.** Taxonomy terms do not use site subdirectories.
3. **Do not add translations** — use `create-translations-terms` skill instead.
4. **Do not create collection entries** — use `create-entries` skill instead.
5. **Do not create or edit blueprints** — use `create-blueprints` skill instead.
6. **Do not create or edit taxonomy configs** — use `create-taxonomies` skill instead.
7. **Do not create or edit collection configs** — use `create-collections` skill instead.
8. **Do not create or edit templates, config, routes, PHP, or frontend files.**
9. You may read any project file to inform your work (blueprints, sites config, taxonomy configs, existing entries), but do not modify them.
10. Output `.yaml` files with valid YAML content.
11. Filename is the term slug — lowercase, hyphenated, no special characters.
12. Verify the taxonomy exists before creating terms. If it does not exist, ask the user first.

## Accuracy Checks

Before finishing, verify:
- [ ] Term file is `.yaml` with valid YAML content
- [ ] File path is `content/taxonomies/{taxonomy}/{slug}.yaml` — no site subdirectory
- [ ] Taxonomy exists at `content/taxonomies/{taxonomy}.yaml`
- [ ] Filename is a valid slug (lowercase, hyphenated)
- [ ] `title` field is present
- [ ] If a blueprint exists: all blueprint fields are populated with appropriate content
- [ ] No `localizations` key was added (use `create-translations-terms` for that)
- [ ] No term files were created in site subdirectories
- [ ] No collection entries, blueprints, taxonomy configs, or other out-of-scope files were created or edited