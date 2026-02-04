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

## Layout vs Template: Key Distinction

Understanding the difference between **layouts** and **templates** is fundamental:

| Aspect | Layout | Template |
|--------|--------|----------|
| **Purpose** | HTML shell/frame | Content-specific markup |
| **Contains** | `<head>`, global styles, shared components | Entry-specific content |
| **Reusability** | Used by all pages | Used by specific page types |
| **Entry Data** | Minimal (globals only) | Full access to entry variables |
| **Modifies** | Rarely changed | Customized per blueprint |
| **Example** | `layout.antlers.html` | `home.antlers.html`, `contact.antlers.html` |

**Analogy:** Layout is the picture frame; template is the painting inside.

---

## Layout

**Location:** `resources/views/layout.antlers.html` (global) or `resources/views/pages/layout.antlers.html` (domain-specific)

**Purpose:** The overall HTML wrapper that serves every page.

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

## Template

**Location:** `resources/views/pages/{template}.antlers.html`

**Purpose:** Content-specific view that extends the layout with entry data.

**Contains:**

- Entry-specific markup using field variables from the blueprint
- Page sections (hero, content blocks, sidebars)
- `{{ section:* }}` tags to inject into layout hooks
- Entry loops, conditionals, and field rendering

**Accesses:**
- All entry fields (`{{ title }}`, `{{ content }}`, custom fields)
- Collection data
- Globals (`{{ site:name }}`, `{{ globals:handle }}`)
- Parent/child data (for structured collections)

**Convention:**

- One template per blueprint or page type
- Filename matches blueprint handle when using `@blueprint` convention
- Lives within the collection's domain folder

**Common Templates:**

- `default.antlers.html` — Standard content page
- `home.antlers.html` — Homepage with hero, features
- `landing.antlers.html` — Marketing page with CTA sections
- `contact.antlers.html` — Contact page with form

**Reference:** https://statamic.dev/views#templates

**Mental model:** Template = what makes each unique page look different.

---

## Partials

**Location:** `resources/views/partials/_name.antlers.html`

**Purpose:** Reusable template fragments shared across all collections.

**Naming convention:**

- Prefix filename with `_` (recommended best practice)
- Reference WITHOUT the underscore: `{{ partial:name }}`

**Features:**

- Pass data via parameters
- Slots for content blocks: `{{ slot }}`
- Named slots via `name` parameter
- YAML front-matter for default values
- Conditional rendering with `:when` / `:unless`

**Common Partials:**

- `_header.antlers.html` — Site header and navigation
- `_footer.antlers.html` — Site footer
- `_navigation.antlers.html` — Main navigation menu
- `_hero.antlers.html` — Hero/banner sections
- `_cta.antlers.html` — Call-to-action blocks

**Where partials live:**

- Global partials: `resources/views/partials/`
- Collection-specific partials: `resources/views/{collection}/partials/`

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
- Templates & layouts: `resources/views/`

**Configuration:**

- Add `sites` array to collection config
- Mark fields as `localizable: true` in blueprint for per-site content

**Reference:** https://statamic.dev/multi-site

**Mental model:** Each site has its own pages, sharing blueprints and views.

---

## Recommended View Structure

```
resources/views/
├── layout.antlers.html          # Global HTML wrapper (head, styles, scripts)
├── partials/                    # Shared components used across all collections
│   ├── _header.antlers.html     # Site header with navigation
│   ├── _footer.antlers.html     # Site footer
│   └── _navigation.antlers.html # Main navigation menu
└── pages/                       # Pages collection templates
    ├── default.antlers.html     # Standard page template
    ├── home.antlers.html        # Homepage template
    ├── landing.antlers.html     # Landing page template
    └── contact.antlers.html     # Contact page template
```

**Key Points:**

- `layout.antlers.html` in root `views/` = shared by all collections
- `partials/` in root `views/` = reusable components (header, footer, nav)
- Collection-specific templates in `views/{collection}/` = content markup only

**Alternative: Domain-Specific Layouts**

For large sites with different layouts per section:

```
resources/views/
├── layout.antlers.html          # Base layout (minimal)
├── pages/
│   ├── layout.antlers.html      # Pages-specific layout extends base
│   └── default.antlers.html
├── blog/
│   ├── layout.antlers.html      # Blog-specific layout extends base
│   └── post.antlers.html
└── partials/
    └── _shared.antlers.html
```

---

## Quick Reference

| Concept | Location | Defines | Docs |
| --- | --- | --- | --- |
| Collection | `content/collections/pages.yaml` | How pages work | [Link](https://statamic.dev/collections) |
| Blueprint | `resources/blueprints/collections/pages/{handle}.yaml` | What fields exist | [Link](https://statamic.dev/blueprints) |
| Tree | `content/trees/collections/pages.yaml` | Hierarchy & order | [Link](https://statamic.dev/structures) |
| Entry | `content/collections/pages/*.md` | Actual content | [Link](https://statamic.dev/collections#entries) |
| **Layout** | `resources/views/layout.antlers.html` | HTML frame (head, styles, scripts, header, footer) | [Link](https://statamic.dev/views#layouts) |
| **Template** | `resources/views/pages/{template}.antlers.html` | Content markup using entry fields | [Link](https://statamic.dev/views#templates) |
| Partial | `resources/views/partials/_*.antlers.html` | Reusable components | [Link](https://statamic.dev/tags/partial) |
| Global | `content/globals/{handle}.yaml` | Site-wide settings | [Link](https://statamic.dev/globals) |
| Navigation | `content/navigation/{handle}.yaml` | Custom menus | [Link](https://statamic.dev/navigation) |
| Multisite Entries | `content/collections/pages/{site}/` | Per-site pages | [Link](https://statamic.dev/multi-site) |
| Multisite Trees | `content/trees/collections/{site}/pages.yaml` | Per-site hierarchy | [Link](https://statamic.dev/multi-site) |
| Multisite Globals | `content/globals/{site}/{handle}.yaml` | Per-site settings | [Link](https://statamic.dev/multi-site) |