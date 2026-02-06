# Taxonomies (condensed)

## Config
- Path: `content/taxonomies/{handle}.yaml`
- Required: `title`
- Common keys: `route`, `template`, `layout`, `sites`, `revisions`, `preview_targets`

## Terms
- Single site: `content/taxonomies/{taxonomy}/{slug}.yaml`
- Multisite: `content/taxonomies/{taxonomy}/{site}/{slug}.yaml`
- Required: `title`

## Attach to collections
- In `content/collections/{collection}.yaml`:
  - `taxonomies: [categories, tags]`
