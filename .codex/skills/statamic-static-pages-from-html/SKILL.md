---
name: statamic-static-pages-from-html
description: Map static HTML into Statamic static page fields, entries, and Antlers templates; use when converting fixed HTML pages to CMS-managed pages.
---

# Statamic Static Pages From HTML

## Quick start
- Check `resources/sites.yaml` to confirm single site vs multisite.
- Identify or create the page blueprint for the HTML structure.
- Map HTML content to fields, update the entry file, and create an Antlers template with conditionals.

## Workflow
1. Detect multisite in `resources/sites.yaml`.
2. Read the page blueprint and inventory available fields.
3. Scan HTML and map content to fields.
4. Update entry file:
   - Single site: `content/collections/pages/{slug}.md`
   - Multisite: `content/collections/pages/{site}/{slug}.md`
5. Create the template at `resources/views/pages/{template}.antlers.html`.
6. Wrap all variables with `{{ if }}` conditionals unless required.

## Accuracy checks
- Page entry filenames are slug-based, not UUID-based.

## References
- See `references/static-pages-from-html.md` for condensed rules and patterns.

