# Statamic Content Builder — Agent Workflow

This document describes the end-to-end workflow for building Statamic content structures from a user's description.

## Finding Skills

Before executing any skill, the agent must locate the skills folder containing the skill files needed for each operation.

**Skill folder discovery logic:**
1. First, check if a `skills` folder exists in the **root project directory** (the current working directory)
2. If not found, check if a `skills` folder exists in the **`.claude` directory** (`~/.claude/skills` or `.claude/skills` relative to the project)
3. Use whichever location is found; if neither exists, inform the user that skills are not installed

When invoking a skill, always use the Skill tool with the skill name (e.g., `create-schema`). The Skill tool will handle locating and executing the skill from the correct folder.

## How It Works

The user describes what they want to build in plain language:

> "Create posts, like WordPress"
> "I need a portfolio site with projects, services, and a team section"
> "Build a blog with categories and tags"

The agent generates schema files, then **guides the user through each downstream skill**. The user can choose to execute steps one at a time or run multiple (or all) steps at once in batch mode.

---

## Workflow

### Step 1: Generate Schema

**Skill:** `create-schema`
**Output:** `schemas/*.md`

Ask the user what their site needs (or infer from their description). Generate one `.md` schema file per content type in the `schemas/` directory. Each schema file maps to a downstream skill.

Do not create any Statamic files in this step — only schema `.md` files.

**After completing this step**, read all generated schema files and present the user with a summary of what was created and the recommended next steps in order. For example:

> Schema files created:
> - `schemas/blog_posts.md` (collection)
> - `schemas/categories.md` (taxonomy)
> - `schemas/site_settings.md` (global)
>
> Recommended next steps:
> 1. Create collections — `blog_posts`
> 2. Create blueprints — `post` for blog_posts
> 3. Mount collections — mount `blog_posts` on a `blog` page
> 4. Create taxonomies — `categories`
> 5. Attach taxonomies — attach `categories` to `blog_posts`
> 6. Create entries — sample entries for blog_posts
> 7. Create globals — `site_settings`
>
> You can run these one at a time, or say **"run all"** to execute all steps automatically.
>
> Which step would you like to run next?

Wait for the user to tell you which step to execute, or whether they want to run all (or a subset) at once.

### Step 2: Create Collections

**Skill:** `create-collections`
**Output:** `content/collections/{handle}.yaml`

For each schema file with `schema_type: collection`, create the collection config.

Do not add `mount` or `taxonomies` fields yet — those are handled by dedicated skills in later steps.

**After completing**, report what was created and recommend the next step.

### Step 3: Create Blueprints

**Skill:** `create-blueprints`
**Output:** `resources/blueprints/collections/{collection}/{handle}.yaml`

For each collection created in Step 2, create its blueprint(s) based on the fields defined in the schema.

**After completing**, report what was created and recommend the next step.

### Step 4: Mount Collections on Pages

**Skill:** `mount-collections` (creates the page entry + adds `mount` to collection config)

For each collection that has a `mount` value in its schema:

1. Create a page entry in the pages collection (uses `create-entries` rules internally)
2. Add the `mount` field to the collection config, referencing the page entry's UUID

If the `pages` collection does not exist yet, inform the user and recommend creating it first (Step 2 and Step 3 for pages).

**After completing**, report what was created and recommend the next step.

### Step 5: Create Taxonomies

**Skill:** `create-taxonomies`
**Output:** `content/taxonomies/{handle}.yaml`

For each schema file with `schema_type: taxonomy`, create the taxonomy config.

Also recommend creating taxonomy blueprints using `create-blueprints`.

**After completing**, report what was created and recommend the next step.

### Step 6: Attach Taxonomies

**Skill:** `attach-taxonomies`

For each collection that has `taxonomy_relationship` in its schema, attach the taxonomies:

1. Add taxonomy handles to the collection's `taxonomies` list
2. Add `term_template` to the taxonomy config

**After completing**, report what was changed and recommend the next step.

### Step 7: Resolve Collection Relationships

Check all schemas for `collection_relationship` entries that reference collections not yet created.

For each missing collection, inform the user and recommend the steps needed:

> The `blog_posts` collection references `team_members` via `collection_relationship`, but `team_members` does not exist yet.
>
> Recommended steps to resolve:
> 1. Create collection — `team_members`
> 2. Create blueprint — for `team_members`
> 3. Mount collection — mount `team_members` if it has a mount page
>
> Which step would you like to run?

Wait for the user to confirm before creating anything.

### Step 8: Create Entries (Optional)

**Skill:** `create-entries`
**Output:** `content/collections/{collection}/{site}/{slug}.md`

Create example/dummy entries for **collections only**. Do NOT create taxonomy terms — use `create-terms` for those.

For multisite projects, create entries for the default site only. Recommend `create-translations` for non-default sites.

### Step 9: Create Terms (Optional)

**Skill:** `create-terms`
**Output:** `content/taxonomies/{taxonomy}/{slug}.yaml`

Create example/dummy term files for **taxonomies only**. Do NOT create collection entries — use `create-entries` for those.

For multisite projects, term files do not use site subdirectories. Recommend `create-translation-terms` for adding translations to terms.

### Step 10: Create Translations (Optional, Multisite Only)

**Skill:** `create-translations`
**Output:** `content/collections/{collection}/{non-default-site}/{slug}.md`

For each default-site entry created in Step 8, create translation entries for all non-default sites. This skill is for **collection entry translations only**.

Only include fields marked `localizable: true` in the blueprint.

Do NOT use this for taxonomy terms — use `create-translation-terms` instead.

### Step 11: Create Translation Terms (Optional, Multisite Only)

**Skill:** `create-translation-terms`
**Output:** Edits existing `content/taxonomies/{taxonomy}/{slug}.yaml` (adds `localizations` key)

For each term created in Step 9, add translations for all non-default sites by adding the `localizations` key to existing term files. This skill is for **taxonomy term translations only**.

Taxonomy terms store translations within the same file (via `localizations`), unlike collection entries which use separate files per site.

Do NOT use this for collection entries — use `create-translations` instead.

### Step 12: Create Globals (Optional)

**Skill:** `create-globals`
**Output:** `content/globals/{handle}.yaml` + `content/globals/{site}/{handle}.yaml`

For each schema file with `schema_type: global`, create the global set config and data files.

Also recommend creating global blueprints using `create-blueprints`.

### Step 13: Create Forms (Optional)

**Skill:** `create-forms`
**Output:** `resources/forms/{handle}.yaml` + `resources/blueprints/forms/{handle}.yaml`

For each schema file with `schema_type: form`, create the form config, blueprint, and email templates.

### Step 14: Create Navigations (Optional)

**Skill:** `create-navigations`
**Output:** `content/navigation/{handle}.yaml` + `content/trees/navigation/{handle}.yaml`

For each schema file with `schema_type: navigation`, create the navigation config and tree.

---

## Batch Execution (Run All)

After schema creation, the user may ask to run all remaining steps at once (e.g., "run all", "do everything", "create it all"). When this happens, the agent **must execute each skill in the correct dependency order**, invoking one skill at a time sequentially.

### Execution Order

When running all steps, execute the applicable skills in this exact order. **Skip any step that has no matching schemas.**

| Order | Skill | Condition |
|-------|-------|-----------|
| 1 | `create-collections` | Any schema with `schema_type: collection` |
| 2 | `create-blueprints` | Any collection or taxonomy that needs blueprints |
| 3 | `mount-collections` | Any collection schema with a `mount` value |
| 4 | `create-taxonomies` | Any schema with `schema_type: taxonomy` |
| 5 | `create-blueprints` | Any taxonomy that needs blueprints (second pass) |
| 6 | `attach-taxonomies` | Any collection with `taxonomy_relationship` in its schema |
| 7 | `create-entries` | If user wants sample entries (collections only) |
| 8 | `create-terms` | If user wants sample terms (taxonomies only) |
| 9 | `create-translations` | Multisite only — collection entry translations |
| 10 | `create-translation-terms` | Multisite only — taxonomy term translations |
| 11 | `create-globals` | Any schema with `schema_type: global` |
| 12 | `create-forms` | Any schema with `schema_type: form` |
| 13 | `create-navigations` | Any schema with `schema_type: navigation` |

### Rules for Batch Execution

1. **Use the right skill for each step.** Never combine responsibilities — always invoke the dedicated skill for each operation (e.g., use `create-collections` for collections, `create-taxonomies` for taxonomies, `create-entries` for entries, `create-terms` for terms).
2. **Execute sequentially, not in parallel.** Each skill depends on the output of prior skills (e.g., blueprints need collections to exist first, mounting needs collections and blueprints).
3. **Handle the `pages` collection dependency.** If any collection requires mounting but there is no `pages` schema, create the `pages` collection and its blueprint first before other collections.
4. **Report progress after each skill.** Even in batch mode, briefly report what was created after each skill completes before moving to the next (e.g., "Created collections: blog_posts, pages. Moving on to blueprints...").
5. **Stop on errors.** If a skill fails or encounters a problem, stop the batch and report the issue to the user rather than continuing with broken state.
6. **Resolve relationships inline.** If `collection_relationship` references are found during batch execution, create the referenced collections as part of the batch (following the same order: collection → blueprint → mount).

### Partial Batch

The user may also request a subset of steps at once (e.g., "create collections, blueprints, and taxonomies"). In this case, execute only the requested steps, still in the correct dependency order from the table above.

---

## Workflow Summary

```
User describes what they need
        |
        v
[1] create-schema              --> schemas/*.md
        |
        v
    Present next steps to user, offer step-by-step or "run all"
        |
        +-----------+-----------+
        |                       |
   Step-by-step            Batch ("run all")
        |                       |
        v                       v
  User picks a step       Execute all applicable
  --> execute             skills sequentially
  --> report & repeat     in dependency order
        |                       |
        v                       v
[2]  create-collections         |
[3]  create-blueprints          |  Same skills,
[4]  mount-collections          |  same order,
[5]  create-taxonomies          |  no pauses
[6]  create-blueprints (tax)    |  between steps
[7]  attach-taxonomies          |
[8]  resolve relationships      |
[9]  create-entries             |
[10] create-terms               |
[11] create-translations        |
[12] create-translation-terms   |
[13] create-globals             |
[14] create-forms               |
[15] create-navigations         |
```

## Key Rules

- **Never auto-execute unless the user opts in.** In step-by-step mode, recommend the next step and wait for confirmation. In batch mode (user said "run all" or similar), execute all applicable steps sequentially without pausing between them.
- **Each skill has strict file boundaries.** A skill only creates/edits the files it owns. It never touches files owned by another skill.
- **Always detect multisite first.** Read `resources/sites.yaml` before creating any content. If multisite, include `sites`, `propagate`, and `localizable` fields where required.
- **Schema files are the source of truth.** All downstream skills read from `schemas/*.md` to know what to build.
- **Surface dependencies early.** If a step requires something that doesn't exist yet (e.g., mounting needs a pages collection), inform the user and recommend the prerequisite step first.
- **Mount every non-pages collection.** If a collection schema has a `mount` value, it must be mounted on a page in the pages collection.
