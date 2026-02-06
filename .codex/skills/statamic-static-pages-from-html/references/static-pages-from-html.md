# Static Pages From HTML (condensed)

## Steps
1. Detect multisite in `resources/sites.yaml`.
2. Read the blueprint and list field handles.
3. Map HTML content to existing fields; flag missing fields.
4. Update entry frontmatter with extracted values.
5. Build Antlers template with safe conditionals.

## Template rule
- Always wrap variables in `{{ if }}` blocks unless required.

## Entry paths
- Single site: `content/collections/pages/{slug}.md`
- Multisite: `content/collections/pages/{site}/{slug}.md`
