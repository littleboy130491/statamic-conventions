# Statamic Conventions: Collections

## Collection (Configuration)

**Location:** `content/collections/{handle}.yaml`

**Purpose:** Defines how the collection behaves globally.

**Controls:**

- Routing / URLs
- Whether collection is structured, dated, or orderable
- Available blueprints
- Localization (sites)
- Default template & layout
- Taxonomies attached to entries
- Mounting to a parent entry

**Required Fields:**

- `title` — Display name in Control Panel

**Common Fields:**

- `route` — URL pattern (e.g., `{slug}`, `blog/{slug}`, `{parent_uri}/{slug}`)
- `template` — Default template for entries
- `layout` — Default layout for entries
- `blueprints` — Array of available blueprint handles
- `sites` — Array of site handles (multisite)
- `taxonomies` — Array of taxonomy handles
- `date` — Boolean, enable dated entries
- `date_behavior.past` / `date_behavior.future` — Visibility rules (`public`, `private`, `unlisted`)
- `sort_by` / `sort_dir` — Default sorting for non-structured collections
- `structure` — Object with `root` (boolean) and `max_depth` for hierarchical collections
- `mount` — Entry ID to mount collection under
- `revisions` — Boolean, enable revision history
- `inject` — Default values inherited by all entries

**Reference:** https://statamic.dev/collections

**Mental model:** Collection config defines how entries work, not what they contain.

---

## Collection Blueprint

**Location:** `resources/blueprints/collections/{collection}/{handle}.yaml`

**Purpose:** Defines what fields entries have and how the editor UI looks.

**Controls:**

- Fields (title, content, author, etc.)
- Field types
- Tabs & sections in Control Panel
- Validation rules
- Field visibility conditions

**Structure:**

- `title` — Blueprint display name
- `tabs` — Object containing tab definitions
    - Each tab has `display` and `sections`
    - Each section has `fields` array
    - Each field has `handle` and `field` configuration

**Notes:**

- Multiple blueprints per collection are supported
- User selects blueprint when creating new entry
- Set `hide: true` to prevent blueprint from appearing in "Create Entry" menu

**Reference:** https://statamic.dev/blueprints

**Mental model:** Blueprint defines what an entry contains.

---

## Collection Tree (Structure)

**Location:**

- Single site: `content/trees/collections/{collection}.yaml`
- Multisite: `content/trees/collections/{site}/{collection}.yaml`

**Purpose:** Defines hierarchy and order for structured collections.

**Controls:**

- Parent–child relationships
- Entry order
- Nesting depth

**Structure:**

- `tree` — Array of nodes
    - `entry` — Entry ID
    - `children` — Nested array of child nodes

**Key rules:**

- Only structured collections have trees
- Enable structure in collection config with `structure: { root: true }`
- Tree controls order and hierarchy, not content
- Entries referenced by ID

**Reference:** https://statamic.dev/structures

**Mental model:** Tree defines where entries live in the hierarchy.

---

## Entry (Content)

**Location:**

- Single site: `content/collections/{collection}/{slug}.md`
- Single site (dated): `content/collections/{collection}/{date}.{slug}.md`
- Multisite: `content/collections/{collection}/{site}/{slug}.md`
- Multisite (dated): `content/collections/{collection}/{site}/{date}.{slug}.md`

**Purpose:** Holds the actual entry content and metadata.

**Structure:** YAML frontmatter + Markdown content

**Required Fields:**

- `id` — Unique identifier (UUID)
- `title` — Entry title

**Common Fields:**

- `blueprint` — Blueprint handle (if not using collection default)
- `published` — Boolean
- `slug` — URL slug
- `date` — Publication date (dated collections)
- `author` — User reference
- `template` — Override default template
- `layout` — Override default layout
- Custom fields from blueprint

**Content Area:** Markdown below frontmatter becomes the `content` field

**Key rules:**

- One file = one entry
- Filename format depends on collection type (dated vs non-dated)
- Entry ID is auto-generated, don't change it

**Reference:** https://statamic.dev/collections#entries

**Mental model:** Entry holds the content itself.

---

## Template Resolution

**Order:**

1. Entry's `template` field (if explicitly set)
2. Collection's default `template` setting

**Convention: `@blueprint` mapping**

Set `template: @blueprint` in collection config to auto-map templates by blueprint handle.

Statamic will look for: `/resources/views/{collection}/{blueprint}.antlers.html`

**Reference:** https://statamic.dev/views#templates

**Mental model:** Entry overrides collection default.

---

## Entry Template

**Location:** `resources/views/{collection}/{slug}.antlers.html`

**Purpose:** Defines entry-specific markup and rendering.

**Convention:**

- One template per entry type or blueprint
- Lives within the collection's domain folder
- Can use `@blueprint` convention for auto-mapping

**Reference:** https://statamic.dev/views#templates

**Mental model:** Template defines what the entry looks like.

---

## Collection Layout

**Location:** `resources/views/{collection}/layout.antlers.html`

**Purpose:** Defines the outer HTML frame for collection entries.

**Key concept:**

- Layout wraps template
- Uses `{{ template_content }}` to inject template
- Can be shared across collections or collection-specific

**Reference:** https://statamic.dev/views#layouts

**Mental model:** Layout defines the frame around the entry.

---

## Partials

**Location:** `resources/views/{collection}/partials/_name.antlers.html`

**Naming convention:**

- Prefix filename with `_` (recommended best practice)
- Reference WITHOUT the underscore: `{{ partial:{collection}/partials/name }}`

**Purpose:** Reusable template fragments within the collection domain.

**Features:**

- Pass data via parameters
- Slots for content blocks: `{{ slot }}`
- Named slots via `name` parameter
- YAML front-matter for default values
- Conditional rendering with `:when` / `:unless`

**Reference:** https://statamic.dev/tags/partial

**Mental model:** Partial = reusable component with optional slots and parameters.

---

## Collection Types

### Standard Collection

- Entries sorted by field (default: title)
- No hierarchy
- Config: no special settings needed

### Dated Collection

- Entries have publication dates
- Can schedule future/past visibility
- Config: `date: true` + `date_behavior`
- Filename: `{date}.{slug}.md`

### Orderable Collection

- Manual drag-and-drop ordering
- Flat list (no nesting)
- Config: `orderable: true`

### Structured Collection

- Hierarchical parent-child relationships
- Tree-based ordering
- Enables `{parent_uri}` and `{depth}` in routes
- Config: `structure: { root: true, max_depth: 3 }`

**Reference:** https://statamic.dev/collections#ordering

---

## Mounting Collections

**Purpose:** Attach a collection to an entry so entries become "children" of that page.

**How to:**

- Set `mount: {entry_id}` in collection config
- Or configure via Control Panel

**Effects:**

- Collection inherits the mount entry's URL as base
- `{mount}` variable available in routes
- Shows shortcut links in structure tree

**Reference:** https://statamic.dev/collections#mounting

---

## Taxonomies

**Purpose:** Categorize entries with terms (categories, tags, etc.)

**How to attach:**

- Add `taxonomies` array to collection config
- Taxonomy fields appear in entry sidebar automatically

**Usage in templates:**

```
{{ taxonomy:categories }}
  {{ title }}
{{ /taxonomy:categories }}

```

**Reference:** https://statamic.dev/taxonomies

---

## Multisite

**Content folders (per site handle):**

- Entries: `content/collections/{collection}/{site}/`
- Trees: `content/trees/collections/{site}/`

**Shared across sites:**

- Collection config: `content/collections/{collection}.yaml`
- Blueprints: `resources/blueprints/collections/{collection}/`

**Configuration:**

- Add `sites` array to collection config
- Mark fields as `localizable: true` in blueprint

**Reference:** https://statamic.dev/multi-site

**Mental model:** Each site has its own entries, sharing blueprints and config.

---

## Recommended View Structure (Domain-Driven)

```
resources/views/
└── {collection}/
    ├── layout.antlers.html
    ├── index.antlers.html      # Collection listing
    ├── show.antlers.html       # Single entry (default)
    ├── {blueprint}.antlers.html # Blueprint-specific templates
    └── partials/
        ├── _card.antlers.html
        ├── _hero.antlers.html
        └── _sidebar.antlers.html

```

---

## Quick Reference

| Concept | Location | Defines | Docs |
| --- | --- | --- | --- |
| Collection | `content/collections/{handle}.yaml` | How entries work | [Link](https://statamic.dev/collections) |
| Blueprint | `resources/blueprints/collections/{collection}/{handle}.yaml` | What fields exist | [Link](https://statamic.dev/blueprints) |
| Tree | `content/trees/collections/{collection}.yaml` | Hierarchy & order | [Link](https://statamic.dev/structures) |
| Entry | `content/collections/{collection}/*.md` | Actual content | [Link](https://statamic.dev/collections#entries) |
| Template | `resources/views/{collection}/{template}.antlers.html` | How entry looks | [Link](https://statamic.dev/views#templates) |
| Layout | `resources/views/{collection}/layout.antlers.html` | Entry frame/shell | [Link](https://statamic.dev/views#layouts) |
| Partial | `resources/views/{collection}/partials/_*.antlers.html` | Reusable fragments | [Link](https://statamic.dev/tags/partial) |
| Multisite Entries | `content/collections/{collection}/{site}/` | Per-site content | [Link](https://statamic.dev/multi-site) |
| Multisite Trees | `content/trees/collections/{site}/{collection}.yaml` | Per-site hierarchy | [Link](https://statamic.dev/multi-site) |