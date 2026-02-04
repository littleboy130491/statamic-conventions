# Statamic Conventions: Taxonomies

## Taxonomy (Configuration)

**Location:** `content/taxonomies/{handle}.yaml`

**Purpose:** Defines how the taxonomy behaves globally.

**Controls:**

- Term URLs / routing
- Localization (sites)
- Default template & layout
- Revision settings

**Required Fields:**

- `title` — Display name in Control Panel (e.g., "Categories", "Tags")

**Common Fields:**

- `route` — URL pattern for term pages (e.g., `categories/{slug}`)
- `template` — Default template for term listing pages
- `layout` — Default layout for term pages
- `sites` — Array of site handles (multisite)
- `revisions` — Boolean, enable revision history
- `preview_targets` — Array of preview URL configurations

**Typical Configuration:**

- Simple route like `{taxonomy}/{slug}` or just `{slug}`
- Attached to collections via collection config, not taxonomy config

**Reference:** https://statamic.dev/taxonomies

**Mental model:** Taxonomy config defines how terms work, not what they contain.

---

## Taxonomy Blueprint

**Location:** `resources/blueprints/taxonomies/{taxonomy}/{handle}.yaml`

**Purpose:** Defines what fields terms have and how the editor UI looks.

**Controls:**

- Fields (title, description, image, etc.)
- Field types
- Tabs & sections in Control Panel
- Validation rules

**Structure:**

- `title` — Blueprint display name
- `tabs` — Object containing tab definitions
    - Each tab has `display` and `sections`
    - Each section has `fields` array
    - Each field has `handle` and `field` configuration

**Common Fields for Terms:**

- `title` — Term name (built-in)
- `slug` — URL slug (built-in)
- `content` — Term description (markdown/bard)
- `image` — Featured image (assets)
- `icon` — Term icon
- `color` — Term color for UI

**Notes:**

- Usually one blueprint per taxonomy
- Blueprint handle typically matches taxonomy handle
- Terms automatically get `title` and `slug` fields

**Reference:** https://statamic.dev/blueprints

**Mental model:** Blueprint defines what a term contains.

---

## Term (Content)

**Location:**

- Single site: `content/taxonomies/{taxonomy}/{slug}.yaml`
- Multisite: `content/taxonomies/{taxonomy}/{site}/{slug}.yaml`

**Purpose:** Holds the actual term content and metadata.

**Structure:** Pure YAML (no markdown content area)

**Required Fields:**

- `title` — Term display name

**Common Fields:**

- `id` — Unique identifier (auto-generated)
- `slug` — URL slug (defaults to slugified title)
- `published` — Boolean
- Custom fields from blueprint

**Key rules:**

- One file = one term
- Filename is the term slug
- Terms are created automatically when assigned to entries (if not existing)

**Reference:** https://statamic.dev/taxonomies#terms

**Mental model:** Term holds the taxonomy item content.

---

## Attaching Taxonomies to Collections

**Location:** Collection config (`content/collections/{collection}.yaml`)

**Purpose:** Make taxonomy fields available on collection entries.

**How to attach:**

- Add `taxonomies` array to collection config with taxonomy handles
- Fields appear automatically in entry sidebar

**Configuration:**

```yaml
# content/collections/posts.yaml
title: Posts
taxonomies:
  - categories
  - tags

```

**Effects:**

- Taxonomy fields added to all entries in collection
- Available regardless of which blueprint is used
- Creates bidirectional relationship (entry ↔ term)

**Reference:** https://statamic.dev/taxonomies#attaching-to-collections

**Mental model:** Collection declares which taxonomies its entries can use.

---

## Layout vs Template: Key Distinction

Understanding the difference between **layouts** and **templates** is fundamental:

| Aspect | Layout | Template |
|--------|--------|----------|
| **Purpose** | HTML shell/frame | Content-specific markup |
| **Contains** | `<head>`, global styles, shared components | Term-specific content |
| **Reusability** | Used by all pages | Used by taxonomy term pages |
| **Term Data** | Minimal (globals only) | Full access to term variables |
| **Modifies** | Rarely changed | Customized per taxonomy |
| **Example** | `layout.antlers.html` | `categories/show.antlers.html`, `tags/show.antlers.html` |

**Analogy:** Layout is the picture frame; template is the painting inside.

---

## Template Resolution

**Order:**

1. Term's `template` field (if explicitly set)
2. Taxonomy's default `template` setting
3. Fallback: `{taxonomy}/show.antlers.html` or `default.antlers.html`

**Reference:** https://statamic.dev/views#templates

**Mental model:** Term overrides taxonomy default.

---

## Layout

**Location:** `resources/views/layout.antlers.html` (global) or `resources/views/{taxonomy}/layout.antlers.html` (taxonomy-specific)

**Purpose:** The overall HTML wrapper that serves every taxonomy page.

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

**Key Principle:** The layout should **not** contain term-specific logic. It only knows about globals and provides hooks for customization.

**Reference:** https://statamic.dev/views#layouts

**Mental model:** Layout = reusable HTML frame that stays consistent across the site.

---

## Term Template

**Location:** `resources/views/{taxonomy}/show.antlers.html`

**Purpose:** Content-specific view that displays a single term page (listing entries with that term).

**Contains:**

- Term-specific markup using field variables from the taxonomy blueprint
- `{{ section:* }}` tags to inject into layout hooks
- Entry listing loop via `{{ taxonomy:entries }}`

**Accesses:**
- Term fields (`{{ title }}`, `{{ slug }}`, `{{ content }}`, custom fields)
- All entries with this term via `{{ taxonomy:entries }}`
- Globals (`{{ site:name }}`, `{{ globals:handle }}`)

**Convention:**

- Lives within the taxonomy's domain folder
- Typically shows term info + paginated entry listing

**Common Pattern:**

```
<h1>{{ title }}</h1>
{{ content }}

<h2>Posts in {{ title }}</h2>
{{ taxonomy:entries }}
  {{ partial:posts/card }}
{{ /taxonomy:entries }}
```

**Reference:** https://statamic.dev/views#templates

**Mental model:** Term template shows the term and its associated entries.

---

## Taxonomy Index Template

**Location:** `resources/views/{taxonomy}/index.antlers.html`

**Purpose:** Defines how the taxonomy listing page looks (all terms).

**Convention:**

- Shows all terms in the taxonomy
- Optional — only needed if you want a `/categories` index page

**Common Pattern:**

```
<h1>{{ title }}</h1>

{{ taxonomy:terms }}
  <a href="{{ url }}">{{ title }} ({{ entries_count }})</a>
{{ /taxonomy:terms }}
```

**Reference:** https://statamic.dev/tags/taxonomy

**Mental model:** Index template lists all terms in the taxonomy.

---

## Partials

**Location:** `resources/views/partials/_name.antlers.html` (global) or `resources/views/{taxonomy}/partials/_name.antlers.html` (taxonomy-specific)

**Purpose:** Reusable template fragments.

**Naming convention:**

- Prefix filename with `_`
- Reference WITHOUT the underscore: `{{ partial:name }}` or `{{ partial:{taxonomy}/partials/name }}`

**Common Partials:**

- `_term-card.antlers.html` — Term card for listings
- `_term-badge.antlers.html` — Small term badge/tag
- `_term-list.antlers.html` — List of terms

**Reference:** https://statamic.dev/tags/partial

**Mental model:** Partial = reusable component for taxonomy display.

---

## Using Taxonomies in Templates

### Display Terms on Entry

**Location:** Entry template (e.g., `resources/views/posts/show.antlers.html`)

**Pattern:**

```
{{# Loop through terms #}}
{{ categories }}
  <a href="{{ url }}">{{ title }}</a>
{{ /categories }}

{{# Or use taxonomy tag #}}
{{ taxonomy:categories }}
  <a href="{{ url }}">{{ title }}</a>
{{ /taxonomy:categories }}

```

### List All Terms

**Pattern:**

```
{{ taxonomy:terms taxonomy="categories" }}
  <a href="{{ url }}">{{ title }} ({{ entries_count }})</a>
{{ /taxonomy:terms }}

```

### Filter Entries by Term

**Pattern:**

```
{{ collection:posts taxonomy:categories="news" }}
  {{ title }}
{{ /collection:posts }}

```

**Reference:** https://statamic.dev/tags/taxonomy

---

## Multisite

**Content folders (per site handle):**

- Terms: `content/taxonomies/{taxonomy}/{site}/`

**Shared across sites:**

- Taxonomy config: `content/taxonomies/{taxonomy}.yaml`
- Blueprints: `resources/blueprints/taxonomies/{taxonomy}/`
- Templates: `resources/views/{taxonomy}/`

**Configuration:**

- Add `sites` array to taxonomy config
- Mark fields as `localizable: true` in blueprint for per-site content

**Reference:** https://statamic.dev/multi-site

**Mental model:** Each site has its own terms, sharing blueprints and views.

---

## Common Taxonomy Patterns

### Categories (Hierarchical)

**Use case:** Structured content classification

**Characteristics:**

- Usually single-select or limited multi-select
- Often has description and image
- May have parent-child relationships

**Blueprint fields:**

- `content` — Category description
- `image` — Category image
- `icon` — Category icon

### Tags (Flat)

**Use case:** Flexible content labeling

**Characteristics:**

- Multi-select, user can add new tags
- Typically just title, no extra fields
- Flat structure (no hierarchy)

**Blueprint fields:**

- Minimal — often just title/slug

### Authors (Specialized)

**Use case:** Content attribution

**Characteristics:**

- Links content to people
- Rich profile information

**Blueprint fields:**

- `bio` — Author biography
- `avatar` — Profile image
- `social_links` — Social media URLs
- `website` — Personal website

---

## Recommended View Structure

```
resources/views/
├── layout.antlers.html          # Global HTML wrapper (shared across site)
├── partials/                    # Shared components
│   ├── _header.antlers.html
│   ├── _footer.antlers.html
│   └── _navigation.antlers.html
├── categories/
│   ├── index.antlers.html      # All categories listing
│   ├── show.antlers.html       # Single category (entries list)
│   └── partials/
│       ├── _card.antlers.html
│       └── _badge.antlers.html
└── tags/
    ├── show.antlers.html       # Single tag (entries list)
    └── partials/
        └── _badge.antlers.html
```

**Key Points:**

- `layout.antlers.html` in root `views/` = shared by all taxonomies
- `partials/` in root `views/` = reusable components (header, footer, nav)
- Taxonomy-specific templates in `views/{taxonomy}/` = term page markup

---

## Quick Reference

| Concept | Location | Defines | Docs |
| --- | --- | --- | --- |
| Taxonomy | `content/taxonomies/{handle}.yaml` | How terms work | [Link](https://statamic.dev/taxonomies) |
| Blueprint | `resources/blueprints/taxonomies/{taxonomy}/{handle}.yaml` | What fields exist | [Link](https://statamic.dev/blueprints) |
| Term | `content/taxonomies/{taxonomy}/{slug}.yaml` | Term content | [Link](https://statamic.dev/taxonomies#terms) |
| **Layout** | `resources/views/layout.antlers.html` | HTML frame (head, styles, scripts, header, footer) | [Link](https://statamic.dev/views#layouts) |
| Term Template | `resources/views/{taxonomy}/show.antlers.html` | Single term page with entries | [Link](https://statamic.dev/views#templates) |
| Index Template | `resources/views/{taxonomy}/index.antlers.html` | All terms listing | [Link](https://statamic.dev/views#templates) |
| Partial | `resources/views/partials/_*.antlers.html` | Reusable components | [Link](https://statamic.dev/tags/partial) |
| Multisite Terms | `content/taxonomies/{taxonomy}/{site}/` | Per-site terms | [Link](https://statamic.dev/multi-site) |