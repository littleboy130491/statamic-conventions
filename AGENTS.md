# Statamic Content Builder — Agent Guide

This document is a routing guide for which skills to use and when. Each skill file in `.claude/skills/` contains its own thorough instructions — read the skill file before executing it.

## General Project Rules

- **Statamic 6** — This is a Statamic 6 project. When unsure about APIs or conventions, check the Statamic docs at `statamic.dev` and verify the installed version via `composer.json`.
- **Check addons** — Before creating blueprints or views, check `composer.json` for installed addons (SEO Pro, Bard, etc.) that may affect field types or template tags.
- **Antlers by default** — Use `.antlers.html` for all view templates unless the user explicitly requests Blade (`.blade.php`).
- **Tailwind CSS + Vite** — Frontend uses Tailwind CSS for styling and Vite for asset bundling.
- **Detect multisite first** — Always read `resources/sites.yaml` before creating any content. If multisite, include `sites`, `propagate`, and `localizable` fields where required.
- **Schema files are the source of truth** — All downstream creation skills read from `schemas/*.md` to know what to build.
- **Each skill has strict file boundaries** — A skill only creates/edits the files it owns. Never combine responsibilities across skills.

## Skill Reference

### Analysis & Reporting (read-only)

| Skill | When to Use |
|-------|-------------|
| `scan-project` | Audit an existing project — generates a full inventory report. Offers follow-up: generate view boilerplates for missing views, or run schema drift check if `schemas/` exists. |
| `check-schema-drift` | Compare `schemas/*.md` against actual project state — finds mismatches, missing items, extras. Run standalone or as a follow-up from `scan-project` (reuses project data already in context to avoid duplicate reads). |

### Schema Design

| Skill | When to Use |
|-------|-------------|
| `create-schema` | First step for new builds. User describes what they need → generates `schemas/*.md` files for collections, taxonomies, globals, forms. Delegates navigation to `create-schema-navigation`. |
| `create-schema-navigation` | Create navigation schemas (`schemas/*_nav.md`) with full menu tree structure — supports entry, archive, term, link, and text item types. Run after `create-schema` when navigations are needed. |

### Content Structure Creation

| Skill | When to Use |
|-------|-------------|
| `create-collections` | Create collection configs from schemas (`content/collections/*.yaml`). |
| `create-taxonomies` | Create taxonomy configs from schemas (`content/taxonomies/*.yaml`). Does NOT set `template` or `term_template` — Statamic auto-resolves views by naming convention. |
| `create-blueprints` | Create/update blueprint YAML files for collections, taxonomies, globals, forms, navigations (`resources/blueprints/`). |
| `create-fieldsets` | Create reusable fieldsets (`resources/fieldsets/*.yaml`). |
| `mount-collections` | Create mount page entries and add `mount` to collection configs. Only for collections with `has_archive: true`. |
| `attach-taxonomies` | Attach taxonomies to collections (adds `taxonomies` to collection config). Informs about collection-scoped taxonomy views that auto-activate. |
| `create-globals` | Create global set configs and data files (`content/globals/`). |
| `create-forms` | Create form configs, blueprints, and email templates (`resources/forms/`). |
| `create-navigations` | Create navigation configs and trees from `schemas/*_nav.md` (`content/navigation/`, `content/trees/`). Resolves entry UUIDs, archive mounts, and taxonomy term URLs. |

### Content Population

| Skill | When to Use |
|-------|-------------|
| `create-entries` | Create collection entries only. For multisite, creates default-site entries only. |
| `create-terms` | Create taxonomy terms only. |
| `create-translations` | Multisite only — create translated entries for non-default sites. |
| `create-translation-terms` | Multisite only — add translations to existing term files. |

### Views & Frontend

| Skill | When to Use |
|-------|-------------|
| `create-view-boilerplates` | Generate `.antlers.html` templates and layouts for collections/taxonomies based on their blueprints. Generates all 4 taxonomy view types: `{taxonomy}/index`, `{taxonomy}/show`, `{collection}/{taxonomy}/index`, `{collection}/{taxonomy}/show`. Only creates missing views, never overwrites. |
| `create-page-templates` | Work with the base layout and page-level templates (`resources/views/`). |
| `create-static-pages` | Create static pages with Antlers templates. |
| `create-static-pages-from-html` | Convert existing HTML files into Statamic page templates. |
| `frontend-screenshot-to-tailwind` | Convert a screenshot into a Tailwind CSS implementation. |
| `frontend-figma-mcp-tailwind` | Convert Figma designs (via MCP) into Tailwind CSS implementation. |

## Build Workflow (Dependency Order)

When building from schemas, execute skills in this order. Skip any step that has no matching schemas.

```
[0]  scan-project              (optional — audit existing project first)
[1]  create-schema             --> schemas/*.md (collections, taxonomies, globals, forms)
[1b] create-schema-navigation  --> schemas/*_nav.md (navigation tree structures)
[2]  create-collections        --> collection configs
[3]  create-blueprints         --> collection blueprints
[4]  mount-collections         --> mount pages (only if has_archive: true)
[5]  create-taxonomies         --> taxonomy configs
[6]  create-blueprints         --> taxonomy blueprints (second pass)
[7]  attach-taxonomies         --> wire up taxonomy-collection relationships
[8]  create-globals            --> global configs + blueprints
[9]  create-forms              --> form configs + blueprints
[10] create-navigations        --> navigation configs + trees
[11] create-view-boilerplates  --> templates + layouts for collections/taxonomies
[12] create-entries            (optional — sample entries)
[13] create-terms              (optional — sample terms)
[14] create-translations       (optional — multisite entry translations)
[15] create-translation-terms  (optional — multisite term translations)
[16] check-schema-drift        (optional — verify project matches schemas)
```

## Batch Execution Rules

When the user says "run all" or similar:

1. Execute skills sequentially in the order above — each depends on prior steps.
2. Skip steps with no matching schemas.
3. If any collection needs mounting but `pages` doesn't exist yet, create it first.
4. Report progress briefly after each skill before moving to the next.
5. Stop on errors — don't continue with broken state.
6. Resolve `collection_relationship` references inline (create referenced collections as part of the batch).

In step-by-step mode, recommend the next step and wait for user confirmation.
