# Create Navigations

Create or update Statamic navigation configs, trees, and nav item blueprints.

## Quick Start

1. **Detect multisite first** — Check `resources/sites.yaml`
2. Create nav config: `content/navigation/{handle}.yaml`
3. Create nav tree: `content/trees/navigation/{handle}.yaml`
4. Create blueprint (optional): `resources/blueprints/navigation/{handle}.yaml`

## Navigation Config

**Path:** `content/navigation/{handle}.yaml`

```yaml
title: Header Navigation
max_depth: 2
collections:
  - pages
  - posts
sites:
  - english
  - indonesian
```

### Config Fields

| Field | Description |
|-------|-------------|
| `title` | Display name in CP (required) |
| `max_depth` | Maximum nesting level |
| `collections` | Collections to select entries from |
| `sites` | Site handles (multisite) |

## Navigation Tree

**Path:**
- Single site: `content/trees/navigation/{handle}.yaml`
- Multisite: `content/trees/navigation/{site}/{handle}.yaml`

```yaml
tree:
  -
    id: nav-item-1
    entry: home-entry-uuid
  -
    id: nav-item-2
    entry: about-entry-uuid
    children:
      -
        id: nav-item-3
        entry: team-entry-uuid
      -
        id: nav-item-4
        entry: history-entry-uuid
  -
    id: nav-item-5
    title: External Link
    url: https://example.com
  -
    id: nav-item-6
    title: Section Header
    children:
      -
        id: nav-item-7
        entry: services-entry-uuid
```

### Node Types

**Entry Reference:**
```yaml
-
  id: unique-nav-id
  entry: entry-uuid
```

**Custom Link:**
```yaml
-
  id: unique-nav-id
  title: External Link
  url: https://example.com
```

**Text (Non-link):**
```yaml
-
  id: unique-nav-id
  title: Section Header
  children:
    -
      id: child-nav-id
      entry: entry-uuid
```

## Navigation Blueprint

**Path:** `resources/blueprints/navigation/{handle}.yaml`

Add custom fields to nav items:

```yaml
title: Header Nav
tabs:
  main:
    display: Main
    sections:
      -
        fields:
          -
            handle: icon
            field:
              type: text
              display: Icon
              instructions: Icon class name (e.g., "fa-home")
          -
            handle: open_in_new_tab
            field:
              type: toggle
              display: Open in New Tab
              default: false
          -
            handle: css_class
            field:
              type: text
              display: CSS Class
          -
            handle: highlight
            field:
              type: toggle
              display: Highlight
              instructions: Show as highlighted/featured item
```

## Using Navigation in Templates

### Basic Navigation
```antlers
<nav>
  <ul>
    {{ nav:header }}
      <li>
        <a href="{{ url }}">{{ title }}</a>
      </li>
    {{ /nav:header }}
  </ul>
</nav>
```

### With Active State
```antlers
{{ nav:header }}
  <a href="{{ url }}"
     class="{{ if is_current || is_parent }}active{{ /if }}">
    {{ title }}
  </a>
{{ /nav:header }}
```

**State Variables:**
- `is_current` — Exact URL match
- `is_parent` — Current page is child of item
- `is_entry` — Item is entry reference
- `is_link` — Item is custom link
- `is_text` — Item is text only (no link)

### With Dropdowns
```antlers
<nav>
  <ul>
    {{ nav:header }}
      <li class="{{ if children }}has-dropdown{{ /if }}">
        {{ if url }}
          <a href="{{ url }}">{{ title }}</a>
        {{ else }}
          <span>{{ title }}</span>
        {{ /if }}

        {{ if children }}
          <ul class="dropdown">
            {{ children }}
              <li>
                <a href="{{ url }}">{{ title }}</a>
              </li>
            {{ /children }}
          </ul>
        {{ /if }}
      </li>
    {{ /nav:header }}
  </ul>
</nav>
```

### With Custom Fields
```antlers
{{ nav:header }}
  <a href="{{ url }}"
     class="{{ css_class }}"
     {{ if open_in_new_tab }}target="_blank" rel="noopener"{{ /if }}>
    {{ if icon }}<i class="{{ icon }}"></i>{{ /if }}
    {{ title }}
  </a>
{{ /nav:header }}
```

### Limit Depth
```antlers
{{ nav:header depth="1" }}
  {{# Only top-level items #}}
{{ /nav:header }}
```

### Recursive Navigation
```antlers
{{# Template #}}
{{ partial:partials/nav-items :items="nav:header" }}

{{# Partial: resources/views/partials/_nav-items.antlers.html #}}
<ul>
  {{ items }}
    <li>
      {{ if url }}
        <a href="{{ url }}">{{ title }}</a>
      {{ else }}
        <span>{{ title }}</span>
      {{ /if }}

      {{ if children }}
        {{ partial:partials/nav-items :items="children" }}
      {{ /if }}
    </li>
  {{ /items }}
</ul>
```

## Breadcrumbs

```antlers
<nav aria-label="Breadcrumb">
  <ol>
    {{ nav:breadcrumbs include_home="true" }}
      <li>
        {{ if is_current }}
          <span aria-current="page">{{ title }}</span>
        {{ else }}
          <a href="{{ url }}">{{ title }}</a>
        {{ /if }}
      </li>
    {{ /nav:breadcrumbs }}
  </ol>
</nav>
```

## Collection Structure as Navigation

Use structured collection's tree as navigation:

```antlers
{{ nav:pages }}
  <a href="{{ url }}" class="{{ if is_current }}active{{ /if }}">
    {{ title }}
  </a>
  {{ if children }}
    <ul>
      {{ children }}
        <li><a href="{{ url }}">{{ title }}</a></li>
      {{ /children }}
    </ul>
  {{ /if }}
{{ /nav:pages }}
```

**When to use:**
- Simple sites where page hierarchy = navigation
- Nav should match page structure exactly

**When to use dedicated Navigation:**
- Header/footer navs different from pages
- Need external links
- Need section headers (non-link items)
- Multiple different menus

## Common Navigation Patterns

### Header Nav
```yaml
# content/navigation/header.yaml
title: Header Navigation
max_depth: 2
collections:
  - pages
```

### Footer Nav
```yaml
# content/navigation/footer.yaml
title: Footer Navigation
max_depth: 1
collections:
  - pages
```

### Sidebar Nav
```yaml
# content/navigation/sidebar.yaml
title: Sidebar Navigation
max_depth: 3
collections:
  - docs
```

## Multisite

**Shared:** Navigation config, blueprint
**Per-site:** Trees

```yaml
sites:
  - english
  - indonesian
```

**Paths:**
- Trees: `content/trees/navigation/{site}/{handle}.yaml`

## File Structure

```
content/
├── navigation/
│   ├── header.yaml
│   └── footer.yaml
└── trees/navigation/
    ├── header.yaml       # Single site
    └── footer.yaml
    # OR
    ├── english/          # Multisite
    │   ├── header.yaml
    │   └── footer.yaml
    └── indonesian/
        ├── header.yaml
        └── footer.yaml

resources/blueprints/navigation/
├── header.yaml           # Optional
└── footer.yaml
```

## Boundaries

- Nav items reference entries by UUID
- Each nav item needs unique `id`
- Blueprint handle must match navigation handle

## Accuracy Checks

- Tree items use `id` and `entry` (not `slug`)
- Custom links need both `title` and `url`
- Text-only items have `title` but no `url` or `entry`

## Template Quick Reference

| Task | Syntax |
|------|--------|
| Basic nav loop | `{{ nav:header }}...{{ /nav:header }}` |
| Collection nav | `{{ nav:pages }}...{{ /nav:pages }}` |
| Breadcrumbs | `{{ nav:breadcrumbs }}...{{ /nav:breadcrumbs }}` |
| Limit depth | `{{ nav:header depth="2" }}` |
| Check active | `{{ if is_current \|\| is_parent }}` |
| Check children | `{{ if children }}` |
| Loop children | `{{ children }}...{{ /children }}` |
| Check type | `{{ if is_entry }}`, `{{ if is_link }}` |
