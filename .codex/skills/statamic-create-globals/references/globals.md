# Globals (condensed)

## Single site
- `content/globals/{handle}.yaml`
- Structure:
  - `title: Site Settings`
  - `data:` with field values

## Multisite
- Config: `content/globals/{handle}.yaml` with `title` and `sites`
- Data: `content/globals/{site}/{handle}.yaml` with values (no `data` wrapper)

## Blueprints
- `resources/blueprints/globals/{handle}.yaml` when using non-text fields or organized UI
