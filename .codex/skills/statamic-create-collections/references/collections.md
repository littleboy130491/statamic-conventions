# Collections (condensed)

## Config
- Path: `content/collections/{handle}.yaml`
- Required: `title`
- Common keys: `route`, `template`, `layout`, `blueprints`, `sites`, `taxonomies`, `date`, `date_behavior`, `orderable`, `sort_by`, `sort_dir`, `structure`, `mount`, `revisions`, `inject`

## Structure (tree)
- Single site: `content/trees/collections/{collection}.yaml`
- Multisite: `content/trees/collections/{site}/{collection}.yaml`
- Shape: `tree:` list of nodes with `entry` and optional `children`

## Entry filenames
- Non-dated: `{slug}.md`
- Dated: `{date}.{slug}.md`
- UUID lives in frontmatter (`id`), not in filename

## Templates
- Collection `template` is the default for entries unless entry overrides it.
