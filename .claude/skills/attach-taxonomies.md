# Attach Taxonomies

Attach a Statamic taxonomy to one or more collections so taxonomy terms appear in the entry sidebar.

## Scope

**You MUST only edit these files:**
- `content/collections/{collection}.yaml` — adding only the taxonomy handle to the `taxonomies` list

Do NOT create, edit, or modify any other files — including but not limited to:
- Taxonomy config files (`content/taxonomies/`) — use `create-taxonomies` skill to create new taxonomies
- Blueprint files (`resources/blueprints/`) — use `create-blueprints` skill instead
- Term/content files (`content/taxonomies/{taxonomy}/`) — use `create-entries` skill instead
- Entry files (`content/collections/{collection}/`) — use `create-entries` skill instead
- View/template files (`resources/views/`)
- Config files (`config/`)
- Fieldset files (`resources/fieldsets/`)
- Routes, controllers, or any PHP files
- CSS, JS, or frontend assets

You may **read** other project files to inform your work, but do not modify them beyond the specific fields listed above.

If the task requires changes outside the allowed scope, stop and inform the user — do not make those changes yourself.

## Quick Start

1. Confirm the taxonomy exists
2. Add the taxonomy handle to the collection's `taxonomies` list
3. Inform about collection-scoped taxonomy views

## Workflow

### Step 1: Verify Taxonomy Exists

Read `content/taxonomies/{handle}.yaml` — **read only in this step**.

If the taxonomy does not exist, stop and ask the user whether they want to create it. If yes, use the `create-taxonomies` skill — do not create taxonomy files from this skill.

### Step 2: Add Taxonomy to Collection Config

Edit `content/collections/{collection}.yaml` — add **only** the taxonomy handle to the `taxonomies` list. Do not modify any other fields in that file:

```yaml
taxonomies:
  - {handle}  # replace with the taxonomy handle
```

If the `taxonomies` key already exists, append to the existing list. If it does not exist, add it.

Repeat for each collection the user wants to attach.

Taxonomy fields appear automatically in the entry sidebar — no blueprint changes needed.

### Step 3: Inform About Collection-Scoped Views

After attaching, inform the user that collection-scoped taxonomy views are now available. These views auto-activate when the corresponding view files exist — no config field is needed:

| View | URL Pattern | View Path | Purpose |
|------|-------------|-----------|---------|
| Collection taxonomy index | `/{collection}/{taxonomy}` | `{collection}/{taxonomy}/index` | Lists terms associated with entries in this collection |
| Collection single term | `/{collection}/{taxonomy}/{term}` | `{collection}/{taxonomy}/show` | Entries for a term, filtered to this collection only |

For example, attaching `categories` to `posts` enables:
- `/posts/categories` → lists only categories that have posts
- `/posts/categories/news` → shows only posts tagged "news"

These are separate from the global taxonomy views (`/categories` and `/categories/news`) which show terms and entries across all collections.

Suggest running the `create-view-boilerplates` skill to generate the view files for these collection-scoped routes.

## Rules

1. **Only edit** the `taxonomies` list in collection configs. Do not change any other fields.
2. **Do not create new files.** This skill only edits existing collection config files.
3. **Do not add `template` or `term_template` to taxonomy configs** — Statamic auto-resolves views by naming convention.
3. **Do not create taxonomies** — use `create-taxonomies` skill instead. If the taxonomy does not exist, ask the user first.
4. **Do not create blueprints** — use `create-blueprints` skill instead.
5. **Do not create terms or entries** — use `create-entries` skill instead.
6. **Do not create or edit templates, routes, PHP, or frontend files.**
7. You may read any project file to inform your work, but do not modify files outside the allowed scope.

## Accuracy Checks

Before finishing, verify:
- [ ] Taxonomy exists at `content/taxonomies/{handle}.yaml` before attaching
- [ ] Collection config edits only added to the `taxonomies` list — no other fields were changed
- [ ] No taxonomy config files were modified (no `template` or `term_template` added)
- [ ] No new files were created
- [ ] No blueprints, entries, templates, or other out-of-scope files were created or edited