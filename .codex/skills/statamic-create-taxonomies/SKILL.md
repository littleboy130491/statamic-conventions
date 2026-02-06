---
name: statamic-create-taxonomies
description: Create or update Statamic taxonomy configuration, term storage, routing, and multisite settings; use when defining taxonomy behavior or adding terms.
---

# Statamic Create Taxonomies

## Quick start
- Choose taxonomy handle and title.
- Create `content/taxonomies/{handle}.yaml`.
- Create term files in `content/taxonomies/{taxonomy}/` as needed.

## Workflow
1. Create or update taxonomy config with `title`, `route`, `template`, `layout`, `sites`.
2. If terms are needed up front, create YAML term files by slug.
3. Attach taxonomies to collections in `content/collections/{collection}.yaml` using `taxonomies:`.
4. If custom term fields are required, use `statamic-create-blueprints`.

## Multisite
- Config is shared; term files are per-site:
  - `content/taxonomies/{taxonomy}/{site}/{slug}.yaml`

## Boundaries
- Blueprints live in `resources/blueprints/taxonomies/{taxonomy}/{handle}.yaml` and are handled by the blueprint skill.

## References
- See `references/taxonomies.md` for condensed conventions.

