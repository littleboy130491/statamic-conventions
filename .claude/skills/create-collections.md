# Create Collections

Create or update Statamic collection configuration with proper multisite support.

## Quick Start

1. **Detect multisite first** — Check `resources/sites.yaml`
2. Create `content/collections/{handle}.yaml` with collection config
3. Use separate skills for blueprints and entries

## Workflow

### Step 1: Detect Multisite

Check `resources/sites.yaml` (or `config/statamic/sites.php`):
- Single site: One site key defined
- Multisite: Multiple site keys (e.g., `english`, `indonesian`)

### Step 2: Create Collection Config

**Path:**
`content/collections/{handle}.yaml`


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

**Add if multisite** (multiple sites in `resources/sites.yaml`):

| Field | Value |
|-------|-------|
| `sites` | List all site keys from `resources/sites.yaml` |
| `propagate` | `true` |

**Add if taxonomy relationships exist:**

List each taxonomy handle under `taxonomies` (e.g., `categories`, `tags`)


### Step 3: Mount Collection (Optional)

Ask the user if they want to mount this collection to a page. If yes:

1. Create a page entry using rule from `create-entries.md` with filename `{collection}.md` and set `template: '{collection}/index'`
2. Get the `id` from the created page entry file
3. Add `mount: {page-id}` to `content/collections/{handle}.yaml` — replace `{page-id}` with the `id` value from the entry created in 2

## Boundaries

- Do NOT create blueprints here — Use `create-blueprints`
- Do NOT create entries here — Use `create-entries` (except for mount page entries in Step 3)
