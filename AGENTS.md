# Statamic Content Builder — Agent Workflow

This document describes the end-to-end workflow for building Statamic content structures from a user's description.

## How It Works

The user describes what they want to build in plain language:

> "Create posts, like WordPress"
> "I need a portfolio site with projects, services, and a team section"
> "Build a blog with categories and tags"

The agent generates schema files, then **guides the user step-by-step** through each downstream skill. The agent does NOT automatically execute steps — it recommends the next step and waits for the user to confirm before proceeding.

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
> Which step would you like to run next?

Wait for the user to tell you which step to execute.

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

Create example/dummy entries for each collection so the user has sample content to work with.

For multisite projects, create entries for the default site only. Recommend `create-translations` for non-default sites.

### Step 9: Create Translations (Optional, Multisite Only)

**Skill:** `create-translations`
**Output:** `content/collections/{collection}/{non-default-site}/{slug}.md`

For each default-site entry created in Step 8, create translation entries for all non-default sites.

Only include fields marked `localizable: true` in the blueprint.

### Step 10: Create Globals (Optional)

**Skill:** `create-globals`
**Output:** `content/globals/{handle}.yaml` + `content/globals/{site}/{handle}.yaml`

For each schema file with `schema_type: global`, create the global set config and data files.

Also recommend creating global blueprints using `create-blueprints`.

### Step 11: Create Forms (Optional)

**Skill:** `create-forms`
**Output:** `resources/forms/{handle}.yaml` + `resources/blueprints/forms/{handle}.yaml`

For each schema file with `schema_type: form`, create the form config, blueprint, and email templates.

### Step 12: Create Navigations (Optional)

**Skill:** `create-navigations`
**Output:** `content/navigation/{handle}.yaml` + `content/trees/navigation/{handle}.yaml`

For each schema file with `schema_type: navigation`, create the navigation config and tree.

---

## Workflow Summary

```
User describes what they need
        |
        v
[1] create-schema          --> schemas/*.md
        |
        v
    Present next steps to user, wait for confirmation
        |
        v
[2] create-collections     --> user confirms --> execute
        |
        v
[3] create-blueprints      --> user confirms --> execute
        |
        v
[4] mount-collections      --> user confirms --> execute
        |
        v
[5] create-taxonomies      --> user confirms --> execute
        |
        v
[6] attach-taxonomies      --> user confirms --> execute
        |
        v
[7] resolve relationships  --> user confirms --> repeat Steps 2–6 for missing collections
        |
        v
[8] create-entries          --> user confirms --> execute
        |
        v
[9] create-translations    --> user confirms --> execute
        |
        v
[10] create-globals         --> user confirms --> execute
        |
        v
[11] create-forms           --> user confirms --> execute
        |
        v
[12] create-navigations     --> user confirms --> execute
```

## Key Rules

- **Never auto-execute.** After each step, recommend the next step and wait for the user to confirm. Do not proceed automatically.
- **Each skill has strict file boundaries.** A skill only creates/edits the files it owns. It never touches files owned by another skill.
- **Always detect multisite first.** Read `resources/sites.yaml` before creating any content. If multisite, include `sites`, `propagate`, and `localizable` fields where required.
- **Schema files are the source of truth.** All downstream skills read from `schemas/*.md` to know what to build.
- **Surface dependencies early.** If a step requires something that doesn't exist yet (e.g., mounting needs a pages collection), inform the user and recommend the prerequisite step first.
- **Mount every non-pages collection.** If a collection schema has a `mount` value, it must be mounted on a page in the pages collection.
