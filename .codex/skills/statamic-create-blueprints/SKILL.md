---
name: statamic-create-blueprints
description: Create or update Statamic blueprints and field layouts for collections, taxonomies, globals, forms, navigation, assets, and users; use when defining field structure, validation, or Control Panel UI.
---

# Statamic Create Blueprints

## Quick start
- Identify the target type (collection, taxonomy, globals, forms, navigation, assets, users).
- Create or edit the blueprint at the correct path.
- Define `title`, `tabs`, `sections`, and `fields` (or `fields` only for forms).

## Workflow
1. Confirm target path:
   - Collections: `resources/blueprints/collections/{collection}/{handle}.yaml`
   - Taxonomies: `resources/blueprints/taxonomies/{taxonomy}/{handle}.yaml`
   - Globals: `resources/blueprints/globals/{handle}.yaml`
   - Forms: `resources/blueprints/forms/{handle}.yaml`
   - Navigation: `resources/blueprints/navigation/{handle}.yaml`
   - Assets: `resources/blueprints/assets/{handle}.yaml`
   - Users: `resources/blueprints/user.yaml`
2. Define `title` and field layout.
3. Use tabs/sections for CP organization where supported.
4. Apply validation rules and conditional visibility as needed.
5. Reuse fieldsets from `resources/fieldsets/` when appropriate.

## Form blueprint note
- Form blueprints commonly use a top-level `fields:` list (no tabs/sections).

## Quality checks
- Ensure handles are consistent with templates and entry frontmatter.
- Mark `localizable: true` for multisite fields that vary per site.

## References
- See `references/blueprints.md` for condensed field and layout rules.

