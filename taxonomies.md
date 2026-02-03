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

## Template Resolution

**Order:**

1. Term's `template` field (if explicitly set)
2. Taxonomy's default `template` setting
3. Fallback: `{taxonomy}/show.antlers.html` or `default.antlers.html`

**Reference:** https://statamic.dev/views#templates

**Mental model:** Term overrides taxonomy default.

---

## Term Template

**Location:** `resources/views/{taxonomy}/show.antlers.html`

**Purpose:** Defines how a single term page looks (listing entries with that term).

**Convention:**

- Lives within the taxonomy's domain folder
- Typically shows term info + paginated entry listing

**Available Variables:**

- `title` — Term title
- `slug` — Term slug
- `content` — Term description
- All custom fields from blueprint
- Access entries via `{{ taxonomy:entries }}`

**Common Pattern:**

```
<h1>{{ title }}</h1>
{{ content }}

<h2>Posts in {{ title }}</h2>
{{ taxonomy:entries }}
  {{ partial:posts/partials/card }}
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

## Taxonomy Layout

**Location:** `resources/views/{taxonomy}/layout.antlers.html`

**Purpose:** Defines the outer HTML frame for taxonomy pages.

**Key concept:**

- Layout wraps template
- Uses `{{ template_content }}` to inject template
- Can share layout with related collection or use taxonomy-specific

**Alternative:** Use shared site layout or collection layout

**Reference:** https://statamic.dev/views#layouts

**Mental model:** Layout defines the frame around taxonomy pages.

---

## Partials

**Location:** `resources/views/{taxonomy}/partials/_name.antlers.html`

**Naming convention:**

- Prefix filename with `_`
- Reference WITHOUT the underscore: `{{ partial:{taxonomy}/partials/name }}`

**Purpose:** Reusable template fragments within the taxonomy domain.

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

## Recommended View Structure (Domain-Driven)

```
resources/views/
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

---

## Quick Reference

| Concept | Location | Defines | Docs |
| --- | --- | --- | --- |
| Taxonomy | `content/taxonomies/{handle}.yaml` | How terms work | [Link](https://statamic.dev/taxonomies) |
| Blueprint | `resources/blueprints/taxonomies/{taxonomy}/{handle}.yaml` | What fields exist | [Link](https://statamic.dev/blueprints) |
| Term | `content/taxonomies/{taxonomy}/{slug}.yaml` | Term content | [Link](https://statamic.dev/taxonomies#terms) |
| Term Template | `resources/views/{taxonomy}/show.antlers.html` | Single term page | [Link](https://statamic.dev/views#templates) |
| Index Template | `resources/views/{taxonomy}/index.antlers.html` | All terms listing | [Link](https://statamic.dev/views#templates) |
| Layout | `resources/views/{taxonomy}/layout.antlers.html` | Page frame/shell | [Link](https://statamic.dev/views#layouts) |
| Partial | `resources/views/{taxonomy}/partials/_*.antlers.html` | Reusable fragments | [Link](https://statamic.dev/tags/partial) |
| Multisite Terms | `content/taxonomies/{taxonomy}/{site}/` | Per-site terms | [Link](https://statamic.dev/multi-site) |