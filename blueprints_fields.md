# Statamic Conventions: Blueprints & Fields

## Blueprint (Configuration)

**Location:**

- Collections: `resources/blueprints/collections/{collection}/{handle}.yaml`
- Taxonomies: `resources/blueprints/taxonomies/{taxonomy}/{handle}.yaml`
- Globals: `resources/blueprints/globals/{handle}.yaml`
- Forms: `resources/blueprints/forms/{handle}.yaml`
- Assets: `resources/blueprints/assets/{handle}.yaml`
- Users: `resources/blueprints/user.yaml`

**Purpose:** Defines what fields content has and how the editor UI looks.

**Controls:**

- Available fields and their types
- Field configuration (validation, instructions, default values)
- Tabs & sections organization in Control Panel
- Field visibility conditions

**Required Fields:**

- `title` — Blueprint display name

**Structure:**

- `title` — Display name in Control Panel
- `tabs` — Object containing tab definitions
- `hide` — Boolean, hide from "Create" menu (optional)

**Reference:** https://statamic.dev/blueprints

**Mental model:** Blueprint defines the content structure and editing experience.

---

## Tabs

**Purpose:** Organize fields into logical groups displayed as tabs in Control Panel.

**Structure:**

```yaml
tabs:
  main:
    display: Main
    sections:
      - fields:
          - handle: title
            field:
              type: text
  sidebar:
    display: Sidebar
    sections:
      - fields:
          - handle: author
            field:
              type: users

```

**Common Tab Patterns:**

- `main` — Primary content fields
- `sidebar` — Meta fields (author, date, categories)
- `seo` — SEO-related fields
- `settings` — Configuration options
- `media` — Images, videos, files

**Key rules:**

- First tab is selected by default
- Tab handle is the key (e.g., `main`, `sidebar`)
- `display` is the visible label
- Each tab contains `sections` array

**Reference:** https://statamic.dev/blueprints#tabs

---

## Sections

**Purpose:** Group related fields within a tab.

**Structure:**

```yaml
tabs:
  main:
    display: Main
    sections:
      -
        display: Content
        instructions: Main content area
        fields:
          - handle: title
            field:
              type: text
          - handle: content
            field:
              type: bard
      -
        display: Options
        fields:
          - handle: featured
            field:
              type: toggle

```

**Optional Properties:**

- `display` — Section heading (omit for no heading)
- `instructions` — Help text below heading

**Key rules:**

- Sections are arrays within tabs
- Multiple sections create visual groupings
- Omit `display` for fields without section header

**Reference:** https://statamic.dev/blueprints#sections

---

## Fields

**Purpose:** Define individual data inputs.

**Structure:**

```yaml
fields:
  -
    handle: title
    field:
      type: text
      display: Title
      instructions: Enter the page title
      placeholder: My Awesome Page
      validate:
        - required
        - min:3
      default: Untitled
      localizable: true
      visibility: visible
      width: 50

```

**Required Properties:**

- `handle` — Field identifier (used in templates)
- `field.type` — Fieldtype name

**Common Properties:**

- `display` — Label in Control Panel
- `instructions` — Help text below field
- `placeholder` — Placeholder text (text-based fields)
- `validate` — Validation rules array
- `required` — Boolean (shorthand for validate: required)
- `default` — Default value
- `localizable` — Boolean, enable per-site values (multisite)
- `visibility` — `visible`, `read_only`, or `hidden`
- `width` — Column width percentage (25, 33, 50, 66, 75, 100)
- `if` / `unless` — Conditional display rules
- `listable` — Show in entry listings (`true`, `false`, `hidden`)

**Reference:** https://statamic.dev/blueprints#fields

---

## Fieldsets (Reusable Field Groups)

**Location:** `resources/fieldsets/{handle}.yaml`

**Purpose:** Define reusable field groups to import into blueprints.

**Structure:**

```yaml
# resources/fieldsets/seo.yaml
title: SEO Fields
fields:
  -
    handle: meta_title
    field:
      type: text
      display: Meta Title
      instructions: Override the default title tag
  -
    handle: meta_description
    field:
      type: textarea
      display: Meta Description
      character_limit: 160
  -
    handle: og_image
    field:
      type: assets
      display: Share Image
      max_files: 1

```

**Importing in Blueprint:**

```yaml
tabs:
  seo:
    display: SEO
    sections:
      -
        fields:
          - import: seo

```

**Importing with Prefix:**

```yaml
fields:
  - import: seo
    prefix: page_

```

**Overriding Imported Fields:**

```yaml
fields:
  - import: seo
  - handle: meta_title
    field:
      instructions: Custom instructions for this blueprint

```

**Key rules:**

- Fieldsets are just field definitions, no tabs/sections
- Import entire fieldset or individual fields
- Prefix prevents handle conflicts
- Override specific fields after import

**Reference:** https://statamic.dev/fieldsets

**Mental model:** Fieldset = reusable field definitions shared across blueprints.

---

## Common Fieldtypes

### Text Fields

**text**

```yaml
field:
  type: text
  display: Title
  placeholder: Enter title
  input_type: text    # text, email, url, tel, password
  character_limit: 100
  prepend: https://
  append: .com

```

**textarea**

```yaml
field:
  type: textarea
  display: Excerpt
  character_limit: 300
  rows: 3

```

**slug**

```yaml
field:
  type: slug
  display: Slug
  from: title         # Generate from this field
  separator: -

```

### Rich Content Fields

**bard**

```yaml
field:
  type: bard
  display: Content
  buttons:
    - h2
    - h3
    - bold
    - italic
    - unorderedlist
    - orderedlist
    - quote
    - link
    - image
    - table
  sets:               # Optional content blocks
    - text_block
    - image_block
  save_html: false
  toolbar_mode: fixed # fixed, floating
  enable_input_rules: true
  enable_paste_rules: true
  smart_typography: true

```

**markdown**

```yaml
field:
  type: markdown
  display: Content
  buttons:
    - h2
    - h3
    - bold
    - italic
    - unorderedlist
    - orderedlist
    - quote
    - link
    - image
  automatic_line_breaks: true
  parser: default

```

### Selection Fields

**select**

```yaml
field:
  type: select
  display: Status
  options:
    draft: Draft
    review: In Review
    published: Published
  default: draft
  clearable: false
  searchable: true
  multiple: false
  taggable: false     # Allow custom values

```

**radio**

```yaml
field:
  type: radio
  display: Layout
  options:
    full: Full Width
    sidebar: With Sidebar
  inline: true
  default: full

```

**checkboxes**

```yaml
field:
  type: checkboxes
  display: Features
  options:
    wifi: WiFi
    parking: Parking
    pool: Pool
  inline: false
  default:
    - wifi

```

**toggle**

```yaml
field:
  type: toggle
  display: Featured
  default: false
  inline_label: Mark as featured

```

**button_group**

```yaml
field:
  type: button_group
  display: Alignment
  options:
    left: Left
    center: Center
    right: Right
  default: left

```

### Relationship Fields

**entries**

```yaml
field:
  type: entries
  display: Related Posts
  collections:
    - posts
  max_items: 3
  mode: default       # default, select, typeahead
  create: true        # Allow creating new entries

```

**terms**

```yaml
field:
  type: terms
  display: Categories
  taxonomies:
    - categories
  max_items: 5
  mode: select
  create: true

```

**users**

```yaml
field:
  type: users
  display: Author
  max_items: 1
  mode: default

```

**link**

```yaml
field:
  type: link
  display: Button Link
  collections:
    - pages
    - posts
  container: assets   # For asset links

```

### Asset Fields

**assets**

```yaml
field:
  type: assets
  display: Images
  container: assets
  folder: images
  max_files: 10
  mode: list          # list, grid
  allow_uploads: true
  restrict: false     # Restrict to folder

```

### Date & Time Fields

**date**

```yaml
field:
  type: date
  display: Publish Date
  time_enabled: false
  time_required: false
  earliest_date: 2020-01-01
  format: Y-m-d
  full_width: false
  columns: 1
  rows: 1

```

**time**

```yaml
field:
  type: time
  display: Event Time
  seconds_enabled: false

```

### Number Fields

**integer**

```yaml
field:
  type: integer
  display: Quantity
  default: 1
  min: 0
  max: 100

```

**float**

```yaml
field:
  type: float
  display: Price
  default: 0.00

```

**range**

```yaml
field:
  type: range
  display: Rating
  min: 1
  max: 5
  step: 1
  default: 3

```

### Structural Fields

**replicator**

```yaml
field:
  type: replicator
  display: Content Blocks
  collapse: false
  max_sets: 10
  sets:
    text:
      display: Text Block
      fields:
        - handle: content
          field:
            type: bard
    image:
      display: Image Block
      fields:
        - handle: image
          field:
            type: assets
            max_files: 1
        - handle: caption
          field:
            type: text

```

**grid**

```yaml
field:
  type: grid
  display: Team Members
  add_row: Add Member
  min_rows: 1
  max_rows: 10
  fields:
    - handle: name
      field:
        type: text
    - handle: role
      field:
        type: text
    - handle: photo
      field:
        type: assets
        max_files: 1

```

**group**

```yaml
field:
  type: group
  display: Address
  fields:
    - handle: street
      field:
        type: text
    - handle: city
      field:
        type: text
    - handle: postal_code
      field:
        type: text

```

### Special Fields

**code**

```yaml
field:
  type: code
  display: Custom CSS
  mode: css           # css, javascript, markdown, yaml, etc.
  theme: material
  indent_type: spaces
  indent_size: 2
  line_numbers: true

```

**color**

```yaml
field:
  type: color
  display: Brand Color
  swatches:
    - '#FF0000'
    - '#00FF00'
    - '#0000FF'
  allow_any: true
  default: '#000000'

```

**video**

```yaml
field:
  type: video
  display: Video
  # Accepts YouTube, Vimeo URLs

```

**table**

```yaml
field:
  type: table
  display: Specifications

```

**template**

```yaml
field:
  type: template
  display: Template
  folder: pages       # Limit to folder
  hide_partials: true

```

**yaml**

```yaml
field:
  type: yaml
  display: Custom Data

```

**section** (UI only)

```yaml
field:
  type: section
  display: Advanced Options
  instructions: Configure advanced settings below

```

**revealer** (Conditional UI)

```yaml
field:
  type: revealer
  display: Show Advanced
  mode: button        # button, toggle

```

**Reference:** https://statamic.dev/fieldtypes

---

## Validation Rules

**Common Rules:**

```yaml
validate:
  - required
  - min:3
  - max:255
  - email
  - url
  - alpha
  - alpha_num
  - alpha_dash
  - numeric
  - integer
  - boolean
  - array
  - date
  - unique:{collection},{field}
  - exists:{collection},{field}
  - in:draft,published,archived
  - not_in:admin,root
  - regex:/^[A-Z]/
  - confirmed            # Requires {field}_confirmation
  - required_if:{field},{value}
  - required_unless:{field},{value}
  - required_with:{field}
  - required_without:{field}
  - same:{field}
  - different:{field}
  - image
  - mimes:jpg,png,gif
  - dimensions:min_width=100,min_height=100

```

**Example:**

```yaml
fields:
  -
    handle: email
    field:
      type: text
      input_type: email
      validate:
        - required
        - email
        - unique:users,email
  -
    handle: age
    field:
      type: integer
      validate:
        - required
        - integer
        - min:18
        - max:120

```

**Reference:** https://statamic.dev/validation

---

## Conditional Fields

**Purpose:** Show/hide fields based on other field values.

**Basic Syntax:**

```yaml
fields:
  -
    handle: has_sidebar
    field:
      type: toggle
      display: Enable Sidebar
  -
    handle: sidebar_content
    field:
      type: bard
      display: Sidebar Content
      if:
        has_sidebar: true

```

**Operators:**

```yaml
# Equals
if:
  field: value

# Not equals
if:
  field: not value

# Empty / Not empty
if:
  field: empty
if:
  field: not empty

# Contains (arrays)
if:
  field: contains value

# Multiple conditions (AND)
if:
  field_one: value
  field_two: another

# Multiple conditions (OR)
if_any:
  field_one: value
  field_two: value

# Unless (inverse)
unless:
  field: value

unless_any:
  field_one: value
  field_two: value

```

**Nested Field Conditions:**

```yaml
if:
  group.nested_field: value

```

**Reference:** https://statamic.dev/conditional-fields

---

## Localizable Fields (Multisite)

**Purpose:** Enable per-site values for specific fields.

**Configuration:**

```yaml
fields:
  -
    handle: title
    field:
      type: text
      localizable: true    # Different value per site
  -
    handle: slug
    field:
      type: slug
      localizable: true
  -
    handle: template
    field:
      type: template
      localizable: false   # Same across all sites

```

**Behavior:**

- `localizable: true` — Each site has independent value
- `localizable: false` (default) — Value shared across sites
- Non-localizable fields edited only in origin site

**Reference:** https://statamic.dev/multi-site#localizable-fields

---

## Field Width & Layout

**Purpose:** Control field widths for better form layout.

**Width Options:**

```yaml
fields:
  -
    handle: first_name
    field:
      type: text
      width: 50           # Half width
  -
    handle: last_name
    field:
      type: text
      width: 50           # Half width
  -
    handle: email
    field:
      type: text
      width: 100          # Full width (default)

```

**Available Widths:** 25, 33, 50, 66, 75, 100

**Pattern — Form Row:**

```yaml
# Two columns
- handle: first_name
  field:
    type: text
    width: 50
- handle: last_name
  field:
    type: text
    width: 50

# Three columns
- handle: city
  field:
    type: text
    width: 50
- handle: state
  field:
    type: select
    width: 25
- handle: zip
  field:
    type: text
    width: 25

```

---

## Common Blueprint Patterns

### Page Blueprint

```yaml
title: Page
tabs:
  main:
    display: Main
    sections:
      -
        fields:
          - handle: title
            field:
              type: text
              required: true
          - handle: content
            field:
              type: bard
              buttons:
                - h2
                - h3
                - bold
                - italic
                - unorderedlist
                - orderedlist
                - quote
                - link
                - image
  seo:
    display: SEO
    sections:
      -
        fields:
          - import: seo
  sidebar:
    display: Sidebar
    sections:
      -
        fields:
          - handle: template
            field:
              type: template
              folder: pages
          - handle: parent
            field:
              type: entries
              collections:
                - pages
              max_items: 1

```

### Post Blueprint

```yaml
title: Post
tabs:
  main:
    display: Main
    sections:
      -
        fields:
          - handle: title
            field:
              type: text
              required: true
          - handle: featured_image
            field:
              type: assets
              container: assets
              max_files: 1
          - handle: excerpt
            field:
              type: textarea
              character_limit: 300
          - handle: content
            field:
              type: bard
  sidebar:
    display: Sidebar
    sections:
      -
        fields:
          - handle: date
            field:
              type: date
              required: true
          - handle: author
            field:
              type: users
              max_items: 1
          - handle: categories
            field:
              type: terms
              taxonomies:
                - categories
          - handle: tags
            field:
              type: terms
              taxonomies:
                - tags
              create: true

```

### Settings Global Blueprint

```yaml
title: Site Settings
tabs:
  general:
    display: General
    sections:
      -
        fields:
          - handle: site_name
            field:
              type: text
              required: true
          - handle: tagline
            field:
              type: text
          - handle: logo
            field:
              type: assets
              max_files: 1
          - handle: favicon
            field:
              type: assets
              max_files: 1
  contact:
    display: Contact
    sections:
      -
        fields:
          - handle: email
            field:
              type: text
              input_type: email
          - handle: phone
            field:
              type: text
              input_type: tel
          - handle: address
            field:
              type: textarea

```

---

## Recommended Fieldset Library

```
resources/fieldsets/
├── seo.yaml              # Meta title, description, OG image
├── hero.yaml             # Hero section fields
├── cta.yaml              # Call-to-action button fields
├── social_links.yaml     # Social media links (replicator)
├── address.yaml          # Address group fields
├── contact.yaml          # Contact information fields
└── media.yaml            # Featured image, gallery

```

---

## Quick Reference

| Concept | Location | Defines | Docs |
| --- | --- | --- | --- |
| Collection Blueprint | `resources/blueprints/collections/{collection}/{handle}.yaml` | Entry fields | [Link](https://statamic.dev/blueprints) |
| Taxonomy Blueprint | `resources/blueprints/taxonomies/{taxonomy}/{handle}.yaml` | Term fields | [Link](https://statamic.dev/blueprints) |
| Global Blueprint | `resources/blueprints/globals/{handle}.yaml` | Global fields | [Link](https://statamic.dev/blueprints) |
| Form Blueprint | `resources/blueprints/forms/{handle}.yaml` | Form fields | [Link](https://statamic.dev/blueprints) |
| Asset Blueprint | `resources/blueprints/assets/{handle}.yaml` | Asset meta fields | [Link](https://statamic.dev/blueprints) |
| User Blueprint | `resources/blueprints/user.yaml` | User fields | [Link](https://statamic.dev/blueprints) |
| Fieldset | `resources/fieldsets/{handle}.yaml` | Reusable fields | [Link](https://statamic.dev/fieldsets) |

---

## Fieldtype Quick Reference

| Category | Fieldtypes |
| --- | --- |
| Text | `text`, `textarea`, `slug`, `code`, `yaml` |
| Rich Content | `bard`, `markdown` |
| Selection | `select`, `radio`, `checkboxes`, `toggle`, `button_group` |
| Relationship | `entries`, `terms`, `users`, `link` |
| Assets | `assets`, `video` |
| Date/Time | `date`, `time` |
| Number | `integer`, `float`, `range` |
| Structure | `replicator`, `grid`, `group`, `table` |
| Special | `color`, `template`, `section`, `revealer` |