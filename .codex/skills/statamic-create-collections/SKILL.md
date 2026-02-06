---
name: statamic-create-collections
description: Create or update Statamic collection configuration, routing, ordering, structure, and mount settings; use when defining collection behavior, enabling structured/dated/orderable collections, or setting collection templates/layouts.
---

# Statamic Create Collections

## Quick start
- Identify the collection handle and its type (standard, dated, orderable, structured).
- Update or create `content/collections/{handle}.yaml`.
- If structured, create or update the tree in `content/trees/collections/{collection}.yaml` (or per-site).
- Keep blueprints and entries separate; use the blueprint and entries skills.

## Workflow
1. Inspect existing config and decide collection type.
2. Create or update `content/collections/{handle}.yaml` with:
   - `title`, `route`, `template`, `layout`, `blueprints`
   - `structure` for hierarchical pages
   - `date` and `date_behavior` for dated collections
   - `orderable` for manual ordering
   - `taxonomies` when attaching terms
3. If structured, ensure a tree file exists and references entry IDs.
4. If using mounts, set `mount` to the entry ID you want to attach under.
5. Note multisite requirements and add `sites` when needed.

## Multisite
- Collection config is shared, entries and trees are per-site.
- Tree paths:
  - Single site: `content/trees/collections/{collection}.yaml`
  - Multisite: `content/trees/collections/{site}/{collection}.yaml`

## Boundaries
- Do not create blueprints here. Use `statamic-create-blueprints`.
- Do not create entries here. Use `statamic-create-entries`.

## Accuracy checks
- Entry filenames are slug-based (or date.slug for dated), not UUID-based.

## References
- See `references/collections.md` for condensed conventions and fields.

