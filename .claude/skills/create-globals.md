# Create Globals

Create or update Statamic global set configuration with proper multisite support.

## Quick Start

1. **Detect multisite first** — Check `resources/sites.yaml`
2. Create `content/globals/{handle}.yaml` with metadata (title, sites)
3. Create `content/globals/{site}/{handle}.yaml` with actual data
4. Use separate skill for blueprints

## Workflow

### Step 1: Detect Multisite

Check `resources/sites.yaml` (or `config/statamic/sites.php`):
- Single site: One site key defined
- Multisite: Multiple site keys (e.g., `english`, `indonesian`)

### Step 2: Create Global Set Config

**Path:** `content/globals/{handle}.yaml`

This file contains **metadata only** (title, sites). It does NOT contain the actual data.

**Always include these fields:**

| Field | Value |
|-------|-------|
| `title` | Global set display name (e.g., "Site Settings", "Contact") |

**Add if multisite** (multiple sites in `resources/sites.yaml`):

| Field | Value |
|-------|-------|
| `sites` | List all site keys from `resources/sites.yaml` |

**Single site example:**
```yaml
title: Site Settings
```

**Multisite example:**
```yaml
title: Site Settings
sites:
  - english
  - indonesian
```

### Step 3: Create Global Data File

**Path:** `content/globals/{site}/{handle}.yaml`

The actual data for the global set is stored here. Field values are at root level — NO `data:` wrapper.

| Rule | Detail |
|------|--------|
| Path | `content/globals/{site}/{handle}.yaml` — replace `{site}` with the site key from `resources/sites.yaml` |
| Format | Field values at root level — NO `data:` wrapper |

**Single site example** (e.g., site key is `default`):
```yaml
# content/globals/default/site_settings.yaml
site_name: "My Website"
tagline: "Your success is our mission"
logo: images/logo.png
email: contact@example.com
```

**Multisite examples:**
```yaml
# content/globals/english/site_settings.yaml
site_name: "My Website"
tagline: "Your success is our mission"
logo: images/logo.png
```

```yaml
# content/globals/indonesian/site_settings.yaml
site_name: "Situs Saya"
tagline: "Sukses Anda adalah misi kami"
logo: images/logo-id.png
```

### Step 4: Create Blueprint (Optional)

If the global set needs custom fieldtypes or CP editing UI, create a blueprint using rules from `create-blueprints.md`.

- Blueprint handle must match the global set handle
- Blueprint path: `resources/blueprints/globals/{handle}.yaml`

## Boundaries

- Do NOT create blueprints here — Use `create-blueprints`
- Config file (`content/globals/{handle}.yaml`) contains metadata only — NOT data
- Data file (`content/globals/{site}/{handle}.yaml`) has field values at root level — NO `data:` wrapper
- Access globals in templates with `{{ handle:field }}` syntax
