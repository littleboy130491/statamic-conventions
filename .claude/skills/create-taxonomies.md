# Create Taxonomies

Create or update Statamic taxonomy configuration with proper multisite support.

## Quick Start

1. **Detect multisite first** — Check `resources/sites.yaml`
2. Create `content/taxonomies/{handle}.yaml` with taxonomy config
3. Use separate skills for blueprints and terms

## Workflow

### Step 1: Detect Multisite

Check `resources/sites.yaml` (or `config/statamic/sites.php`):
- Single site: One site key defined
- Multisite: Multiple site keys (e.g., `english`, `indonesian`)

### Step 2: Create Taxonomy Config

**Path:**
`content/taxonomies/{handle}.yaml`

**Always include these fields:**

| Field | Value |
|-------|-------|
| `title` | Taxonomy display name in plural form (e.g., "Categories", "Tags") |
| `template` | `{handle}/show` — replace `{handle}` with the taxonomy handle |
| `layout` | `{handle}/layout` — replace `{handle}` with the taxonomy handle |
| `revisions` | `true` |
| `route` | `/{handle}/{slug}` — replace `{handle}` with the taxonomy handle |
| `preview_targets` | See hardcoded block below |

```yaml
preview_targets:
  -
    label: Entry
    url: '{permalink}'
    refresh: true
```

**Add if multisite** (multiple sites in `resources/sites.yaml`):

| Field | Value |
|-------|-------|
| `sites` | List all site keys from `resources/sites.yaml` |

### Step 3: Attach to Collections

Ask the user if they want to attach this taxonomy to any existing collections. If yes, add the taxonomy handle to the `taxonomies` list in each chosen collection config (`content/collections/{collection}.yaml`):

```yaml
taxonomies:
  - {handle}  # replace with the taxonomy handle created in Step 2
```

Also add `term_template` to the taxonomy config (`content/taxonomies/{handle}.yaml`) for each attached collection:

```yaml
term_template: '{collection}/show'  # replace {collection} with the collection handle
```

Taxonomy fields appear automatically in entry sidebar.

## Boundaries

- Do NOT create blueprints here — Use `create-blueprints`
- Do NOT create entries or terms here — Use `create-entries`
