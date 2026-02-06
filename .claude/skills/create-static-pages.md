# Create Static Pages

Set up the Statamic pages collection, page tree/hierarchy, page entries, and template resolution.

## Quick Start

1. **Detect multisite first** — Check `resources/sites.yaml`
2. Create pages collection config: `content/collections/pages.yaml`
3. Create page blueprint(s) in `resources/blueprints/collections/pages/`
4. Create page entries as `.md` files
5. Create page tree for hierarchy
6. Create page template(s)

## Workflow

### Step 1: Detect Multisite

Check `resources/sites.yaml` (or `config/statamic/sites.php`):
- Single site: One site key defined
- Multisite: Multiple site keys (e.g., `english`, `indonesian`)

### Step 2: Create Pages Collection Config

**Path:** `content/collections/pages.yaml`

```yaml
title: Pages
route: '{parent_uri}/{slug}'
template: 'pages/default'
layout: layout
blueprints:
  - default
  - home
  - about
  - contact
sites:
  - english
  - indonesian
structure:
  root: true
  max_depth: 3
revisions: true
inject:
  author: default-author-id
```

**Key fields:**

| Field | Description |
|-------|-------------|
| `title` | Display name in CP (required) |
| `route` | URL pattern — use `{parent_uri}/{slug}` for hierarchical pages |
| `template` | Default template for pages |
| `layout` | Default layout |
| `blueprints` | Array of available blueprint handles |
| `structure` | Enable hierarchy with `root: true` |
| `sites` | Site handles (multisite only) |

### Step 3: Choose Blueprint Strategy

| Page Type | Approach |
|-----------|----------|
| Homepage | Hybrid (fixed hero + flexible sections) |
| About | Page-specific blueprint |
| Services | Flexible (replicator) |
| Contact | Page-specific blueprint |
| Landing Pages | Flexible (replicator) |
| Legal Pages | Simple (title + bard) |

**Decision tree:**
- Fixed, unique layout? → Page-specific blueprint
- Reusable sections that editors rearrange? → Flexible page builder (replicator)
- Both fixed and flexible parts? → Hybrid approach

**Blueprint location:** `resources/blueprints/collections/pages/`
```
resources/blueprints/collections/pages/
├── default.yaml        # Simple pages (title + content)
├── flexible.yaml       # Page builder with replicator
├── home.yaml           # Homepage specific
├── about.yaml          # About page specific
├── contact.yaml        # Contact page specific
└── services.yaml       # Services page specific
```

Use `create-blueprints` skill for detailed blueprint creation.

### Step 4: Create Page Entries

**Path:**
- Single site: `content/collections/pages/{slug}.md`
- Multisite: `content/collections/pages/{site}/{slug}.md`

**Homepage entry:**
```yaml
---
id: 550e8400-e29b-41d4-a716-446655440000
title: Home
blueprint: home
hero_heading: Welcome to Our Site
hero_lead: Building great websites since 1999
---
```

**Standard page entry:**
```yaml
---
id: 550e8400-e29b-41d4-a716-446655440001
title: About Us
blueprint: about
hero_image: images/about-hero.jpg
subtitle: Our story and values
---
Optional markdown content here.
```

Use `create-entries` skill for detailed entry creation.

### Step 5: Create Page Tree

The tree defines page hierarchy and ordering. The first entry becomes the homepage (URL: `/`).

**Path:**
- Single site: `content/trees/collections/pages.yaml`
- Multisite: `content/trees/collections/{site}/pages.yaml`

```yaml
tree:
  -
    entry: home-uuid
  -
    entry: about-uuid
    children:
      -
        entry: team-uuid
      -
        entry: history-uuid
  -
    entry: services-uuid
    children:
      -
        entry: web-design-uuid
      -
        entry: development-uuid
  -
    entry: contact-uuid
```

**Tree rules:**
- Entries referenced by UUID
- First entry with collection `root: true` becomes homepage
- `children` array for nested pages
- Tree controls order and hierarchy, NOT content

### Step 6: Template Resolution

Template resolution order:
1. Entry's `template` field (if explicitly set in frontmatter)
2. Collection's default `template` setting

**Blueprint-based mapping:**

Set `template` per blueprint convention in collection config:
```yaml
template: 'pages/default'
```

Or override per entry:
```yaml
---
template: pages/about
---
```

**Template path convention:**
```
resources/views/pages/{template}.antlers.html
```

Use `create-page-templates` skill for detailed template creation.

## Page Hierarchy & URLs

With `route: '{parent_uri}/{slug}'` and a structured collection:

| Page | Parent | URL |
|------|--------|-----|
| Home | (root) | `/` |
| About | (root) | `/about` |
| Team | About | `/about/team` |
| History | About | `/about/history` |
| Services | (root) | `/services` |
| Web Design | Services | `/services/web-design` |
| Contact | (root) | `/contact` |

## Recommended View Structure

```
resources/views/
├── layout.antlers.html              # Global HTML wrapper
├── partials/                        # Shared components
│   ├── _header.antlers.html
│   ├── _footer.antlers.html
│   └── _navigation.antlers.html
├── pages/                           # Pages collection templates
│   ├── default.antlers.html
│   ├── home.antlers.html
│   ├── about.antlers.html
│   └── contact.antlers.html
└── sets/                            # Replicator set partials
    ├── _hero.antlers.html
    ├── _text_block.antlers.html
    └── _cta.antlers.html
```

## Multisite

**Shared:** Collection config, blueprints, templates
**Per-site:** Entries, trees

```yaml
# In collection config
sites:
  - english
  - indonesian
```

**Paths:**
- Entries: `content/collections/pages/{site}/{slug}.md`
- Trees: `content/trees/collections/{site}/pages.yaml`

Localizable fields need `localizable: true` in blueprint.

## Common Page Patterns

### Homepage
```yaml
# content/collections/pages/home.md
---
id: unique-uuid
title: Home
blueprint: home
hero_heading: Welcome
hero_lead: We build great websites
hero_image: images/hero.jpg
hero_cta_text: Learn More
hero_cta_link: /about
---
```

### About Page
```yaml
# content/collections/pages/about.md
---
id: unique-uuid
title: About Us
blueprint: about
subtitle: Our story
hero_image: images/about-hero.jpg
story_heading: Our Story
story_content:
  -
    type: paragraph
    content:
      -
        type: text
        text: 'We started in 1999...'
values:
  -
    icon: icons/quality.svg
    title: Quality
    description: We never compromise
cta_heading: Ready to work with us?
cta_button_text: Get in Touch
cta_button_link: /contact
---
```

### Contact Page
```yaml
# content/collections/pages/contact.md
---
id: unique-uuid
title: Contact Us
blueprint: contact
subtitle: Get in touch
address: |
  123 Main Street
  City, State 12345
phone: '+1 234 567 890'
email: contact@example.com
map_embed: '<iframe src="..."></iframe>'
form_heading: Send us a message
---
```

### Legal Page (Simple)
```yaml
# content/collections/pages/privacy.md
---
id: unique-uuid
title: Privacy Policy
blueprint: default
---
Your privacy is important to us...

## Information We Collect
...
```

## Mounting Collections to Pages

Mount a collection (e.g., blog) under a page for URL hierarchy:

1. Create the parent page entry (e.g., `blog.md`)
2. In collection config, set `mount`:
```yaml
# content/collections/posts.yaml
mount: blog-page-entry-uuid
route: '{mount}/{slug}'
```

Result: Blog posts appear at `/blog/{slug}`.

## Boundaries

- Do NOT create blueprints here — Use `create-blueprints`
- Do NOT create entries here — Use `create-entries`
- Do NOT create templates here — Use `create-page-templates`
- Tree controls hierarchy only, not content
- Collection config is shared across sites

## Accuracy Checks

- Pages collection uses `structure: { root: true }` for hierarchy
- Route pattern `{parent_uri}/{slug}` for nested URLs
- Tree file references entries by UUID
- Entry filenames use slug, NOT UUID
- Template path has no file extension in config
- First tree entry becomes homepage

## Quick Reference

| Concept | Location |
|---------|----------|
| Collection config | `content/collections/pages.yaml` |
| Blueprint | `resources/blueprints/collections/pages/{handle}.yaml` |
| Entry (single) | `content/collections/pages/{slug}.md` |
| Entry (multi) | `content/collections/pages/{site}/{slug}.md` |
| Tree (single) | `content/trees/collections/pages.yaml` |
| Tree (multi) | `content/trees/collections/{site}/pages.yaml` |
| Template | `resources/views/pages/{template}.antlers.html` |
| Partial | `resources/views/partials/_name.antlers.html` |
| Set partial | `resources/views/sets/_name.antlers.html` |
