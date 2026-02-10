# Mount Collections

Mount a Statamic collection to a page entry so the collection's URL structure is defined relative to that page.

## Scope

**You MUST only create or edit these files:**
- **One page entry file** in the `pages` collection (e.g., `content/collections/pages/{site}/{collection-handle}.md`) — following the rules in `create-entries.md`
- **One collection config file** at `content/collections/{handle}.yaml` — adding only the `mount` field

Do NOT create, edit, or modify any other files — including but not limited to:
- Blueprint files (`resources/blueprints/`) — use `create-blueprints` skill instead
- Other entry/content files — use `create-entries` skill instead
- Collection config files other than the one being mounted
- View/template files (`resources/views/`)
- Config files (`config/`)
- Fieldset files (`resources/fieldsets/`)
- Routes, controllers, or any PHP files
- CSS, JS, or frontend assets

You may **read** other project files (e.g., `resources/sites.yaml`, existing entries, existing collection configs) to inform your work, but do not modify them unless they are the specific files listed above.

If the task requires changes outside the allowed paths, stop and inform the user — do not make those changes yourself.

## Quick Start

1. Identify the collection to mount and the pages collection structure
2. Create a page entry for the mount point
3. Add the `mount` field to the collection config

## Workflow

### Step 1: Read Project Context

Read the following files — **read only, do not modify**:
- `resources/sites.yaml` — to detect multisite and identify site keys
- `content/collections/pages.yaml` — to understand the pages collection structure
- `content/collections/{handle}.yaml` — the collection config you will add `mount` to

### Step 2: Create the Mount Page Entry

Create a page entry in the `pages` collection following the rules in `create-entries.md`:

**Path pattern:**
- Single site: `content/collections/pages/{collection-handle}.md`
- Multisite: `content/collections/pages/{default-site}/{collection-handle}.md`

**Required frontmatter:**
```yaml
---
id: {generated-uuid}
title: '{Collection Title}'
template: '{collection-handle}/index'
---
```

- `id` — Generate a valid UUID. This value will be used in Step 3.
- `title` — Use the collection's display name (e.g., "Blog", "Projects", "Services")
- `template` — Set to `{collection-handle}/index` (e.g., `blog/index`, `projects/index`)

This is the **only** entry file you may create from this skill.

### Step 3: Add Mount to Collection Config

Edit `content/collections/{handle}.yaml` — add **only** the `mount` field:

```yaml
mount: {page-entry-id}
```

Replace `{page-entry-id}` with the `id` value from the page entry created in Step 2.

**Do not modify any other fields in the collection config.** Only add the `mount` line.

## Rules

1. **Only create** one page entry file and **only edit** one collection config file. No other files.
2. **Only add the `mount` field** to the collection config. Do not change any other fields in that file.
3. **Do not create blueprints** — use `create-blueprints` skill instead.
4. **Do not create other entries** — use `create-entries` skill instead.
5. **Do not create or edit templates, config, routes, PHP, or frontend files.**
6. You may read any project file to inform your work, but do not modify files outside the allowed paths.
7. The page entry must follow the rules in `create-entries.md` for frontmatter, ID generation, and file naming.
8. If multisite is enabled, place the page entry under the default site directory.

## Accuracy Checks

Before finishing, verify:
- [ ] Only one page entry file was created
- [ ] Page entry has a valid `id`, `title`, and `template` in frontmatter
- [ ] `template` follows the `{collection-handle}/index` pattern
- [ ] Collection config file has the `mount` field with the correct page entry `id`
- [ ] No other fields in the collection config were modified
- [ ] No blueprints, templates, config, or other out-of-scope files were created or edited