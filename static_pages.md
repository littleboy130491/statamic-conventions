# Statamic Conventions: Static Pages

## Pages Collection (Configuration)

**Location:** `content/collections/pages.yaml`

**Purpose:** Defines how the pages collection behaves globally.

**Controls:**

- Routing / URLs
- Whether pages are hierarchical (structure)
- Localization (sites)
- Default template & layout
- Collection-level rules

**Required Fields:**

- `title` — Display name in Control Panel (e.g., "Pages")

**Common Fields:**

- `route` — URL pattern (typically `{parent_uri}/{slug}` for hierarchical pages)
- `template` — Default template for pages
- `layout` — Default layout for pages
- `blueprints` — Array of available blueprint handles
- `sites` — Array of site handles (multisite)
- `structure` — Object with `root` (boolean) and `max_depth` for hierarchical pages
- `revisions` — Boolean, enable revision history
- `inject` — Default values inherited by all pages

**Typical Configuration:**

- Structured collection with `structure.root: true` to enable homepage
- Route pattern `{parent_uri}/{slug}` for nested URLs
- Max depth to limit nesting levels

**Reference:** https://statamic.dev/collections

**Mental model:** Collection config defines how pages work, not what they contain.

---

## Pages Blueprint

**Location:** `resources/blueprints/collections/pages/page.yaml`

**Purpose:** Defines what fields a page has and how the editor UI looks.

**Controls:**

- Fields (title, content, SEO, etc.)
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

**Common Tabs:**

- `main` — Primary content fields (title, content)
- `seo` — Meta title, description, OG image
- `settings` — Template override, published status

**Notes:**

- Multiple blueprints supported (e.g., `page`, `landing`, `contact`)
- User selects blueprint when creating new page
- Set `hide: true` to prevent blueprint from appearing in "Create Page" menu (useful for one-off pages like homepage)

**Reference:** https://statamic.dev/blueprints

**Mental model:** Blueprint defines what a page contains.

---

## Page Tree (Structure)

**Location:**

- Single site: `content/trees/collections/pages.yaml`
- Multisite: `content/trees/collections/{site}/pages.yaml`

**Purpose:** Defines the hierarchy and order of pages.

**Controls:**

- Parent–child relationships
- Navigation order
- Page nesting

**Structure:**

- `tree` — Array of nodes
    - `entry` — Page entry ID
    - `children` — Nested array of child page nodes

**Key rules:**

- Only structured collections use trees
- Tree controls order and hierarchy, not content
- Entries referenced by ID
- First entry with `root: true` in collection becomes homepage (URL: `/`)

**Reference:** https://statamic.dev/structures

**Mental model:** Tree defines where pages live in the hierarchy.

---

## Page Entry (Content)

**Location:**

- Single site: `content/collections/pages/{slug}.md`
- Multisite: `content/collections/pages/{site}/{slug}.md`

**Purpose:** Holds the actual page content and metadata.

**Structure:** YAML frontmatter + Markdown content

**Required Fields:**

- `id` — Unique identifier (UUID, auto-generated)
- `title` — Page title

**Common Fields:**

- `blueprint` — Blueprint handle (if not using collection default)
- `published` — Boolean
- `slug` — URL slug
- `template` — Override default template
- `layout` — Override default layout
- `parent` — Parent page ID (for nested pages)
- Custom fields from blueprint (content, meta_title, meta_description, etc.)

**Content Area:** Markdown below frontmatter becomes the `content` field

**Key rules:**

- One file = one page
- Filename is typically UUID (slug stored in frontmatter)
- Entry ID is auto-generated, don't change it
- Tree determines hierarchy, not folder structure

**Reference:** https://statamic.dev/collections#entries

**Mental model:** Entry holds the content itself.

---

## Template Resolution

**Order:**

1. Entry's `template` field (if explicitly set)
2. Collection's default `template` setting

**Convention: `@blueprint` mapping**

Set `template: @blueprint` in collection config to auto-map templates by blueprint handle.

Statamic will look for: `/resources/views/pages/{blueprint}.antlers.html`

**Reference:** https://statamic.dev/views#templates

**Mental model:** Entry overrides collection default.

---

## Page Template

**Location:** `resources/views/pages/{slug}.antlers.html`

**Purpose:** Defines page-specific markup and rendering.

**Convention:**

- One template per page type or blueprint
- Lives within the `pages/` domain folder
- Can use `@blueprint` convention for auto-mapping

**Common Templates:**

- `default.antlers.html` — Standard page layout
- `home.antlers.html` — Homepage
- `landing.antlers.html` — Marketing landing pages
- `contact.antlers.html` — Contact page with form

**Reference:** https://statamic.dev/views#templates

**Mental model:** Template defines what the page looks like.

---

## Page Layout

**Location:** `resources/views/pages/layout.antlers.html`

**Purpose:** Defines the outer HTML frame for pages.

**Key concept:**

- Layout wraps template
- Uses `{{ template_content }}` to inject template
- Contains shared elements (doctype, head, header, footer)

**Common Elements:**

- `<!DOCTYPE html>` and `<html>` wrapper
- `<head>` with meta, title, CSS
- Header/navigation partial
- `{{ template_content }}` placeholder
- Footer partial
- JavaScript includes

**Reference:** https://statamic.dev/views#layouts

**Mental model:** Layout defines the frame around the page.

---

## Partials

**Location:** `resources/views/pages/partials/_name.antlers.html`

**Naming convention:**

- Prefix filename with `_` (recommended best practice)
- Reference WITHOUT the underscore: `{{ partial:pages/partials/name }}`

**Purpose:** Reusable template fragments within the pages domain.

**Features:**

- Pass data via parameters
- Slots for content blocks: `{{ slot }}`
- Named slots via `name` parameter
- YAML front-matter for default values
- Conditional rendering with `:when` / `:unless`

**Common Partials:**

- `_header.antlers.html` — Site header and navigation
- `_footer.antlers.html` — Site footer
- `_hero.antlers.html` — Hero/banner sections
- `_cta.antlers.html` — Call-to-action blocks
- `_seo.antlers.html` — Meta tags and OG data

**Reference:** https://statamic.dev/tags/partial

**Mental model:** Partial = reusable component with optional slots and parameters.

---

## Multisite

**Purpose:** Run multiple sites from a single Statamic installation with separate content per site.

**Content folders (per site handle):**

- Entries: `content/collections/pages/{site}/`
- Trees: `content/trees/collections/{site}/`
- Globals: `content/globals/{site}/`
- Nav Trees: `content/trees/navigation/{site}/`

**Shared across sites:**

- Collection config: `content/collections/pages.yaml`
- Blueprints: `resources/blueprints/collections/pages/`
- Templates & layouts: `resources/views/pages/`

**Configuration:**

- Add `sites` array to collection config
- Mark fields as `localizable: true` in blueprint for per-site content

**Reference:** https://statamic.dev/multi-site

**Mental model:** Each site has its own pages, sharing blueprints and views.

---

## Recommended View Structure (Domain-Driven)

```
resources/views/
└── pages/
    ├── layout.antlers.html
    ├── default.antlers.html
    ├── home.antlers.html
    ├── landing.antlers.html
    ├── contact.antlers.html
    └── partials/
        ├── _header.antlers.html
        ├── _footer.antlers.html
        ├── _hero.antlers.html
        ├── _cta.antlers.html
        └── _seo.antlers.html

```

---

## Quick Reference

| Concept | Location | Defines | Docs |
| --- | --- | --- | --- |
| Collection | `content/collections/pages.yaml` | How pages work | [Link](https://statamic.dev/collections) |
| Blueprint | `resources/blueprints/collections/pages/{handle}.yaml` | What fields exist | [Link](https://statamic.dev/blueprints) |
| Tree | `content/trees/collections/pages.yaml` | Hierarchy & order | [Link](https://statamic.dev/structures) |
| Entry | `content/collections/pages/*.md` | Actual content | [Link](https://statamic.dev/collections#entries) |
| Template | `resources/views/pages/{template}.antlers.html` | How page looks | [Link](https://statamic.dev/views#templates) |
| Layout | `resources/views/pages/layout.antlers.html` | Page frame/shell | [Link](https://statamic.dev/views#layouts) |
| Partial | `resources/views/pages/partials/_*.antlers.html` | Reusable fragments | [Link](https://statamic.dev/tags/partial) |
| Global | `content/globals/{handle}.yaml` | Site-wide settings | [Link](https://statamic.dev/globals) |
| Navigation | `content/navigation/{handle}.yaml` | Custom menus | [Link](https://statamic.dev/navigation) |
| Multisite Entries | `content/collections/pages/{site}/` | Per-site pages | [Link](https://statamic.dev/multi-site) |
| Multisite Trees | `content/trees/collections/{site}/pages.yaml` | Per-site hierarchy | [Link](https://statamic.dev/multi-site) |
| Multisite Globals | `content/globals/{site}/{handle}.yaml` | Per-site settings | [Link](https://statamic.dev/multi-site) |