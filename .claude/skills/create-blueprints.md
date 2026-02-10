# Create Blueprints

Create or update Statamic blueprints for collections, taxonomies, globals, forms, navigation, assets, and users.

## Scope

**You MUST only create or edit `.yaml` files within `resources/blueprints/` or `resources/fieldsets/`.**

Do NOT create, edit, or modify any other files — including but not limited to:
- Content/entry files (`content/collections/`, `content/taxonomies/`, etc.)
- Collection configuration files (`content/collections/*.yaml`)
- View/template files (`resources/views/`)
- Config files (`config/`)
- Routes, controllers, or any PHP files
- CSS, JS, or frontend assets

If the task requires changes outside `resources/blueprints/` or `resources/fieldsets/`, stop and inform the user — do not make those changes yourself.

## Quick Start

1. Identify the blueprint type and location from the table below
2. Create the YAML file with tabs, sections, and fields
3. Match the blueprint handle to the content handle where required (forms, navigation, globals)

## Blueprint Locations

| Type | Path |
|------|------|
| Collections | `resources/blueprints/collections/{collection}/{handle}.yaml` |
| Taxonomies | `resources/blueprints/taxonomies/{taxonomy}/{handle}.yaml` |
| Globals | `resources/blueprints/globals/{handle}.yaml` |
| Forms | `resources/blueprints/forms/{handle}.yaml` |
| Navigation | `resources/blueprints/navigation/{handle}.yaml` |
| Assets | `resources/blueprints/assets/{handle}.yaml` |
| Users | `resources/blueprints/user.yaml` |

Do NOT create directories or files outside these paths.

## Blueprint Structure

Every blueprint MUST have a `title` and `tabs`. Use exactly these 4 tabs in this order:

| Tab | Purpose | Fields |
|-----|---------|--------|
| `main` | Primary content | title, content/bard, excerpt, featured_image, and other content fields |
| `sidebar` | Meta & relationships | author, categories, tags, toggles, slug, date, and other meta fields |
| `SEO Meta` | SEO Pro addon | Hardcoded — always include exactly as shown below |
| `SEO Previews` | SEO Pro addon | Hardcoded — always include exactly as shown below |

**How to build:**

1. `title` — Set to the blueprint display name (e.g., "Post", "Page", "Product")
2. `main` tab — Add sections with content fields. Each section can have a `display` name and `instructions`
3. `sidebar` tab — Add a section with meta/relationship fields
4. `SEO Meta` and `SEO Previews` tabs — Copy these exactly as-is, do not modify:

```yaml
  'SEO Meta':
    display: 'SEO Meta'
    sections:
      -
        fields:
          -
            handle: seo
            field:
              type: seo_pro
              listable: false
              display: SEO
              localizable: false
  'SEO Previews':
    display: 'SEO Previews'
    sections:
      -
        fields:
          -
            handle: seo_previews
            field:
              type: seo_pro_previews
              listable: false
              display: 'SEO Previews'
              localizable: true
              hide_display: true
              unless:
                seo.enabled: 'equals false'
```

## Field Definition

Each field follows this structure. Only include properties that are needed — omit defaults.

**Before writing any fields**, check if the project uses multisite (see [Multisite & Localization](#multisite--localization)). If it does, you MUST add `localizable: true` to every field unless it explicitly qualifies for an exception.

```yaml
-
  handle: field_name
  field:
    type: text
    display: Field Label
    instructions: Help text below field
    placeholder: Placeholder text
    validate:
      - required
      - min:3
    default: Default value
    localizable: true          # REQUIRED if multisite is enabled — do not omit
    visibility: visible
    width: 50
    if:
      other_field: true
```

## Common Fieldtypes

### Text Fields
```yaml
# text
type: text
input_type: email  # text, email, url, tel
character_limit: 100

# textarea
type: textarea
character_limit: 300
rows: 3

# slug
type: slug
from: title
```

### Rich Content
```yaml
# bard
type: bard
buttons:
  - h2
  - h3
  - bold
  - italic
  - unorderedlist
  - link
  - image
sets:
  - text_block
  - image_block

# markdown
type: markdown
```

### Selection
```yaml
# select
type: select
options:
  draft: Draft
  published: Published
multiple: false
clearable: true

# toggle
type: toggle
default: false
inline_label: Enable feature

# checkboxes
type: checkboxes
options:
  wifi: WiFi
  parking: Parking
```

### Relationships
```yaml
# entries
type: entries
collections:
  - posts
max_items: 3

# terms
type: terms
taxonomies:
  - categories
create: true

# users
type: users
max_items: 1

# link
type: link
collections:
  - pages
```

### Assets
```yaml
type: assets
container: assets
folder: images
max_files: 10
mode: grid
```

### Date/Time
```yaml
type: date
time_enabled: true
format: Y-m-d
```

### Structural
```yaml
# replicator
type: replicator
sets:
  text:
    display: Text Block
    fields:
      -
        handle: content
        field:
          type: bard
  image:
    display: Image Block
    fields:
      -
        handle: image
        field:
          type: assets
          max_files: 1

# grid
type: grid
fields:
  -
    handle: name
    field:
      type: text
  -
    handle: role
    field:
      type: text

# group
type: group
fields:
  -
    handle: street
    field:
      type: text
```

## Fieldsets (Reusable)

**Location:** `resources/fieldsets/{handle}.yaml` — this is the only other path you may write to.

**Create Fieldset:**
```yaml
title: 'Fieldset Name'
fields:
  -
    handle: field_name
    field:
      type: text
```

**Import in Blueprint:**
```yaml
fields:
  - import: fieldset_handle
  - import: fieldset_handle
    prefix: page_  # Optional prefix
```

## Validation Rules

```yaml
validate:
  - required
  - min:3
  - max:255
  - email
  - url
  - numeric
  - unique:users,email
  - in:draft,published
  - regex:/^[A-Z]/
  - required_if:other_field,value
```

## Conditional Fields

```yaml
# Show if field equals value
if:
  has_sidebar: true

# Multiple conditions (AND)
if:
  field_one: value
  field_two: another

# Multiple conditions (OR)
if_any:
  field_one: value
  field_two: value

# Inverse
unless:
  field: value
```

## Field Width & Layout

```yaml
# Available widths: 25, 33, 50, 66, 75, 100
-
  handle: first_name
  field:
    type: text
    width: 50
-
  handle: last_name
  field:
    type: text
    width: 50
```

## Multisite & Localization

**This check is mandatory before writing any blueprint.**

### Step 1 — Detect multisite

Before creating or editing any blueprint, read `config/statamic/sites.php` (read only — do not modify). If it defines more than one site, or if `resources/sites` contains multiple directories, the project uses multisite.

### Step 2 — Apply `localizable: true` to every field

If multisite is enabled, you MUST add `localizable: true` to **every single field** in the blueprint. Do not skip this property on any field. The only permitted exceptions are listed below — if a field does not match an exception, it MUST have `localizable: true`.

```yaml
# Default for EVERY field when multisite is enabled
field:
  type: text
  localizable: true

# Exception — only if the field meets a criteria below
field:
  type: toggle
  localizable: false
```

### Exceptions — fields that MAY use `localizable: false`

A field may use `localizable: false` only if it meets one of these criteria:
- It is an internal toggle or setting that must be identical across all sites (e.g., a "featured" flag that controls homepage logic globally)
- It is a relationship field where the same entry must be referenced in every locale
- It is a structural/layout field (e.g., template selection) that does not change per locale

If you are unsure, default to `localizable: true`.

### Fields that MUST always be localized

These field types must always have `localizable: true` — no exceptions:
- `title`, `bard`, `textarea`, `markdown`, `text` — any user-facing text
- `slug` — each locale needs its own URL
- `assets` — different images/files per locale
- SEO fields (`seo_pro`, `seo_pro_previews`)
- `date` — if publication dates may differ per locale

## Rules

1. **Only write to** `resources/blueprints/` and `resources/fieldsets/`. No exceptions.
2. **Do not create content entries, templates, config, routes, or any non-blueprint file.**
3. **Before writing any blueprint**, check if multisite is enabled. If it is, every field MUST include `localizable: true` unless it qualifies for a specific exception listed in [Multisite & Localization](#multisite--localization).
4. Blueprint handles must match content handles for forms, navigation, and globals.
5. Output valid YAML only. Tabs contain sections, sections contain fields.
6. Field `handle` is the key used in templates — use snake_case.
7. Always include all 4 tabs (`main`, `sidebar`, `SEO Meta`, `SEO Previews`).
8. Copy `SEO Meta` and `SEO Previews` tabs verbatim — do not alter them.
9. You may read other project files (config, existing blueprints, content) to inform your work, but do not modify them.

## Accuracy Checks

Before finishing, verify:
- [ ] File is valid YAML
- [ ] File path matches the correct blueprint location from the table above
- [ ] All 4 tabs are present in the correct order
- [ ] SEO tabs are copied exactly as specified
- [ ] No files were created or edited outside `resources/blueprints/` or `resources/fieldsets/`
- [ ] If multisite is enabled: every field has `localizable: true` unless it qualifies for a listed exception
- [ ] Required fields use `validate: [required]`
- [ ] Field handles use snake_case

## Quick Reference

| Fieldtype Category | Types |
|-------------------|-------|
| Text | text, textarea, slug, code, yaml |
| Rich Content | bard, markdown |
| Selection | select, radio, checkboxes, toggle, button_group |
| Relationship | entries, terms, users, link |
| Assets | assets, video |
| Date/Time | date, time |
| Number | integer, float, range |
| Structure | replicator, grid, group, table |
| Special | color, template, section, revealer |