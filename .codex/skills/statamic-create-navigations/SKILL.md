---
name: statamic-create-navigations
description: Create or update Statamic navigation configs, trees, and nav item blueprints; use when defining menus, links, and hierarchy for site navigation.
---

# Statamic Create Navigations

## Quick start
- Create navigation config in `content/navigation/{handle}.yaml`.
- Create tree file in `content/trees/navigation/`.
- Add nav item blueprints if custom fields are needed.

## Workflow
1. Config:
   - Required: `title`
   - Optional: `collections`, `max_depth`, `sites`
2. Tree:
   - Single site: `content/trees/navigation/{handle}.yaml`
   - Multisite: `content/trees/navigation/{site}/{handle}.yaml`
   - Node types: entry reference, custom link, or text-only
3. Blueprint (optional):
   - `resources/blueprints/navigation/{handle}.yaml`
   - Add fields like `icon`, `open_in_new_tab`, `css_class`

## References
- See `references/navigations.md` for condensed structure and template usage.

