# Check Schema Drift

Compare schema files in `schemas/` against the actual Statamic project state and generate a drift report showing mismatches, missing items, and extras.

## Scope

**You MUST only create or edit one file: `reports/schema-drift-{YYYYMMDD-HHmm}.md`** (e.g., `reports/schema-drift-20250115-1430.md`).

This skill is entirely read-only with respect to the Statamic project. You read schema files and project files to compare them, then write the drift report.

Do NOT create, edit, or modify any Statamic project files — including but not limited to:
- Collection configs (`content/collections/`)
- Taxonomy configs (`content/taxonomies/`)
- Blueprint files (`resources/blueprints/`)
- Entry/content files (`content/collections/{collection}/`)
- Term files (`content/taxonomies/{taxonomy}/`)
- Navigation configs or trees (`content/navigation/`, `content/trees/`)
- Global configs or data (`content/globals/`)
- Form configs (`resources/forms/`)
- Fieldset files (`resources/fieldsets/`)
- View/template files (`resources/views/`)
- Config files (`config/`)
- Schema files (`schemas/`)
- User files (`users/`)
- Routes, controllers, or any PHP files
- CSS, JS, or frontend assets

You may **read** any project file and any schema file. You write only to `reports/schema-drift-{YYYYMMDD-HHmm}.md`.

The timestamped filename ensures each check produces a unique file and previous reports are preserved.

## Quick Start

1. **Read all schemas** — Parse every `schemas/*.md` file
2. **Read actual project state** — Collection configs, taxonomy configs, blueprints, globals, forms, navigations, `resources/sites.yaml` *(skip if invoked as a follow-up from `scan-project` — see below)*
3. **Compare schema vs. actual** — Across every dimension
4. **Write drift report** — Output structured markdown to `reports/schema-drift-{YYYYMMDD-HHmm}.md`
5. **Follow up** — Offer actionable fixes based on what drifts were found

## Consolidation with scan-project

When this skill is invoked as a follow-up from the `scan-project` skill, all project data (multisite config, collection configs, taxonomy configs, global configs, form configs, navigation configs, blueprints, fieldsets, view file existence) has already been read and is in context. In that case:

- **Skip Step 2 entirely** — do not re-read any project files
- **Start at Step 1** — parse the schema files (these were not read by `scan-project`)
- **Proceed to Step 3** — compare schemas against the project data already in context
- **Continue with Steps 4–5** as normal

When invoked standalone (not as a follow-up), execute all steps including Step 2.

## Workflow

### Step 1: Parse All Schema Files

Read every `.md` file in `schemas/`. For each file, parse the key-value header and pipe-delimited field list according to the `create-schema` format:

**Header keys to extract per schema type:**

- **Collection:** `schema_name`, `schema_type`, `title`, `has_single`, `has_archive`, `route`, `dated`, `structure`, `structure_max_depth`, `mount`, `taxonomy_relationship`, `collection_relationship`, `multisite`, `sites`
- **Taxonomy:** `schema_name`, `schema_type`, `title`, `has_single`, `route`, `collections`, `multisite`, `sites`
- **Global:** `schema_name`, `schema_type`, `title`, `multisite`, `sites`
- **Form:** `schema_name`, `schema_type`, `title`, `store`, `honeypot`, `email_to`, `email_subject`
- **Navigation:** `schema_name`, `schema_type`, `title`, `max_depth`, `collections`, `multisite`, `sites`

**Blueprint and field parsing:**

For each schema file, parse one or more blueprint blocks (separated by `---` for collections with multiple blueprints). For each blueprint block, extract:
- `blueprint` — the blueprint handle
- `blueprint_description` — optional description
- `fields:` — the pipe-delimited field list

For each field line, parse:
- `handle` — first segment before `|`
- `type` — second segment
- `required` or `optional` — third segment (for collections/forms)
- `localizable` — present if the field should be localizable
- `notes` — trailing segment with extra properties (e.g., `max_files: 1`, `collections: team_members`)

Handle indented sub-fields for replicator sets and grid columns:
- Lines starting with `  set: {name}` indicate a replicator set
- Indented lines after a set line are sub-fields of that set
- Indented lines after a grid field are grid column fields

Record all parsed schemas in memory, grouped by `schema_type`.

### Step 2: Read Actual Project State

Read the actual Statamic project files — **read only, do not modify**:

#### 2a. Multisite Configuration
Read `resources/sites.yaml` (or `config/statamic/sites.php`). Record:
- Whether the project is single-site or multisite
- All site handles, names, locales, and URLs

#### 2b. Collections
For each `.yaml` file in `content/collections/`:
1. Read the collection config
2. Record: `title`, `route`, `dated` (true if `date: true`), `structure` (true if `structure` key present), `structure.max_depth`, `mount`, `taxonomies`, `sites`, `template`, `layout`

#### 2c. Taxonomies
For each root-level `.yaml` file in `content/taxonomies/`:
1. Read the taxonomy config
2. Record: `title`, `route`, `template`, `layout`, `term_template`, `sites`

#### 2d. Globals
For each `.yaml` file in `content/globals/` (root level only):
1. Read the global config
2. Record: `title`, `sites`

#### 2e. Forms
For each `.yaml` file in `resources/forms/`:
1. Read the form config
2. Record: `title`, `store`, `honeypot`, `email` configurations

#### 2f. Navigations
For each `.yaml` file in `content/navigation/`:
1. Read the navigation config
2. Record: `title`, `max_depth`, `collections`, `sites`

#### 2g. Blueprints
For each blueprint file under `resources/blueprints/`:
1. **Collection blueprints** — `resources/blueprints/collections/{collection}/{handle}.yaml`
2. **Taxonomy blueprints** — `resources/blueprints/taxonomies/{taxonomy}/{handle}.yaml`
3. **Global blueprints** — `resources/blueprints/globals/{handle}.yaml`
4. **Form blueprints** — `resources/blueprints/forms/{handle}.yaml`
5. **Navigation blueprints** — `resources/blueprints/navigation/{handle}.yaml`

For each blueprint, parse all tabs and sections. For each field, record:
- `handle`, `type`, `required` (has `validate: [required]` or `required: true`), `localizable`
- Notable properties: `max_files`, `collections`, `taxonomies`, `max_items`, `buttons`, `sets`, `fields`, etc.
- Resolve fieldset imports (`import: fieldset_handle`) by reading `resources/fieldsets/{handle}.yaml`
- Parse replicator sets and their sub-fields
- Parse grid columns and their sub-fields

#### 2h. View Files
For each collection/taxonomy with a `template` or `layout` value, check whether the view file exists in `resources/views/` (check for both `.antlers.html` and `.blade.php`).

### Step 3: Compare Schema vs. Actual

Perform a systematic comparison across every dimension. For each comparison, classify the result as one of:
- **Match** — schema and actual agree
- **Missing in project** — defined in schema but not found in project
- **Extra in project** — found in project but not in any schema
- **Mismatch** — both exist but values differ

#### 3a. Multisite Comparison

Compare the schema-level multisite settings against the actual project:
- Do any schemas declare `multisite: true` while the project is single-site (or vice versa)?
- Do schema `sites` lists match the actual sites in `resources/sites.yaml`?
- Are there sites in schemas not present in the project, or sites in the project not referenced in any schema?

#### 3b. Collection Comparison

For each collection schema:
1. **Existence** — Does the collection config exist at `content/collections/{schema_name}.yaml`? If not → missing in project.
2. **Config values** — Compare each schema property against the actual config:
   - `route` — schema `route` vs. actual `route`
   - `dated` — schema `dated: true/false` vs. actual (presence of `date: true`)
   - `structure` — schema `structure: true/false` vs. actual (presence of `structure` key)
   - `structure_max_depth` — schema `structure_max_depth` vs. actual `structure.max_depth`
   - `mount` — schema declares a mount page slug vs. actual `mount` UUID (note: schema uses slug, actual uses UUID — compare by resolving the UUID to the mount page entry and checking its slug)
   - `taxonomy_relationship` — schema taxonomy list vs. actual `taxonomies` list in config
   - `sites` — schema sites list vs. actual `sites` list in config (only for multisite)

For each actual collection NOT in any schema → extra in project.

#### 3c. Taxonomy Comparison

For each taxonomy schema:
1. **Existence** — Does the taxonomy config exist at `content/taxonomies/{schema_name}.yaml`?
2. **Config values** — Compare:
   - `route` — schema `route` vs. actual `route`
   - `collections` — schema `collections` list vs. which actual collections have this taxonomy in their `taxonomies` config
   - `sites` — schema sites list vs. actual `sites` list

For each actual taxonomy NOT in any schema → extra in project.

#### 3d. Global Comparison

For each global schema:
1. **Existence** — Does the global config exist at `content/globals/{schema_name}.yaml`?
2. **Config values** — Compare:
   - `sites` — schema sites list vs. actual `sites` list

For each actual global NOT in any schema → extra in project.

#### 3e. Form Comparison

For each form schema:
1. **Existence** — Does the form config exist at `resources/forms/{schema_name}.yaml`?
2. **Config values** — Compare:
   - `store` — schema `store` vs. actual `store`
   - `honeypot` — schema `honeypot` vs. actual `honeypot`

For each actual form NOT in any schema → extra in project.

#### 3f. Navigation Comparison

For each navigation schema:
1. **Existence** — Does the navigation config exist at `content/navigation/{schema_name}.yaml`?
2. **Config values** — Compare:
   - `max_depth` — schema `max_depth` vs. actual `max_depth`
   - `collections` — schema `collections` list vs. actual `collections` list
   - `sites` — schema sites list vs. actual `sites` list

For each actual navigation NOT in any schema → extra in project.

#### 3g. Blueprint Comparison

For each blueprint defined in a schema:
1. **Existence** — Does the blueprint file exist at the expected path? (e.g., `resources/blueprints/collections/{collection}/{blueprint}.yaml`)
2. If missing → missing blueprint in project.

For each actual blueprint NOT referenced in any schema → extra blueprint in project.

#### 3h. Field-Level Comparison

For each blueprint that exists in both schema and project, compare fields:

1. **Missing fields** — field handle in schema but not in actual blueprint
2. **Extra fields** — field handle in actual blueprint but not in schema (skip `seo`, `seo_previews` fields — these are added by convention and not in schemas)
3. **Field mismatches** — field exists in both but properties differ:
   - `type` mismatch — schema type vs. actual type
   - `required` mismatch — schema says required but actual has no `validate: [required]`, or vice versa
   - `localizable` mismatch — schema says localizable but actual field has `localizable: false` or is missing the property (when multisite is enabled), or vice versa
   - Property mismatches — `max_files`, `collections`, `taxonomies`, `max_items`, `buttons`, etc.

For replicator fields, also compare:
- Missing/extra sets
- Missing/extra sub-fields within sets
- Sub-field type mismatches

For grid fields, also compare:
- Missing/extra columns
- Column type mismatches

#### 3i. View File Comparison

For each collection/taxonomy schema with `has_single: true`:
- Check if the expected template view file exists in `resources/views/`
- If the actual collection/taxonomy config has a `template` value, check for that specific file
- If no template value exists in the actual config, check for a default pattern (e.g., `{handle}/show.antlers.html`)

Report missing view files.

### Step 4: Write Drift Report

Create the `reports/` directory if it does not exist. Write the report to `reports/schema-drift-{YYYYMMDD-HHmm}.md` using the current date and time.

### Step 5: Follow Up

After writing the report, offer actionable next steps based on what drifts were found (see [Follow-Up Questions](#follow-up-questions)).

## Report Format

The report at `reports/schema-drift-{YYYYMMDD-HHmm}.md` uses this structure:

```markdown
# Schema Drift Report

Generated: {YYYY-MM-DD HH:mm}
Project: {project root directory name}

---

## Summary

| Metric | Count |
|--------|-------|
| Schemas checked | {N} |
| Matches (no drift) | {N} |
| Drifts found | {N} |
| Missing in project | {N} |
| Extra in project | {N} |

---

## 1. Multisite Configuration

**Schema says:** {Multisite with sites: english, indonesian | Single site | No multisite schemas found}
**Project says:** {Multisite with sites: english, indonesian | Single site}
**Status:** {Match | DRIFT}

{If drift, explain the mismatch:}
- Schema declares multisite but project is single-site
- Schema lists site `{handle}` not found in project
- Project has site `{handle}` not referenced in any schema

---

## 2. Collections

### Overview

| Collection | In Schema | In Project | Status |
|------------|-----------|------------|--------|
| pages | Yes | Yes | {Match / DRIFT / MISSING / EXTRA} |
| posts | Yes | Yes | {Match / DRIFT} |
| team | Yes | No | MISSING |
| events | No | Yes | EXTRA |

### Collection Config Diffs

{For each collection that exists in both schema and project but has config mismatches:}

#### {handle}

| Property | Schema | Project | Status |
|----------|--------|---------|--------|
| route | /blog/{slug} | /blog/{slug} | Match |
| dated | true | false | DRIFT |
| structure | false | false | Match |
| mount | blog | blog | Match |
| taxonomies | categories, tags | categories | DRIFT |
| sites | english, indonesian | english, indonesian | Match |

{Only show rows where there is something to compare — omit rows for properties not in schema and not in project.}

---

## 3. Taxonomies

### Overview

| Taxonomy | In Schema | In Project | Status |
|----------|-----------|------------|--------|
| categories | Yes | Yes | {Match / DRIFT} |
| tags | Yes | No | MISSING |

### Taxonomy Config Diffs

{Same table format as collections, comparing route, collections, sites.}

---

## 4. Globals

### Overview

| Global | In Schema | In Project | Status |
|--------|-----------|------------|--------|
| site_settings | Yes | Yes | {Match / DRIFT} |

### Global Config Diffs

{Table format comparing sites.}

---

## 5. Forms

### Overview

| Form | In Schema | In Project | Status |
|------|-----------|------------|--------|
| contact | Yes | Yes | {Match / DRIFT} |

### Form Config Diffs

{Table format comparing store, honeypot.}

---

## 6. Navigations

### Overview

| Navigation | In Schema | In Project | Status |
|------------|-----------|------------|--------|
| header | Yes | Yes | {Match / DRIFT} |

### Navigation Config Diffs

{Table format comparing max_depth, collections, sites.}

---

## 7. Blueprint Diffs

### Overview

| Type | Parent | Blueprint | In Schema | In Project | Status |
|------|--------|-----------|-----------|------------|--------|
| collection | pages | default | Yes | Yes | {Match / DRIFT} |
| collection | pages | home | Yes | No | MISSING |
| collection | posts | post | Yes | Yes | Match |
| taxonomy | categories | categories | Yes | Yes | DRIFT |
| global | site_settings | site_settings | Yes | Yes | Match |
| form | contact | contact | Yes | Yes | Match |

### Field-Level Diffs

{For each blueprint that exists in both schema and project but has field differences:}

#### {type}: {parent} / Blueprint: {handle}

**Missing fields** (in schema, not in project):

| Handle | Schema Type | Schema Required | Schema Localizable |
|--------|-------------|-----------------|---------------------|
| excerpt | textarea | optional | localizable |

**Extra fields** (in project, not in schema):

| Handle | Project Type | Project Required | Project Localizable |
|--------|--------------|------------------|----------------------|
| sidebar_image | assets | No | Yes |

**Field mismatches** (in both, but properties differ):

| Handle | Property | Schema | Project |
|--------|----------|--------|---------|
| content | type | bard | markdown |
| featured_image | required | required | optional |
| author | localizable | -- | true |
| featured_image | max_files | 1 | 3 |

{Repeat for each blueprint with diffs...}

---

## 8. View Files

{For each collection/taxonomy schema with has_single: true:}

| Type | Handle | Expected View | Status |
|------|--------|---------------|--------|
| collection | posts | resources/views/posts/show.antlers.html | Exists |
| collection | pages | resources/views/pages/show.antlers.html | MISSING |
| taxonomy | categories | resources/views/categories/show.antlers.html | Exists |

---

## 9. Full Drift Inventory

| # | Severity | Type | Item | Detail |
|---|----------|------|------|--------|
| 1 | High | Missing collection | team | Defined in schema, not in project |
| 2 | High | Missing blueprint | pages/home | Defined in schema, not in project |
| 3 | Medium | Config mismatch | posts.dated | Schema: true, Project: false |
| 4 | Medium | Missing field | posts/post.excerpt | Field in schema, not in blueprint |
| 5 | Medium | Field mismatch | posts/post.content.type | Schema: bard, Project: markdown |
| 6 | Low | Extra collection | events | In project, not in any schema |
| 7 | Low | Extra field | posts/post.sidebar_image | In blueprint, not in schema |
| 8 | Medium | Missing view | pages/show.antlers.html | has_single: true but view missing |
| 9 | Low | Localizable drift | posts/post.author | Schema: --, Project: true |
```

**Severity levels:**
- **High** — Missing content type or blueprint (schema defines it, project doesn't have it)
- **Medium** — Config mismatch, missing field, field type/property mismatch, missing view file, localizable drift
- **Low** — Extra items in project not in schema, extra fields in blueprints not in schema

Use `--` for absent/empty values. Do not omit sections — if no drifts exist for a category, write "No drifts found."

## Follow-Up Questions

After writing the report, offer actionable next steps based on what drifts were found. Present the follow-up as a numbered list of options:

**If missing collections/taxonomies/globals/forms/navigations were found:**
> The drift report found **{N}** content type(s) missing from the project:
> - Collection `{handle}` — defined in `schemas/{handle}.md`
> - Taxonomy `{handle}` — defined in `schemas/{handle}.md`
> - ...
>
> Would you like me to create the missing content types using the appropriate skills (`create-collections`, `create-taxonomies`, `create-globals`, `create-forms`, `create-navigations`)?

**If missing blueprints were found:**
> The drift report found **{N}** blueprint(s) missing from the project:
> - `resources/blueprints/collections/{collection}/{handle}.yaml`
> - `resources/blueprints/taxonomies/{taxonomy}/{handle}.yaml`
> - ...
>
> Would you like me to create the missing blueprints using the `create-blueprints` skill?

**If missing fields or field mismatches were found:**
> The drift report found **{N}** field-level drift(s):
> - {N} missing fields
> - {N} field mismatches (type, required, localizable, or property differences)
>
> Would you like me to update the affected blueprints using the `create-blueprints` skill to match the schema definitions?

**If missing view files were found:**
> The drift report found **{N}** missing view file(s):
> - `resources/views/{path}.antlers.html` — {collection/taxonomy} template
> - ...
>
> Would you like me to generate boilerplate view files using the `create-view-boilerplates` skill?

**If extra items were found in the project (not in any schema):**
> The drift report found **{N}** item(s) in the project not defined in any schema:
> - Collection `{handle}`
> - Blueprint `{parent}/{handle}`
> - Fields: {list}
> - ...
>
> Would you like me to reverse-engineer schemas for these using the `create-schema` skill to bring the schemas up to date?

List only the follow-up options that are relevant (i.e., skip categories with zero drifts). If no drifts were found at all, report that the project matches all schemas perfectly.

## Schema Parsing Reference

### Key-Value Header

Each line before the `fields:` line is a key-value pair separated by `: `. Parse these rules:
- Boolean values: `true` and `false` (string, not YAML boolean)
- List values (prefixed with `- `): `- english - indonesian` → `["english", "indonesian"]`
- Numeric values: `3` → integer
- String values: everything else

### Field Lines

After the `fields:` line, each line is pipe-delimited:
```
handle | type | required_or_optional | localizable | notes
```

- Fields may have 2–5 segments depending on schema type and multisite status
- For globals: `handle | type | notes` (no required/optional column)
- For forms: `handle | type | required/optional | notes` (no localizable column)
- For collections/taxonomies with multisite: `handle | type | required/optional | localizable | notes`
- For collections/taxonomies without multisite: `handle | type | required/optional | notes`
- `notes` may contain comma-separated properties like `max_files: 1, collections: team_members`
- Indented lines (starting with spaces) are replicator set definitions or grid sub-fields

### Blueprint Separator

Collections with multiple blueprints separate them with a `---` line on its own. Each blueprint block starts with `blueprint: {handle}`.

## Rules

1. **Only write to** `reports/schema-drift-{YYYYMMDD-HHmm}.md`. No other files. Each check creates a new timestamped file to avoid overwriting previous reports.
2. **Do not modify any project files or schema files.** This skill is entirely read-only with respect to the project and schemas.
3. **Compare every dimension.** Do not skip content types, config properties, blueprints, or fields. If a schema defines it, compare it. If the project has it, check if a schema covers it.
4. **Resolve mount references.** When a schema says `mount: blog`, find the actual collection config's `mount` UUID, resolve it to a page entry, and compare the page slug against the schema value.
5. **Handle the SEO convention.** Blueprints in the project will have `seo` and `seo_previews` fields added by the `create-blueprints` skill convention. These are NOT in schema field lists. Do not report `seo` or `seo_previews` as "extra fields" — silently skip them during field comparison.
6. **Handle sidebar convention fields.** The `create-blueprints` skill may add conventional fields like `slug` to the sidebar tab. If `slug` is in the blueprint but not in the schema, do not report it as extra — silently skip it.
7. **Treat `title` field carefully.** Many schemas list `title` as a field, and all blueprints have a `title` field. If `title` is in both schema and blueprint, compare properties. If `title` is only in the blueprint but not in the schema field list, do not report it as extra (it is always implicit).
8. **Compare taxonomy attachments bidirectionally.** A collection schema's `taxonomy_relationship` should match the actual collection config's `taxonomies` list. A taxonomy schema's `collections` should match which actual collections have that taxonomy attached.
9. **Report raw values.** Show exact schema values and exact project values side by side. Do not interpret or normalize values unless necessary for comparison (e.g., mount slug resolution).
10. **Use severity levels consistently.** High = missing content type/blueprint. Medium = config/field mismatch, missing field, missing view. Low = extras not in schema.
11. **Handle missing `schemas/` directory.** If no `schemas/` directory exists or it is empty, report this clearly and stop — there is nothing to compare.
12. **Handle empty project gracefully.** If no Statamic content types exist yet, report everything in schemas as "missing in project."

## Accuracy Checks

Before finishing, verify:
- [ ] Report file is at `reports/schema-drift-{YYYYMMDD-HHmm}.md` with the current date and time
- [ ] Every `.md` file in `schemas/` was read and parsed
- [ ] Every collection config in `content/collections/` was read
- [ ] Every taxonomy config in `content/taxonomies/` (root level) was read
- [ ] Every global config in `content/globals/` (root level) was read
- [ ] Every form config in `resources/forms/` was read
- [ ] Every navigation config in `content/navigation/` was read
- [ ] Every blueprint under `resources/blueprints/` was read
- [ ] Multisite configuration was compared (schema vs. `resources/sites.yaml`)
- [ ] All collection config properties were compared (route, dated, structure, mount, taxonomies, sites)
- [ ] All taxonomy config properties were compared (route, collections, sites)
- [ ] All global, form, and navigation config properties were compared
- [ ] All blueprint existence was checked (schema blueprints vs. actual blueprint files)
- [ ] All field-level comparisons were done for blueprints existing in both schema and project
- [ ] SEO fields (`seo`, `seo_previews`) and `slug` were silently skipped as extras
- [ ] Mount references were resolved (schema slug vs. actual UUID → page entry)
- [ ] View file existence was checked for all `has_single: true` schemas
- [ ] Summary counts are accurate
- [ ] Full Drift Inventory table lists every individual drift found
- [ ] Severity levels are assigned correctly
- [ ] Follow-up questions are offered only for drift categories that have actual drifts
- [ ] No project files or schema files were created, modified, or deleted
