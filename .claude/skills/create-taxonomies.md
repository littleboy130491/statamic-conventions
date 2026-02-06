# Create Taxonomies

Create or update Statamic taxonomy configuration, term storage, routing, and multisite settings.

## Quick Start

1. **Detect multisite first** — Check `resources/sites.yaml`
2. Create `content/taxonomies/{handle}.yaml` with taxonomy config
3. Create blueprint in `resources/blueprints/taxonomies/{taxonomy}/`
4. Attach taxonomy to collections via collection config

## Workflow

### Step 1: Detect Multisite

Check `resources/sites.yaml`:
- Single site: One site key
- Multisite: Multiple site keys

### Step 2: Create Taxonomy Config

**Path:** `content/taxonomies/{handle}.yaml`

**Required Fields:**
- `title` — Display name (e.g., "Categories", "Tags")

**Full Example:**
```yaml
title: Categories
route: '/categories/{slug}'
template: 'categories/show'
layout: layout
sites:
  - english
  - indonesian
revisions: true
preview_targets:
  -
    label: Entry
    url: '/categories/{slug}'
```

### Step 3: Create Taxonomy Blueprint

**Path:** `resources/blueprints/taxonomies/{taxonomy}/{handle}.yaml`

**Example:**
```yaml
title: Category
tabs:
  main:
    display: Main
    sections:
      -
        fields:
          -
            handle: title
            field:
              type: text
              required: true
          -
            handle: content
            field:
              type: bard
              display: Description
          -
            handle: image
            field:
              type: assets
              display: Category Image
              max_files: 1
```

### Step 4: Create Terms

**Path:**
- Single site: `content/taxonomies/{taxonomy}/{slug}.yaml`
- Multisite: `content/taxonomies/{taxonomy}/{site}/{slug}.yaml`

**Term Structure (YAML only, no markdown):**
```yaml
title: Technology
slug: technology
content: 'Description of this category...'
image: images/tech-category.jpg
```

### Step 5: Attach to Collections

In collection config (`content/collections/{collection}.yaml`):
```yaml
taxonomies:
  - categories
  - tags
```

Taxonomy fields appear automatically in entry sidebar.

## Term Templates

**Location:** `resources/views/{taxonomy}/show.antlers.html`

**Common Pattern:**
```antlers
<h1>{{ title }}</h1>
{{ if content }}{{ content }}{{ /if }}

<h2>Posts in {{ title }}</h2>
{{ taxonomy:entries }}
  {{ partial:posts/card }}
{{ /taxonomy:entries }}
```

**Index Template:** `resources/views/{taxonomy}/index.antlers.html`
```antlers
<h1>All Categories</h1>
{{ taxonomy:terms }}
  <a href="{{ url }}">{{ title }} ({{ entries_count }})</a>
{{ /taxonomy:terms }}
```

## Common Taxonomy Patterns

### Categories (Hierarchical)
- Single or limited multi-select
- Often has description and image
- Blueprint: content, image, icon

### Tags (Flat)
- Multi-select, user can add new
- Minimal fields (just title/slug)

### Authors
- Rich profile information
- Blueprint: bio, avatar, social_links

## Multisite

**Shared:** Taxonomy config, blueprints, templates
**Per-site:** Terms

**Paths:**
- Terms: `content/taxonomies/{taxonomy}/{site}/`

```yaml
sites:
  - english
  - indonesian
```

## Using in Templates

**Display terms on entry:**
```antlers
{{ categories }}
  <a href="{{ url }}">{{ title }}</a>
{{ /categories }}
```

**List all terms:**
```antlers
{{ taxonomy:terms taxonomy="categories" }}
  <a href="{{ url }}">{{ title }} ({{ entries_count }})</a>
{{ /taxonomy:terms }}
```

**Filter entries by term:**
```antlers
{{ collection:posts taxonomy:categories="news" }}
  {{ title }}
{{ /collection:posts }}
```

## Boundaries

- Do NOT create blueprints inline — Use `create-blueprints`
- Attach taxonomies in COLLECTION config, not taxonomy config

## Accuracy Checks

- Terms are `.yaml` files, NOT `.md`
- Terms are created automatically when assigned (if not existing)
- Taxonomy route goes in taxonomy config, not collection

## Quick Reference

| Concept | Location |
|---------|----------|
| Taxonomy config | `content/taxonomies/{handle}.yaml` |
| Blueprint | `resources/blueprints/taxonomies/{taxonomy}/{handle}.yaml` |
| Term | `content/taxonomies/{taxonomy}/{slug}.yaml` |
| Term Template | `resources/views/{taxonomy}/show.antlers.html` |
| Index Template | `resources/views/{taxonomy}/index.antlers.html` |
