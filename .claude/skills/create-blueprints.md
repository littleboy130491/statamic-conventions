# Create Blueprints

Create or update Statamic blueprints for collections, taxonomies, globals, forms, navigation, assets, and users.

## Quick Start

1. Identify the blueprint type and location
2. Create YAML file with tabs, sections, and fields
3. Match blueprint handle to content handle where required

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

## Blueprint Structure

Every blueprint has a `title` and `tabs`. Use exactly these 4 tabs in this order:

| Tab | Purpose | Fields |
|-----|---------|--------|
| `main` | Primary content | title, content/bard, excerpt, featured_image, and other content fields |
| `sidebar` | Meta & relationships | author, categories, tags, toggles, slug, date, and other meta fields |
| `SEO Meta` | SEO Pro addon | Hardcoded — always include as-is |
| `SEO Previews` | SEO Pro addon | Hardcoded — always include as-is |

**How to build:**

1. `title` — Set to the blueprint display name (e.g., "Post", "Page", "Product")
2. `main` tab — Add sections with content fields. Each section can have a `display` name and `instructions`
3. `sidebar` tab — Add a section with meta/relationship fields
4. `SEO Meta` and `SEO Previews` tabs — Always copy these exactly:

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
    localizable: true
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

**Location:** `resources/fieldsets/{handle}.yaml`

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

## Localizable Fields

```yaml
field:
  type: text
  localizable: true  # Different value per site
```

## Boundaries

- Blueprint handles must match content handles for forms, navigation, globals
- Do NOT create content here — Use respective create skills

## Accuracy Checks

- Blueprint is YAML format
- Tabs contain sections, sections contain fields
- Field handle is the key used in templates
- Required fields use `validate: [required]` or `required: true`

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
