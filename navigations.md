# Statamic Conventions: Navigations

## Navigation (Configuration)

**Location:** `content/navigation/{handle}.yaml`

**Purpose:** Defines a navigation menu structure separate from collection hierarchies.

**Controls:**

- Menu configuration
- Which collections can be referenced
- Maximum nesting depth
- Localization (sites)

**Required Fields:**

- `title` — Display name in Control Panel (e.g., "Header Nav", "Footer Nav")

**Common Fields:**

- `max_depth` — Maximum nesting level (default: unlimited)
- `collections` — Array of collection handles to select entries from
- `sites` — Array of site handles (multisite)

**Reference:** https://statamic.dev/navigation

**Mental model:** Navigation config defines the menu structure and available content sources.

---

## Navigation Tree

**Location:**

- Single site: `content/trees/navigation/{handle}.yaml`
- Multisite: `content/trees/navigation/{site}/{handle}.yaml`

**Purpose:** Stores the actual menu items and their hierarchy.

**Structure:**

```yaml
tree:
  -
    id: unique-id-1
    entry: entry-uuid          # Entry reference
  -
    id: unique-id-2
    title: External Link       # Custom link
    url: https://example.com
  -
    id: unique-id-3
    title: Section Header      # Text only (no link)
    children:
      -
        id: unique-id-4
        entry: another-entry-uuid

```

**Node Types:**

**Entry Reference:**

- `id` — Unique identifier for the nav item
- `entry` — Entry ID to link to
- Uses entry's title and URL automatically
- `children` — Optional nested items

**Custom Link:**

- `id` — Unique identifier
- `title` — Link text
- `url` — URL (internal path or external URL)
- `children` — Optional nested items

**Text (Non-link):**

- `id` — Unique identifier
- `title` — Display text
- No `url` or `entry`
- Useful for section headers in dropdowns

**Key rules:**

- Each node must have unique `id`
- Entry references inherit title/URL from entry
- Custom data can be added to any node
- Tree is separate from navigation config

**Reference:** https://statamic.dev/navigation

**Mental model:** Tree holds the actual menu items and structure.

---

## Navigation Blueprint

**Location:** `resources/blueprints/navigation/{handle}.yaml`

**Purpose:** Add custom fields to navigation items.

**Use cases:**

- Icon field for menu items
- Open in new tab toggle
- CSS classes for styling
- Custom attributes

**Structure:**

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

```

**Accessing in templates:**

```
{{ nav:header }}
  <a href="{{ url }}"
     class="{{ css_class }}"
     {{ if open_in_new_tab }}target="_blank" rel="noopener"{{ /if }}>
    {{ if icon }}<i class="{{ icon }}"></i>{{ /if }}
    {{ title }}
  </a>
{{ /nav:header }}

```

**Key rules:**

- Blueprint handle must match navigation handle
- Fields are available on each nav item
- Entry reference items can override entry fields

**Reference:** https://statamic.dev/navigation#blueprints

**Mental model:** Blueprint adds custom data to nav items.

---

## Using Navigation in Templates

### Basic Navigation Loop

**Pattern:**

```
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

**Pattern:**

```
{{ nav:header }}
  <a href="{{ url }}" class="{{ if is_current || is_parent }}active{{ /if }}">
    {{ title }}
  </a>
{{ /nav:header }}

```

**Available State Variables:**

- `is_current` — Exact URL match
- `is_parent` — Current page is child of this item
- `is_entry` — Item is an entry reference
- `is_link` — Item is a custom link
- `is_text` — Item is text only (no link)

### With Nested Children (Dropdown)

**Pattern:**

```
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

### Recursive Navigation (Unlimited Depth)

**Pattern:**

```
{{# In template #}}
{{ partial:partials/nav-items :items="nav:header" }}

{{# resources/views/partials/_nav-items.antlers.html #}}
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

### Filtering by Depth

**Pattern:**

```
{{# Only top level items #}}
{{ nav:header depth="1" }}
  <a href="{{ url }}">{{ title }}</a>
{{ /nav:header }}

{{# Top level and one child level #}}
{{ nav:header depth="2" }}
  ...
{{ /nav:header }}

```

**Reference:** https://statamic.dev/tags/nav

---

## Using Collection Structure as Navigation

**Purpose:** Use a structured collection's tree as navigation instead of a separate nav.

**Pattern:**

```
{{# Using pages collection structure #}}
{{ nav:pages }}
  <a href="{{ url }}" class="{{ if is_current }}active{{ /if }}">
    {{ title }}
  </a>
{{ /nav:pages }}

```

**With Children:**

```
{{ nav:pages }}
  <li>
    <a href="{{ url }}">{{ title }}</a>
    {{ if children }}
      <ul>
        {{ children }}
          <li><a href="{{ url }}">{{ title }}</a></li>
        {{ /children }}
      </ul>
    {{ /if }}
  </li>
{{ /nav:pages }}

```

**Key differences from Navigation:**

- Uses collection's tree structure
- No custom links or text-only items
- Items are always entries
- URL structure determined by collection route

**When to use:**

- Simple sites where page hierarchy = navigation
- When you want nav to match page structure exactly

**When to use dedicated Navigation:**

- Header/footer navs different from page structure
- Need external links
- Need section headers (non-link items)
- Multiple different menus

**Reference:** https://statamic.dev/tags/nav

---

## Breadcrumbs

**Purpose:** Show current page location in site hierarchy.

**Pattern:**

```
<nav aria-label="Breadcrumb">
  <ol>
    {{ nav:breadcrumbs }}
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

**Including Home:**

```
{{ nav:breadcrumbs include_home="true" }}
  ...
{{ /nav:breadcrumbs }}

```

**Available Variables:**

- `title` — Page/entry title
- `url` — Page URL
- `is_current` — Is the current page

**Reference:** https://statamic.dev/tags/nav-breadcrumbs

---

## Common Navigation Patterns

### Header Navigation

**Purpose:** Main site navigation

**Configuration:**

```yaml
# content/navigation/header.yaml
title: Header Navigation
max_depth: 2
collections:
  - pages
  - posts

```

**Template:**

```
<header>
  <nav>
    {{ nav:header }}
      <a href="{{ url }}" class="{{ if is_current || is_parent }}active{{ /if }}">
        {{ title }}
      </a>
      {{ if children }}
        <div class="dropdown">
          {{ children }}
            <a href="{{ url }}">{{ title }}</a>
          {{ /children }}
        </div>
      {{ /if }}
    {{ /nav:header }}
  </nav>
</header>

```

### Footer Navigation

**Purpose:** Footer links (typically flat, no dropdowns)

**Configuration:**

```yaml
# content/navigation/footer.yaml
title: Footer Navigation
max_depth: 1
collections:
  - pages

```

**Template:**

```
<footer>
  <nav>
    {{ nav:footer }}
      <a href="{{ url }}">{{ title }}</a>
    {{ /nav:footer }}
  </nav>
</footer>

```

### Sidebar Navigation

**Purpose:** Section-specific navigation

**Configuration:**

```yaml
# content/navigation/sidebar.yaml
title: Sidebar Navigation
max_depth: 3
collections:
  - docs

```

**Template:**

```
<aside>
  <nav>
    {{ nav:sidebar }}
      {{ partial:partials/nav-item }}
    {{ /nav:sidebar }}
  </nav>
</aside>

```

### Mobile Navigation

**Purpose:** Simplified or different structure for mobile

**Options:**

1. Same nav with CSS/JS for mobile styling
2. Separate mobile-specific navigation
3. Use responsive partial with different markup

---

## Multisite Navigation

**Content folders (per site handle):**

- Trees: `content/trees/navigation/{site}/{handle}.yaml`

**Shared across sites:**

- Navigation config: `content/navigation/{handle}.yaml`
- Blueprint: `resources/blueprints/navigation/{handle}.yaml`

**Configuration:**

```yaml
# content/navigation/header.yaml
title: Header Navigation
sites:
  - english
  - indonesian
collections:
  - pages

```

**Key rules:**

- Each site has its own tree (menu items)
- Config and blueprint shared across sites
- Entry references link to entries in current site

**Reference:** https://statamic.dev/multi-site

**Mental model:** Each site has its own menu items, sharing navigation structure.

---

## Navigation Partials

**Location:** `resources/views/partials/_nav-{handle}.antlers.html`

**Purpose:** Reusable navigation components.

**Common Partials:**

- `_nav-header.antlers.html` — Header navigation
- `_nav-footer.antlers.html` — Footer navigation
- `_nav-mobile.antlers.html` — Mobile menu
- `_nav-breadcrumbs.antlers.html` — Breadcrumb trail
- `_nav-item.antlers.html` — Recursive nav item

**Example — Recursive Nav Item:**

```
{{# resources/views/partials/_nav-item.antlers.html #}}
<li class="{{ if children }}has-children{{ /if }} {{ if is_current || is_parent }}active{{ /if }}">
  {{ if url }}
    <a href="{{ url }}">{{ title }}</a>
  {{ else }}
    <span class="nav-header">{{ title }}</span>
  {{ /if }}

  {{ if children }}
    <ul class="nav-children">
      {{ children }}
        {{ partial:partials/nav-item }}
      {{ /children }}
    </ul>
  {{ /if }}
</li>

```

**Reference:** https://statamic.dev/tags/partial

---

## Recommended View Structure

```
resources/views/
├── partials/
│   ├── _nav-header.antlers.html
│   ├── _nav-footer.antlers.html
│   ├── _nav-mobile.antlers.html
│   ├── _nav-breadcrumbs.antlers.html
│   └── _nav-item.antlers.html
└── layouts/
    └── default.antlers.html      # Includes nav partials

```

---

## Recommended Navigation Structure

### Minimal Setup

```
content/navigation/
└── header.yaml

content/trees/navigation/
└── header.yaml

```

### Standard Setup

```
content/navigation/
├── header.yaml
└── footer.yaml

content/trees/navigation/
├── header.yaml
└── footer.yaml

```

### Full Setup

```
content/navigation/
├── header.yaml
├── footer.yaml
├── sidebar.yaml
└── legal.yaml           # Terms, Privacy, etc.

content/trees/navigation/
├── header.yaml
├── footer.yaml
├── sidebar.yaml
└── legal.yaml

resources/blueprints/navigation/
├── header.yaml          # With icon, new tab fields
└── footer.yaml

```

---

## Quick Reference

| Concept | Location | Defines | Docs |
| --- | --- | --- | --- |
| Navigation | `content/navigation/{handle}.yaml` | Menu configuration | [Link](https://statamic.dev/navigation) |
| Nav Tree (single) | `content/trees/navigation/{handle}.yaml` | Menu items | [Link](https://statamic.dev/navigation) |
| Nav Tree (multi) | `content/trees/navigation/{site}/{handle}.yaml` | Per-site menu items | [Link](https://statamic.dev/navigation) |
| Nav Blueprint | `resources/blueprints/navigation/{handle}.yaml` | Custom nav item fields | [Link](https://statamic.dev/navigation#blueprints) |
| Nav Partial | `resources/views/partials/_nav-*.antlers.html` | Nav templates | [Link](https://statamic.dev/tags/partial) |

---

## Template Quick Reference

| Task | Syntax |
| --- | --- |
| Basic nav loop | `{{ nav:header }}...{{ /nav:header }}` |
| Collection nav | `{{ nav:pages }}...{{ /nav:pages }}` |
| Breadcrumbs | `{{ nav:breadcrumbs }}...{{ /nav:breadcrumbs }}` |
| Limit depth | `{{ nav:header depth="2" }}` |
| Check active | `{{ if is_current || is_parent }}active{{ /if }}` |
| Check has children | `{{ if children }}...{{ /if }}` |
| Loop children | `{{ children }}...{{ /children }}` |
| Check item type | `{{ if is_entry }}`, `{{ if is_link }}`, `{{ if is_text }}` |
| Access custom field | `{{ icon }}`, `{{ css_class }}` |