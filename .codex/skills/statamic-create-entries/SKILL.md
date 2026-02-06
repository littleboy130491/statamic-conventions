---
name: statamic-create-entries
description: Create Statamic entry files and dummy content for collections and pages; use when adding entries, seeding dummy data, or generating frontmatter and field values.
---

# Statamic Create Entries

## Quick start
- Identify collection handle and whether it is dated or structured.
- Create a Markdown entry in the correct path with frontmatter.
- Use realistic dummy content that matches blueprint field types.

## Workflow
1. Read collection config to determine:
   - Dated vs non-dated filename format
   - Multisite paths
   - Blueprints and template conventions
2. Create entry file:
   - Non-dated: `content/collections/{collection}/{slug}.md`
   - Dated: `content/collections/{collection}/{date}.{slug}.md`
   - Multisite: `content/collections/{collection}/{site}/{slug}.md`
3. Add frontmatter:
   - `id` (UUID), `title`, optional `slug`, `published`, `blueprint`, `date`
4. Populate fields with dummy data that matches field types.
5. For structured collections, ensure the tree file references the entry ID.

## Dummy data guidelines
- Match field types (text, assets, terms, entries, replicator, grid).
- Keep examples consistent with templates.
- Do not change existing `id` values.

## Accuracy checks
- Filenames are slug-based, not UUID-based.

## References
- See `references/entries.md` for condensed file location and frontmatter rules.

