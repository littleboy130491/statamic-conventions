# Create Collections

Create or update Statamic collection configuration, routing, ordering, structure, and mount settings.

## Quick Start

1. **Detect multisite first** — Check `resources/sites.yaml`
2. Create `content/collections/{handle}.yaml` with collection config
3. For structured collections, create tree file
4. Use separate skills for blueprints and entries

## Workflow

### Step 1: Detect Multisite

Check `resources/sites.yaml` (or `config/statamic/sites.php`):
- Single site: One site key defined
- Multisite: Multiple site keys (e.g., `english`, `indonesian`)

### Step 2: Create Collection Config

**Path:** `content/collections/{handle}.yaml`

**Required Fields:**
- `title` — Display name in Control Panel

**Full Example:**
```yaml
title: Blog Posts
route: '/blog/{slug}'
template: 'blog/show'
layout: layout
blueprints:
  - post
sites:
  - english
  - indonesian
taxonomies:
  - categories
  - tags
date: true
date_behavior:
  past: public
  future: private
sort_by: date
sort_dir: desc
structure:
  root: true
  max_depth: 3
mount: entry-uuid-here
orderable: true
revisions: true
inject:
  author: default-author-id
```

### Step 3: Choose Collection Type

| Type | Config | Use Case |
|------|--------|----------|
| **Standard** | No special settings | Simple content lists |
| **Dated** | `date: true` | Blog posts, news, events |
| **Orderable** | `orderable: true` | Manual drag-drop ordering |
| **Structured** | `structure: { root: true }` | Hierarchical pages |

### Step 4: Create Tree (Structured Collections Only)

**Path:**
- Single site: `content/trees/collections/{collection}.yaml`
- Multisite: `content/trees/collections/{site}/{collection}.yaml`

**Structure:**
```yaml
tree:
  -
    entry: home-entry-uuid
  -
    entry: about-entry-uuid
    children:
      -
        entry: team-entry-uuid
```

### Step 5: Route Patterns

| Pattern | Example |
|---------|---------|
| Standard | `/{slug}` |
| Blog | `/blog/{slug}` |
| Structured | `/{parent_uri}/{slug}` |
| Dated | `/blog/{year}/{month}/{slug}` |
| Mounted | `/{mount}/{slug}` |

### Step 6: Template Configuration

```yaml
template: 'blog/show'  # Points to resources/views/blog/show.antlers.html
```

## Multisite

**Shared:** Collection config, blueprints
**Per-site:** Entries, trees

```yaml
sites:
  - english
  - indonesian
```

**Paths:**
- Entries: `content/collections/{collection}/{site}/`
- Trees: `content/trees/collections/{site}/{collection}.yaml`

## Mounting Collections

```yaml
mount: entry-uuid-of-parent-page
```

## Attaching Taxonomies

```yaml
taxonomies:
  - categories
  - tags
```

## Boundaries

- Do NOT create blueprints here — Use `create-blueprints`
- Do NOT create entries here — Use `create-entries`

## Accuracy Checks

- Entry filenames use slug (or date.slug for dated), NOT UUID
- UUID lives in frontmatter `id` field
- Tree files reference entries by UUID
- Template path has no file extension

## Quick Reference

| Concept | Location |
|---------|----------|
| Collection config | `content/collections/{handle}.yaml` |
| Blueprint | `resources/blueprints/collections/{collection}/{handle}.yaml` |
| Tree | `content/trees/collections/{collection}.yaml` |
| Entry | `content/collections/{collection}/{slug}.md` |
| Template | `resources/views/{collection}/{template}.antlers.html` |
