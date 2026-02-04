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

**Setting the template value:**

Set `template` in collection config to point to a template view file.

```yaml
template: 'blog/show'  # Looks for resources/views/blog/show.antlers.html
```

The template value should be a path to your view file (relative to `resources/views/`), without the file extension.

**Reference:** https://statamic.dev/views#templates

**Mental model:** Entry overrides collection default.

---

## Layout vs Template: Key Distinction

Understanding the difference between **layouts** and **templates** is fundamental:

| Aspect | Layout | Template |
|--------|--------|----------|
| **Purpose** | HTML shell/frame | Content-specific markup |
| **Contains** | `<head>`, global styles, shared components | Entry-specific content |
| **Reusability** | Used by all entries/collections | Used by specific entry types |
| **Entry Data** | Minimal (globals only) | Full access to entry variables |
| **Modifies** | Rarely changed | Customized per blueprint |
| **Example** | `layout.antlers.html` | `show.antlers.html`, `post.antlers.html` |

**Analogy:** Layout is the picture frame; template is the painting inside.

---

## Layout

**Location:** `resources/views/layout.antlers.html` (global) or `resources/views/{collection}/layout.antlers.html` (collection-specific)

**Purpose:** The overall HTML wrapper that serves every entry.

**Contains:**

1. **HTML Structure**
   - `<!DOCTYPE html>` declaration
   - `<html>`, `<head>`, `<body>` tags
   - Opening and closing tags for the entire document

2. **Head Section**
   - Meta tags (charset, viewport, SEO)
   - `<title>` tag (often from `{{ seo_pro:meta }}` or `{{ title }}`)
   - Global stylesheets (CSS frameworks, custom CSS)
   - Fonts and preconnects
   - Yield hooks for page-specific head content: `{{ yield:head }}`, `{{ yield:styles }}`

3. **Shared Components** (via partials or inline)
   - Header with logo
   - Site navigation (`{{ partial:navigation }}`)
   - Footer with copyright, links, social icons
   - Analytics scripts (GTM, Google Analytics)

4. **Template Injection Point**
   - `{{ template_content }}` — where templates render their content

5. **Scripts Section**
   - Global JavaScript (jQuery, Alpine.js, etc.)
   - Yield hook for page-specific scripts: `{{ yield:scripts }}`

**Key Principle:** The layout should **not** contain entry-specific logic. It only knows about globals and provides hooks for customization.

**Reference:** https://statamic.dev/views#layouts

**Mental model:** Layout = reusable HTML frame that stays consistent across the site.

---

## Entry Template

**Location:** `resources/views/{collection}/{template}.antlers.html`

**Purpose:** Content-specific view that extends the layout with entry data.

**Contains:**

- Entry-specific markup using field variables from the blueprint
- Entry sections (hero, content blocks, sidebars)
- `{{ section:* }}` tags to inject into layout hooks
- Entry loops, conditionals, and field rendering

**Accesses:**
- All entry fields (`{{ title }}`, `{{ content }}`, custom fields)
- Collection data
- Globals (`{{ site:name }}`, `{{ globals:handle }}`)
- Taxonomy terms attached to the entry
- Related entries

**Convention:**

- One template per entry type or blueprint
- Lives within the collection's domain folder
- Set template explicitly in collection config or entry

**Common Templates:**

- `show.antlers.html` — Single entry view (default)
- `index.antlers.html` — Collection listing page
- `{blueprint}.antlers.html` — Blueprint-specific templates

**Reference:** https://statamic.dev/views#templates

**Mental model:** Template = what makes each unique entry look different.

---

## Partials

**Location:** `resources/views/partials/_name.antlers.html` (global) or `resources/views/{collection}/partials/_name.antlers.html` (collection-specific)

**Purpose:** Reusable template fragments.

**Naming convention:**

- Prefix filename with `_` (recommended best practice)
- Reference WITHOUT the underscore: `{{ partial:name }}` or `{{ partial:{collection}/partials/name }}`

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

## Recommended View Structure

```
resources/views/
├── layout.antlers.html          # Global HTML wrapper (head, styles, scripts)
├── partials/                    # Shared components used across all collections
│   ├── _header.antlers.html     # Site header with navigation
│   ├── _footer.antlers.html     # Site footer
│   └── _navigation.antlers.html # Main navigation menu
└── {collection}/                # Collection-specific templates
    ├── index.antlers.html       # Collection listing page
    ├── show.antlers.html        # Single entry (default)
    ├── {blueprint}.antlers.html # Blueprint-specific templates
    └── partials/                # Collection-specific partials
        ├── _card.antlers.html   # Entry card for listings
        ├── _hero.antlers.html   # Hero section
        └── _sidebar.antlers.html # Sidebar content
```

**Key Points:**

- `layout.antlers.html` in root `views/` = shared by all collections
- `partials/` in root `views/` = reusable components (header, footer, nav)
- Collection-specific templates in `views/{collection}/` = content markup only
- Collection-specific partials in `views/{collection}/partials/` = entry-specific components

---

## Quick Reference

| Concept | Location | Defines | Docs |
| --- | --- | --- | --- |
| Collection | `content/collections/{handle}.yaml` | How entries work | [Link](https://statamic.dev/collections) |
| Blueprint | `resources/blueprints/collections/{collection}/{handle}.yaml` | What fields exist | [Link](https://statamic.dev/blueprints) |
| Tree | `content/trees/collections/{collection}.yaml` | Hierarchy & order | [Link](https://statamic.dev/structures) |
| Entry | `content/collections/{collection}/*.md` | Actual content | [Link](https://statamic.dev/collections#entries) |
| **Layout** | `resources/views/layout.antlers.html` | HTML frame (head, styles, scripts, header, footer) | [Link](https://statamic.dev/views#layouts) |
| **Template** | `resources/views/{collection}/{template}.antlers.html` | Content markup using entry fields | [Link](https://statamic.dev/views#templates) |
| Partial | `resources/views/partials/_*.antlers.html` | Reusable components | [Link](https://statamic.dev/tags/partial) |
| Multisite Entries | `content/collections/{collection}/{site}/` | Per-site content | [Link](https://statamic.dev/multi-site) |
| Multisite Trees | `content/trees/collections/{site}/{collection}.yaml` | Per-site hierarchy | [Link](https://statamic.dev/multi-site) |