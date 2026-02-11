# Create Fieldsets

Create or update Statamic fieldsets — reusable field groups that can be imported into blueprints.

## Scope

**You MUST only create or edit `.yaml` files within `resources/fieldsets/`.**

Do NOT create, edit, or modify any other files — including but not limited to:
- Blueprint files (`resources/blueprints/`)
- Content/entry files (`content/collections/`, `content/taxonomies/`, etc.)
- Collection configuration files (`content/collections/*.yaml`)
- View/template files (`resources/views/`)
- Config files (`config/`)
- Routes, controllers, or any PHP files
- CSS, JS, or frontend assets

If the task requires changes outside `resources/fieldsets/`, stop and inform the user — do not make those changes yourself.

## Quick Start

1. Determine a descriptive handle for the fieldset (snake_case)
2. Create the YAML file at `resources/fieldsets/{handle}.yaml`
3. Define the reusable fields

## Fieldset Location

| Type | Path |
|------|------|
| Fieldsets | `resources/fieldsets/{handle}.yaml` |

Do NOT create directories or files outside this path.

## Fieldset Structure

Every fieldset MUST have a `title` and `fields` array.

```yaml
title: 'Fieldset Name'
fields:
  -
    handle: field_name
    field:
      type: text
      display: Field Label
  -
    handle: another_field
    field:
      type: textarea
      display: Another Field
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

**This check is mandatory before writing any fieldset.**

### Step 1 — Detect multisite

Before creating or editing any fieldset, read `config/statamic/sites.php` (read only — do not modify). If it defines more than one site, or if `resources/sites` contains multiple directories, the project uses multisite.

### Step 2 — Apply `localizable: true` to every field

If multisite is enabled, you MUST add `localizable: true` to **every single field** in the fieldset. Do not skip this property on any field. The only permitted exceptions are listed below — if a field does not match an exception, it MUST have `localizable: true`.

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
- `date` — if publication dates may differ per locale

## Rules

1. **Only write to** `resources/fieldsets/`. No exceptions.
2. **Do not create content entries, blueprints, templates, config, routes, or any non-fieldset file.**
3. **Before writing any fieldset**, check if multisite is enabled. If it is, every field MUST include `localizable: true` unless it qualifies for a specific exception listed in [Multisite & Localization](#multisite--localization).
4. Output valid YAML only. Fieldsets contain a `title` and a `fields` array.
5. Field `handle` is the key used in templates — use snake_case.
6. You may read other project files (config, existing blueprints, fieldsets, content) to inform your work, but do not modify them.

## Accuracy Checks

Before finishing, verify:
- [ ] File is valid YAML
- [ ] File is located at `resources/fieldsets/{handle}.yaml`
- [ ] Fieldset has a `title` and `fields` array
- [ ] No files were created or edited outside `resources/fieldsets/`
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
