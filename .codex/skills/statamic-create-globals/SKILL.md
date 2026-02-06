---
name: statamic-create-globals
description: Create or update Statamic global sets, per-site global data, and global blueprints; use when defining site-wide settings or shared content.
---

# Statamic Create Globals

## Quick start
- Decide single-site vs multisite.
- Create global set config in `content/globals/{handle}.yaml`.
- Add data in the correct location.

## Workflow
1. Single site:
   - `content/globals/{handle}.yaml` with `title` and `data:` object.
2. Multisite:
   - Config: `content/globals/{handle}.yaml` with `title` and `sites` list.
   - Data: `content/globals/{site}/{handle}.yaml` with field values.
3. Create or update blueprint in `resources/blueprints/globals/{handle}.yaml` when field types or UI are needed.

## References
- See `references/globals.md` for condensed structure and examples.

