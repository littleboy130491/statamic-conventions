# Navigations (condensed)

## Config
- Path: `content/navigation/{handle}.yaml`
- Required: `title`
- Common keys: `collections`, `max_depth`, `sites`

## Trees
- Single site: `content/trees/navigation/{handle}.yaml`
- Multisite: `content/trees/navigation/{site}/{handle}.yaml`
- Node fields: `id`, `entry` or `title`+`url`, optional `children`

## Blueprint
- Path: `resources/blueprints/navigation/{handle}.yaml`

## Template usage
- `{{ nav:header }}...{{ /nav:header }}`
- Check states: `is_current`, `is_parent`, `children`
