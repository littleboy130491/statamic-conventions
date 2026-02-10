# Attach Taxonomies

Attach a Statamic taxonomy to one or more collections so taxonomy terms appear in the entry sidebar.

## Scope

**You MUST only edit these files:**
- `content/collections/{collection}.yaml` — adding only the taxonomy handle to the `taxonomies` list
- `content/taxonomies/{handle}.yaml` — adding only the `term_template` field

Do NOT create, edit, or modify any other files — including but not limited to:
- Taxonomy config files beyond adding `term_template` — use `create-taxonomies` skill to create new taxonomies
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
3. Add `term_template` to the taxonomy config

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

### Step 3: Add Term Template to Taxonomy Config

Edit `content/taxonomies/{handle}.yaml` — add **only** the `term_template` field for each attached collection. Do not modify any other fields in that file:

```yaml
term_template: '{collection}/show'  # replace {collection} with the collection handle
```

Taxonomy fields appear automatically in the entry sidebar — no blueprint changes needed.

## Rules

1. **Only edit** the `taxonomies` list in collection configs and the `term_template` field in the taxonomy config. Do not change any other fields in either file.
2. **Do not create new files.** This skill only edits existing config files.
3. **Do not create taxonomies** — use `create-taxonomies` skill instead. If the taxonomy does not exist, ask the user first.
4. **Do not create blueprints** — use `create-blueprints` skill instead.
5. **Do not create terms or entries** — use `create-entries` skill instead.
6. **Do not create or edit templates, routes, PHP, or frontend files.**
7. You may read any project file to inform your work, but do not modify files outside the allowed scope.

## Accuracy Checks

Before finishing, verify:
- [ ] Taxonomy exists at `content/taxonomies/{handle}.yaml` before attaching
- [ ] Collection config edits only added to the `taxonomies` list — no other fields were changed
- [ ] Taxonomy config edits only added the `term_template` field — no other fields were changed
- [ ] No new files were created
- [ ] No blueprints, entries, templates, or other out-of-scope files were created or edited