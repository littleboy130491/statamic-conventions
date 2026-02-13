# Create Navigations

Create Statamic navigation configs, trees, and (optionally) nav item blueprints from navigation schema files.

## Input

This skill reads **navigation schema files** from `schemas/{handle}_nav.md`. These are created by the `create-schema-navigation` skill.

If no schema file exists for the requested navigation, ask the user to run `create-schema-navigation` first, or ask them to describe the navigation so you can proceed manually.

## Schema Format Reference

Each schema file has this structure:

```
schema_name: {handle}
schema_type: navigation
title: {Display Title}
max_depth: {number}
collections: - {collection1} - {collection2}
multisite: {true/false}
sites: {- site1 - site2, only if multisite: true}

tree:
- {Label} | {type} | {reference}
  - {Child Label} | {type} | {reference}
```

### Schema Item Types

| Type | Reference Format | What to Generate |
|------|-----------------|------------------|
| `entry` | `{collection}/{slug}` | Look up entry UUID from `content/collections/{collection}/{slug}.md` → use `entry: {uuid}` |
| `archive` | `{collection}` | Find the mount entry for that collection (the page whose slug matches the collection's `mount` value) → use `entry: {mount-entry-uuid}` |
| `term` | `{taxonomy}/{slug}` | Resolve URL from taxonomy route config → use `title: {Label}` and `url: {resolved-url}` (hardcoded link) |
| `link` | `{url}` | Use `title: {Label}` and `url: {url}` |
| `text` | _(none)_ | Use `title: {Label}` only (no `url`, no `entry`) |

**Note on `term` type:** Statamic's navigation does NOT natively support taxonomy terms as a node type — only entries, URLs, and text. The `term` type in the schema is resolved into a hardcoded URL link node. See Step 3 for how to resolve the URL.

### Tree Indentation in Schema

- Top-level: `- Item` → top-level tree node
- 2-space indent: `  - Item` → `children` of parent
- 4-space indent: `    - Item` → `children` of child (grandchild)

## Quick Start

1. **Read the schema** — `schemas/{handle}_nav.md`
2. **Detect multisite** — Check `resources/sites.yaml`
3. **Resolve entry UUIDs** — Scan entry files to map `collection/slug` → UUID
4. **Create nav config** — `content/navigation/{handle}.yaml`
5. **Create nav tree** — `content/trees/navigation/{handle}.yaml` (or per-site)

## Workflow

### Step 1: Read the Schema

Read `schemas/{handle}_nav.md` and parse:
- `schema_name` → navigation handle
- `title` → display title for CP
- `max_depth` → max nesting level
- `collections` → collections to attach
- `multisite` / `sites` → site configuration
- `tree` → the full menu structure with item types and references

### Step 2: Detect Multisite

Read `resources/sites.yaml` — cross-check against schema's `multisite` and `sites` values.

- Single site → one tree file at `content/trees/navigation/{handle}.yaml`
- Multisite → one tree file per site at `content/trees/navigation/{site}/{handle}.yaml`

### Step 3: Resolve Entry UUIDs

For every `entry` and `archive` item in the schema tree, you need the entry's UUID.

**For `entry` type** (`{collection}/{slug}`):
1. Read `content/collections/{collection}/{slug}.md` (or the site-specific variant for multisite)
2. Extract the `id:` value from the YAML front matter — that's the UUID

**For `archive` type** (`{collection}`):
1. Read the collection config `content/collections/{collection}.yaml` to find the `mount` value (a UUID)
2. Use that mount UUID directly as the `entry` value — this is the page entry that serves as the archive

**For `term` type** (`{taxonomy}/{slug}`):
Statamic navigation does NOT support taxonomy terms natively — only entries, hardcoded URLs, and text. So `term` items become hardcoded URL links.
1. Read the taxonomy config `content/taxonomies/{taxonomy}.yaml` to find the `route` (e.g., `/categories/{slug}`)
2. Replace `{slug}` in the route with the term's slug to get the final URL
3. Generate a `link` node with `title: {Label}` and `url: {resolved-url}`

Example: `term` reference `categories/news` + taxonomy route `/categories/{slug}` → URL `/categories/news`

**If an entry file doesn't exist yet** (collection/entries not created), you have two options:
- Ask the user to run `create-entries` or `create-static-pages` first
- Generate a UUID yourself using the standard format (e.g., `xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx`) and note that the entry must be created with this ID

### Step 4: Create Navigation Config

**Path:** `content/navigation/{handle}.yaml`

```yaml
title: {title from schema}
max_depth: {max_depth from schema}
collections:
  - {collection1}
  - {collection2}
```

For multisite, add `sites`:

```yaml
title: Header Navigation
max_depth: 2
collections:
  - pages
  - services
sites:
  - english
  - indonesian
```

### Step 5: Create Navigation Tree

**Path:**
- Single site: `content/trees/navigation/{handle}.yaml`
- Multisite: `content/trees/navigation/{site}/{handle}.yaml`

Convert the schema tree into YAML tree format. Each node needs a unique `id`.

**ID generation:** Use `{handle}-{sequential-number}` pattern (e.g., `header-1`, `header-2`, `header-3`).

#### Mapping Schema Items to YAML Nodes

**`entry` type** → entry reference node:
```
Schema:  - About | entry | pages/about
```
```yaml
-
  id: header-2
  entry: d6f1c8a2-3b4e-4f5a-8c7d-9e0f1a2b3c4d    # UUID from pages/about.md
```

**`archive` type** → entry reference node (using mount entry):
```
Schema:  - Blog | archive | blog_posts
```
```yaml
-
  id: header-4
  entry: a1b2c3d4-5e6f-7a8b-9c0d-e1f2a3b4c5d6    # UUID of the mount page
```

**`term` type** → hardcoded URL node (resolved from taxonomy route):
```
Schema:  - News | term | categories/news
```
```yaml
# Taxonomy route: /categories/{slug} → resolved URL: /categories/news
-
  id: header-5
  title: News
  url: /categories/news
```

**`link` type** → custom URL node:
```
Schema:  - GitHub | link | https://github.com/example
```
```yaml
-
  id: header-6
  title: GitHub
  url: https://github.com/example
```

**`text` type** → text-only node (section header):
```
Schema:  - Company | text
```
```yaml
-
  id: footer-1
  title: Company
```

#### Children / Nesting

Schema indentation maps to YAML `children`:

```
Schema:
- Services | archive | services
  - Web Dev | entry | services/web-development
  - Mobile | entry | services/mobile-apps
```

```yaml
-
  id: header-3
  entry: mount-uuid-of-services
  children:
    -
      id: header-4
      entry: uuid-of-web-development
    -
      id: header-5
      entry: uuid-of-mobile-apps
```

### Step 5b: Multisite Trees

For multisite, create one tree file per site. The tree structure is the same but entry UUIDs may differ between sites if entries are localized.

For each site:
1. Read entry files from `content/collections/{collection}/{site}/{slug}.md` (or the default site path)
2. Resolve UUIDs per site
3. Write to `content/trees/navigation/{site}/{handle}.yaml`

## Full Example

**Schema input** (`schemas/header_nav.md`):
```
schema_name: header
schema_type: navigation
title: Header Navigation
max_depth: 2
collections: - pages - services
multisite: false

tree:
- Home | entry | pages/home
- About | entry | pages/about
- Services | archive | services
  - Web Development | entry | services/web-development
  - Mobile Apps | entry | services/mobile-apps
- Blog | link | https://blog.example.com
- Contact | entry | pages/contact
```

**Generated config** (`content/navigation/header.yaml`):
```yaml
title: Header Navigation
max_depth: 2
collections:
  - pages
  - services
```

**Generated tree** (`content/trees/navigation/header.yaml`):
```yaml
tree:
  -
    id: header-1
    entry: 11111111-1111-1111-1111-111111111111
  -
    id: header-2
    entry: 22222222-2222-2222-2222-222222222222
  -
    id: header-3
    entry: 33333333-3333-3333-3333-333333333333
    children:
      -
        id: header-4
        entry: 44444444-4444-4444-4444-444444444444
      -
        id: header-5
        entry: 55555555-5555-5555-5555-555555555555
  -
    id: header-6
    title: Blog
    url: https://blog.example.com
  -
    id: header-7
    entry: 66666666-6666-6666-6666-666666666666
```

## Navigation Blueprint (Optional)

Only create a blueprint if the schema or user specifies custom fields for nav items.

**Path:** `resources/blueprints/navigation/{handle}.yaml`

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
          -
            handle: open_in_new_tab
            field:
              type: toggle
              display: Open in New Tab
              default: false
```

## File Structure

```
content/
├── navigation/
│   ├── header.yaml              # Nav config
│   └── footer.yaml
└── trees/navigation/
    ├── header.yaml              # Single site tree
    └── footer.yaml
    # OR for multisite:
    ├── english/
    │   ├── header.yaml
    │   └── footer.yaml
    └── indonesian/
        ├── header.yaml
        └── footer.yaml

resources/blueprints/navigation/
├── header.yaml                  # Optional custom fields
└── footer.yaml
```

## YAML Node Reference

### Entry Reference
```yaml
-
  id: unique-id
  entry: entry-uuid
```

### Custom Link
```yaml
-
  id: unique-id
  title: Link Text
  url: https://example.com
```

### Text Only (No Link)
```yaml
-
  id: unique-id
  title: Section Header
```

### With Children
```yaml
-
  id: unique-id
  entry: parent-uuid
  children:
    -
      id: child-id
      entry: child-uuid
```

## Antlers Template Reference

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

### Limit Depth
```antlers
{{ nav:header depth="1" }}
  {{# Only top-level items #}}
{{ /nav:header }}
```

### Breadcrumbs
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

## Accuracy Checks

Before finishing, verify:
- [ ] Nav config exists at `content/navigation/{handle}.yaml`
- [ ] Nav tree exists at correct path (single site vs multisite)
- [ ] Every `entry` schema item resolved to a valid UUID in the tree
- [ ] Every `archive` schema item resolved to the mount entry UUID
- [ ] Every `term` schema item resolved to a hardcoded URL node (`title` + `url`) using the taxonomy's route config
- [ ] Every `link` schema item has both `title` and `url` in the tree
- [ ] Every `text` schema item has `title` only (no `url`, no `entry`)
- [ ] All tree nodes have unique `id` values
- [ ] Tree nesting matches schema indentation
- [ ] Config `collections` matches schema `collections`
- [ ] Config `max_depth` matches schema `max_depth`
- [ ] Multisite: one tree file per site, config has `sites` list
- [ ] Blueprint handle matches navigation handle (if blueprint created)
