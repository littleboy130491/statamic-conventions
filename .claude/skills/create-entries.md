# Create Entries

Create Statamic entry files with dummy content for collections and pages.

## Quick Start

1. **Detect multisite first** — Check `resources/sites.yaml`
2. Check blueprint for available fields
3. Create `.md` file with YAML frontmatter + optional markdown content
4. Generate UUID for `id` field

## Entry Paths

**Single site:**
- Standard: `content/collections/{collection}/{slug}.md`
- Dated: `content/collections/{collection}/{date}.{slug}.md`

**Multisite:**
- Standard: `content/collections/{collection}/{site}/{slug}.md`
- Dated: `content/collections/{collection}/{site}/{date}.{slug}.md`

## Entry Structure

```yaml
---
id: 550e8400-e29b-41d4-a716-446655440000
title: My Page Title
blueprint: page
published: true
slug: my-page
template: pages/default
author: user-uuid
custom_field: value
---
Optional markdown content goes here.

This becomes the `content` field.
```

## Required Fields

- `id` — Unique UUID (auto-generate)
- `title` — Entry title

## Common Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | UUID | Unique identifier (required) |
| `title` | string | Entry title (required) |
| `blueprint` | string | Blueprint handle (if not default) |
| `published` | boolean | Publication status |
| `slug` | string | URL slug |
| `date` | date | Publication date (dated collections) |
| `template` | string | Override template |
| `layout` | string | Override layout |
| `author` | UUID/array | User reference |

## Field Value Formats

### Text
```yaml
title: My Title
tagline: 'Value with "quotes"'
```

### Multiline
```yaml
description: |
  First paragraph.

  Second paragraph.
```

### Single Asset
```yaml
hero_image: images/hero.jpg
```

### Multiple Assets
```yaml
gallery:
  - images/photo1.jpg
  - images/photo2.jpg
```

### Replicator
```yaml
sections:
  -
    type: hero
    heading: Welcome
    image: images/hero.jpg
  -
    type: text_block
    content: 'Some content here...'
  -
    type: features
    items:
      -
        title: Feature 1
        description: Description 1
```

### Grid
```yaml
team_members:
  -
    name: John Doe
    role: CEO
    photo: team/john.jpg
  -
    name: Jane Smith
    role: CTO
```

### Bard (Rich Text)
```yaml
content:
  -
    type: paragraph
    content:
      -
        type: text
        text: 'This is a paragraph.'
  -
    type: heading
    attrs:
      level: 2
    content:
      -
        type: text
        text: 'Subheading'
```

### Entries Relationship
```yaml
related_posts:
  - post-uuid-1
  - post-uuid-2
```

### Terms
```yaml
categories:
  - news
  - technology
tags:
  - featured
```

### Link
```yaml
cta_link: /contact
# or entry reference
cta_link: 'entry::page-uuid'
```

## Generating UUIDs

Use standard UUID v4 format:
```
550e8400-e29b-41d4-a716-446655440000
```

Each entry MUST have a unique ID.

## Dated Entries

**Filename format:** `{YYYY-MM-DD}.{slug}.md`

Example: `2024-01-15.my-first-post.md`

```yaml
---
id: unique-uuid
title: My First Post
date: '2024-01-15'
---
```

## Structured Collections (Pages)

For structured collections, entries are also referenced in tree file:

**Tree:** `content/trees/collections/{collection}.yaml`
```yaml
tree:
  -
    entry: home-uuid
  -
    entry: about-uuid
    children:
      -
        entry: team-uuid
```

## Dummy Content Examples

### Blog Post
```yaml
---
id: 550e8400-e29b-41d4-a716-446655440001
title: Getting Started with Statamic
blueprint: post
published: true
date: '2024-01-15'
author: admin-user-uuid
featured_image: blog/getting-started.jpg
excerpt: Learn how to get started with Statamic CMS.
categories:
  - tutorials
tags:
  - beginner
  - cms
---
Welcome to Statamic! This guide will help you get started...
```

### Page
```yaml
---
id: 550e8400-e29b-41d4-a716-446655440002
title: About Us
blueprint: about
hero_image: pages/about-hero.jpg
hero_lead: Building trust since 1999
story_heading: Our Story
story_content:
  -
    type: paragraph
    content:
      -
        type: text
        text: 'We started in a small garage in 1999...'
team_members:
  -
    name: John Founder
    role: CEO
    photo: team/john.jpg
---
```

### Product
```yaml
---
id: 550e8400-e29b-41d4-a716-446655440003
title: Premium Widget
blueprint: product
price: 99.99
sku: WIDGET-001
images:
  - products/widget-1.jpg
  - products/widget-2.jpg
features:
  -
    title: Durable
    description: Built to last
  -
    title: Lightweight
    description: Easy to carry
published: true
---
```

## Multisite Entries

Each site has its own entry files. Localizable fields can differ per site.

**English:** `content/collections/pages/english/about.md`
**Indonesian:** `content/collections/pages/indonesian/about.md`

## Boundaries

- Do NOT create blueprints here — Use `create-blueprints`
- Do NOT create collection config here — Use `create-collections`
- Filename is slug-based, NOT UUID-based

## Accuracy Checks

- Entry file is `.md` with YAML frontmatter
- UUID in `id` field, slug in filename
- Dated entries: date in both filename and frontmatter
- Replicator items need `type` key matching set name
- Asset paths relative to container root

## Quick Reference

| Collection Type | Filename Pattern |
|----------------|------------------|
| Standard | `{slug}.md` |
| Dated | `{YYYY-MM-DD}.{slug}.md` |
| Multisite | `{site}/{slug}.md` |
