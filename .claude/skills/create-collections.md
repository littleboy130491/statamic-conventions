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

### Step 2: Create Collection Config

**Path:** `content/collections/{handle}.yaml` — this is the only file you create in this step.

**Always include these fields:**

| Field | Value |
|-------|-------|
| `title` | Collection display name in plural form (e.g., "Blog Posts") |
| `icon` | `collections` |
| `template` | `{handle}/show` — replace `{handle}` with the collection handle |
| `layout` | `{handle}/layout` — replace `{handle}` with the collection handle |
| `revisions` | `true` |
| `route` | `/{handle}/{slug}` — replace `{handle}` with the collection handle |
| `date` | `true` |
| `sort_by` | `date` |
| `sort_dir` | `desc` |
| `date_behavior` | See hardcoded block below |
| `preview_targets` | See hardcoded block below |
| `structure` | See hardcoded block below. Set `max_depth` based on collection type |

```yaml
date_behavior:
  past: public
  future: private
preview_targets:
  -
    label: Entry
    url: '{permalink}'
    refresh: true
structure:
  root: false
  max_depth: 1
  slugs: true
```

`max_depth` guide:
- `1` — Flat collections (e.g., blog posts, news). Enables drag-and-drop ordering without nesting
- `3` or more — Hierarchical collections (e.g., pages). Enables nested parent-child structure

**Add if multisite** (multiple sites detected in Step 1):

| Field | Value |
|-------|-------|
| `sites` | List all site keys from `resources/sites.yaml` |
| `propagate` | `true` |

**Add if taxonomy relationships exist:**

You may include a `taxonomies` list in the collection config you are creating (e.g., `categories`, `tags`).

Before adding a taxonomy handle to the list, check if that taxonomy already exists at `content/taxonomies/{handle}.yaml` (read only). If it does not exist, ask the user whether they want to create it. If yes, use the `create-taxonomies` skill — do not create taxonomy files from this skill. To attach the taxonomy to an existing collection, use the `attach-taxonomies` skill.

## Rules

1. **Only write to** `content/collections/{handle}.yaml`. No other files.
2. **Do not create blueprints** — use `create-blueprints` skill instead.
3. **Do not create entries** — use `create-entries` skill instead.
4. **Do not mount collections** — use `mount-collections` skill instead.
5. **Do not create taxonomy files** — use `create-taxonomies` skill instead. To attach taxonomies to existing collections, use `attach-taxonomies` skill. If a taxonomy in the `taxonomies` list does not exist yet, ask the user before proceeding.
6. **Do not create or edit templates, config, routes, PHP, or frontend files.**
7. You may read any project file to inform your work, but do not modify files outside the allowed path.
8. Output valid YAML only.
9. Always detect multisite in Step 1 before writing the collection config. If multisite is enabled, you MUST include `sites` and `propagate` fields.
10. Always include all required fields from the table in Step 2.

## Accuracy Checks

Before finishing, verify:
- [ ] File is valid YAML
- [ ] File path is `content/collections/{handle}.yaml`
- [ ] All required fields from the Step 2 table are present
- [ ] `date_behavior`, `preview_targets`, and `structure` blocks are included exactly as specified
- [ ] If multisite is enabled: `sites` lists all site keys and `propagate: true` is set
- [ ] No blueprints, entries, templates, config, or other out-of-scope files were created or edited