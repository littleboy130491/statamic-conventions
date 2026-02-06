# Blueprints (condensed)

## Core structure
- Required: `title`
- Layout:
  - `tabs` -> `sections` -> `fields`
  - Each field: `handle` + `field` (with `type`, `display`, etc.)

## Common field options
- `display`, `instructions`, `placeholder`, `validate`, `required`, `default`
- `localizable`, `visibility`, `width`, `if` / `unless`, `listable`

## Fieldsets
- Path: `resources/fieldsets/{handle}.yaml`
- Import with:
  - `- import: seo`
  - Optional `prefix:` and overrides

## Form blueprints
- Typically:
  - `title: Contact Form`
  - `fields:` (list of fields only)
