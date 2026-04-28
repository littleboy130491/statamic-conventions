# Scan Project

Scan an existing Statamic project and generate a comprehensive report of all content structures, configurations, and relationships.

## Scope

**You MUST only create or edit one file: `reports/project-scan-{YYYYMMDD-HHmm}.md`** (e.g., `reports/project-scan-20250115-1430.md`).

This skill is entirely read-only with respect to the Statamic project. You read all project files to gather information, then write the report.

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
- Role and group configs (`resources/roles.yaml`, `resources/user-groups.yaml`)
- Routes, controllers, or any PHP files
- CSS, JS, or frontend assets

You may **read** any project file. You write only to `reports/project-scan-{YYYYMMDD-HHmm}.md`.

**Optional output (user-requested only):** `schemas/*.md` — reverse-engineered schema files in `create-schema` format. Only generate schema files if the user explicitly requests it. Do not generate schemas automatically.

The timestamped filename ensures each scan produces a unique file and previous reports are preserved.

## Quick Start

1. **Detect multisite** — Read `resources/sites.yaml`
2. **Scan all content types** — Collections, taxonomies, blueprints, navigations, globals, forms, fieldsets, asset containers, users and roles
3. **List entries and terms** — Title of every entry/term, per collection/taxonomy, per site (if multisite)
4. **Map relationships** — Taxonomy attachments, mounts, entry field references, fieldset imports
5. **Write report** — Output structured markdown to `reports/project-scan-{YYYYMMDD-HHmm}.md`
6. **Follow up** — If missing view files detected, offer to generate boilerplates via `create-view-boilerplates`

## Workflow

### Step 1: Detect Multisite

Read `resources/sites.yaml` (or `config/statamic/sites.php`) — **read only**.

Record:
- Whether the project is single-site or multisite (1 site key = single, 2+ = multisite)
- All site handles, names, locales, and URLs
- Which site is the default (first listed)

### Step 2: Scan Collections

For each `.yaml` file in `content/collections/`:

1. Read the collection config file
2. Record: `title`, `route`, `template`, `layout`, `date`, `structure` (and `max_depth`), `mount`, `taxonomies`, `sites`, `propagate`, `revisions`, `sort_by`, `sort_dir`, `preview_targets`
3. Determine `has_single` — true if `route` is present, false if absent
4. Determine `has_archive` — true if `mount` is present, false if absent
5. **Check view file existence** — For each `template` and `layout` value, check whether the corresponding view file exists in `resources/views/`. Check for both `.antlers.html` and `.blade.php` extensions. For example, `template: posts/show` means check for `resources/views/posts/show.antlers.html` or `resources/views/posts/show.blade.php`. Record the result (exists with which extension, or missing).

### Step 3: Count and List Entries

For each collection discovered in Step 2:

1. List files in `content/collections/{collection}/`
2. If multisite: list files in each `content/collections/{collection}/{site}/` subdirectory and count per site
3. If single site: count all `.md` files directly in the collection directory
4. Record total entry count and per-site breakdown
5. **Read each entry file** and extract the `title` and `id` from the YAML frontmatter. For multisite, also record which site directory the entry belongs to and the `origin` field (the ID of the original entry this is a translation of — absent for default-site entries)
6. **Record entry localization** — For multisite, determine which sites/languages each entry has been localized into. An entry is considered localized in a non-default site if a file with a matching `origin` exists in that site's subdirectory. Record the list of localized languages per entry.

### Step 4: Scan Blueprints

For each subdirectory and file under `resources/blueprints/`:

1. **Collection blueprints** — `resources/blueprints/collections/{collection}/{handle}.yaml`
2. **Taxonomy blueprints** — `resources/blueprints/taxonomies/{taxonomy}/{handle}.yaml`
3. **Global blueprints** — `resources/blueprints/globals/{handle}.yaml`
4. **Form blueprints** — `resources/blueprints/forms/{handle}.yaml`
5. **Navigation blueprints** — `resources/blueprints/navigation/{handle}.yaml`
6. **Asset blueprints** — `resources/blueprints/assets/{handle}.yaml`
7. **User blueprint** — `resources/blueprints/user.yaml`

For each blueprint:
- Read the YAML file
- Record: `title`, tab names, section names
- For each field: record `handle`, `type`, `required` (has `validate: [required]` or `required: true`), `localizable`, and notable properties (`max_files`, `collections`, `taxonomies`, `max_items`, `buttons`, etc.)
- Detect fieldset imports (`import: fieldset_handle`)
- Detect relationship fields (`type: entries`, `type: terms`, `type: users`, `type: link`) and record what they reference

### Step 5: Scan Taxonomies

For each `.yaml` file in `content/taxonomies/` (only root-level config files, not term files in subdirectories):

1. Read the taxonomy config file
2. Record: `title`, `layout`, `sites`, `revisions` (also record `template` and `term_template` if present, though these are typically omitted)
3. Determine which view types are enabled — the presence of `layout: {taxonomy}/layout` in the config signals that views are enabled. Check if a schema exists at `schemas/{handle}.md` and read `has_index`, `has_show`, `has_collection_index`, `has_collection_show` (all default to `true`). If no schema exists and `layout` is present, assume all views enabled.
4. **Check layout view file** — If `layout` is configured, check whether the view file exists in `resources/views/`. Check for both `.antlers.html` and `.blade.php` extensions.
5. **Check taxonomy index view** — If `has_index` is enabled, check if `resources/views/{taxonomy}/index.antlers.html` or `.blade.php` exists.
6. **Check taxonomy show view** — If `has_show` is enabled, check if `resources/views/{taxonomy}/show.antlers.html` or `.blade.php` exists.
7. **Check collection-scoped taxonomy views** — Cross-reference with collection configs from Step 2. For each collection that has this taxonomy in its `taxonomies` list:
   - If `has_collection_index` is enabled: check `resources/views/{collection}/{taxonomy}/index.antlers.html` (or `.blade.php`)
   - If `has_collection_show` is enabled: check `resources/views/{collection}/{taxonomy}/show.antlers.html` (or `.blade.php`)
   Record existence for each.

### Step 6: Count and List Terms

For each taxonomy discovered in Step 5:

1. List `.yaml` files in `content/taxonomies/{taxonomy}/` (excluding the config file itself — the config is at `content/taxonomies/{handle}.yaml`, terms are in the `content/taxonomies/{handle}/` subdirectory)
2. If multisite: list files in each `content/taxonomies/{taxonomy}/{site}/` subdirectory and count per site
3. Record term count and per-site breakdown
4. **Read each term file** and extract the `title` and `id`. For multisite, also record which site directory the term belongs to and the `origin` field (the ID of the original term this is a translation of — absent for default-site terms)
5. **Record term localization** — For multisite, determine which sites/languages each term has been localized into. A term is considered localized in a non-default site if a file with a matching `origin` exists in that site's subdirectory. Record the list of localized languages per term.

### Step 7: Scan Navigations

For each `.yaml` file in `content/navigation/`:

1. Read the navigation config file
2. Record: `title`, `max_depth`, `collections`, `sites`

For each navigation, check for tree files:
- Single site: `content/trees/navigation/{handle}.yaml`
- Multisite: `content/navigation/{site}/{handle}.yaml`

If tree files exist, count the total number of items (including nested children) and count top-level items separately.

Check for navigation blueprints at `resources/blueprints/navigation/{handle}.yaml`.

### Step 8: Scan Globals

For each `.yaml` file in `content/globals/` (root level only, not site subdirectories):

1. Read the global config file
2. Record: `title`, `sites`
3. Check for data files at `content/globals/{site}/{handle}.yaml` (multisite) or inline `data:` key (single site)

### Step 9: Scan Forms

For each `.yaml` file in `resources/forms/`:

1. Read the form config file
2. Record: `title`, `honeypot`, `store`, `email` configurations

### Step 10: Scan Fieldsets

For each `.yaml` file in `resources/fieldsets/`:

1. Read the fieldset file
2. Record: `title`, field count, field handles and types

Cross-reference with blueprints from Step 4 to determine which blueprints import each fieldset.

### Step 11: Scan Asset Containers

For each `.yaml` file in `content/assets/`:

1. Read the asset container config
2. Record: `title`, `disk`

### Step 12: Scan Users and Roles

1. **Scan roles** — Read `resources/roles.yaml`. For each role, record: `handle`, `title`, and `permissions` list.
2. **Scan user groups** — Read `resources/user-groups.yaml` if it exists. For each group, record: `handle`, `title`, and `roles` list.
3. **Scan users** — For each `.yaml` file in `users/`:
   - Read the user file
   - Record: `name`, `email` (from the filename, e.g., `users/john@example.com.yaml`), `super` (boolean, true if super admin), `roles` (array of role handles), `groups` (array of group handles)
   - Cross-reference roles and groups to determine the effective role(s) for each user

### Step 13: Build Relationship Map

Using data gathered in all previous steps, build the relationships:

1. **Taxonomy-Collection attachments** — From collection configs (`taxonomies` field)
2. **Collection mounts** — From collection configs (`mount` field). Resolve the mount UUID to find the page entry title by reading the corresponding entry file in the pages collection
3. **Entry field references** — From blueprint fields with `type: entries` (record the `collections` list), `type: terms` (record the `taxonomies` list), `type: users`
4. **Fieldset imports** — From blueprint `import:` directives, cross-referenced with fieldset files

### Step 14: Write Report

Create the `reports/` directory if it does not exist. Write the report to `reports/project-scan-{YYYYMMDD-HHmm}.md` using the current date and time (e.g., `reports/project-scan-20250115-1430.md`). This ensures each scan produces a unique file and does not overwrite previous reports.

### Step 15: Generate Schemas (Optional, User-Requested Only)

If the user requests reverse-engineered schemas:

1. For each collection, generate a schema file at `schemas/{handle}.md` using the collection schema format from `create-schema`:
   - Set `has_single: true` if the collection has a `route`, `false` otherwise
   - Set `has_archive: true` if the collection has a `mount`, `false` otherwise
   - Extract `route`, `dated`, `structure`, `mount` from the collection config
   - Extract `taxonomy_relationship` from the `taxonomies` list in the collection config
   - Extract `collection_relationship` from blueprint fields with `type: entries`
   - Extract blueprint fields from the actual blueprint YAML, converting to pipe-delimited format
   - If multisite, add `multisite: true` and list all sites

2. For each taxonomy, generate a schema file at `schemas/{handle}.md`:
   - Set `has_index`, `has_show`, `has_collection_index`, `has_collection_show` based on which view files exist (all default to `true` — only include fields that are `false`)
   - Extract `collections` from cross-referencing which collections have this taxonomy in their `taxonomies` list

3. For each global, generate a schema at `schemas/{handle}.md`

4. For each form, generate a schema at `schemas/{handle}.md`

5. For each navigation, generate a schema at `schemas/{handle}.md`

**Important:** If schema files already exist in `schemas/`, warn the user before overwriting. Ask for confirmation.

## Report Format

The report at `reports/project-scan-{YYYYMMDD-HHmm}.md` uses this structure:

```markdown
# Project Scan Report

Generated: {YYYY-MM-DD HH:mm}
Project: {project root directory name}

---

## 1. Multisite Configuration

**Status:** {Single site | Multisite}

{If multisite:}

| Site Handle | Name | Locale | URL |
|-------------|------|--------|-----|
| english | English | en_US | / |
| indonesian | Indonesian | id_ID | /id/ |

Default site: `{first site handle}`

{If single site:}

Site handle: `{handle}`

---

## 2. Collections

| Collection | Entries | Route | Template | Layout | Dated | Structure | Mounted |
|------------|---------|-------|----------|--------|-------|-----------|---------|
| pages | 5 | /{parent_uri}/{slug} | pages/show | pages/layout | No | Yes (depth: 3) | No |
| posts | 12 | /blog/{slug} | posts/show | posts/layout | Yes | No | Yes (blog) |
| team | 4 | -- | -- | -- | No | No | No |

### View File Status

| Collection | View | Path | Status |
|------------|------|------|--------|
| pages | template | `resources/views/pages/show.antlers.html` | Exists |
| pages | layout | `resources/views/pages/layout.antlers.html` | Exists |
| posts | template | `resources/views/posts/show.antlers.html` | Exists |
| posts | layout | `resources/views/posts/layout.blade.php` | Exists |
| team | template | -- | No template configured |
| team | layout | -- | No layout configured |

{If multisite, add entry counts per site:}

### Entry Counts by Site

| Collection | {site1} | {site2} | Total |
|------------|---------|---------|-------|
| pages | 5 | 5 | 10 |
| posts | 12 | 10 | 22 |

### Collection Details

#### {handle}
- **Title:** {title}
- **Route:** `{route}` {or `--` if absent}
- **Template:** `{template}` {or `--` if absent} — {View exists: `resources/views/{template}.antlers.html` | View exists: `resources/views/{template}.blade.php` | MISSING | --}
- **Layout:** `{layout}` {or `--` if absent} — {View exists: `resources/views/{layout}.antlers.html` | View exists: `resources/views/{layout}.blade.php` | MISSING | --}
- **Structure:** {Yes, max_depth: N | No}
- **Dated:** {Yes | No}
- **Mounted:** {Yes (mount entry title) | No}
- **Taxonomies:** {comma-separated list | --}
- **Blueprints:** {comma-separated list}
- **Entry count:** {N} {or "N (site1: X, site2: Y)" for multisite}
- **Entries localization:** {comma-separated list of sites/languages that have localized entries, with count per site} {or `--` if single site}

**Entries:**

{If single site:}

| # | Title | ID |
|---|-------|----|
| 1 | Hello World | {uuid} |
| 2 | About Us | {uuid} |
| 3 | Getting Started | {uuid} |

{If multisite:}

| # | Title | Site | ID | Origin ID | Localized |
|---|-------|------|----|-----------|-----------|
| 1 | Hello World | english | {uuid} | -- | indonesian |
| 2 | Hello World | indonesian | {uuid} | {origin-uuid} | -- |
| 3 | About Us | english | {uuid} | -- | indonesian |
| 4 | Tentang Kami | indonesian | {uuid} | {origin-uuid} | -- |
| 5 | Contact | english | {uuid} | -- | (not localized) |

Default-site entries show which other sites have a localized version in the `Localized` column. Non-default-site entries (translations) show `--` in the `Localized` column. Default-site entries with no translations in any other site show `(not localized)`.

{Repeat for each collection...}

---

## 3. Taxonomies

| Taxonomy | Terms | Layout | Attached To | Views |
|----------|-------|--------|-------------|-------|
| categories | 6 | categories/layout | posts | has_index, has_show, has_collection_index, has_collection_show |
| tags | 15 | tags/layout | posts | has_index, has_show, has_collection_index, has_collection_show |

### View File Status

| Taxonomy | View | Path | Status |
|----------|------|------|--------|
| categories | layout | `resources/views/categories/layout.antlers.html` | Exists |
| categories | index | `resources/views/categories/index.antlers.html` | MISSING |
| categories | show | `resources/views/categories/show.antlers.html` | Exists |
| categories | collection index | `resources/views/posts/categories/index.antlers.html` | MISSING |
| categories | collection show | `resources/views/posts/categories/show.antlers.html` | MISSING |
| tags | layout | `resources/views/tags/layout.antlers.html` | Exists |
| tags | index | `resources/views/tags/index.antlers.html` | MISSING |
| tags | show | `resources/views/tags/show.antlers.html` | MISSING |
| tags | collection index | `resources/views/posts/tags/index.antlers.html` | MISSING |
| tags | collection show | `resources/views/posts/tags/show.antlers.html` | MISSING |

### Taxonomy Details

#### {handle}
- **Title:** {title}
- **Layout:** `{layout}` {or `--` if absent} — {View exists | MISSING | --}
- **Enabled views:** {list which of has_index, has_show, has_collection_index, has_collection_show are enabled}
- **View files:**
  - Index: `resources/views/{taxonomy}/index` — {Exists | MISSING | N/A}
  - Show: `resources/views/{taxonomy}/show` — {Exists | MISSING | N/A}
  - {For each attached collection:}
  - Collection index: `resources/views/{collection}/{taxonomy}/index` — {Exists | MISSING | N/A}
  - Collection show: `resources/views/{collection}/{taxonomy}/show` — {Exists | MISSING | N/A}
- **Attached to collections:** {comma-separated list}
- **Blueprints:** {comma-separated list}
- **Term count:** {N}
- **Terms localization:** {comma-separated list of sites/languages that have localized terms, with count per site} {or `--` if single site}

**Terms:**

{If single site:}

| # | Title | ID |
|---|-------|----|
| 1 | News | {uuid} |
| 2 | Technology | {uuid} |
| 3 | Lifestyle | {uuid} |

{If multisite:}

| # | Title | Site | ID | Origin ID | Localized |
|---|-------|------|----|-----------|-----------|
| 1 | News | english | {uuid} | -- | indonesian |
| 2 | Berita | indonesian | {uuid} | {origin-uuid} | -- |
| 3 | Technology | english | {uuid} | -- | indonesian |
| 4 | Teknologi | indonesian | {uuid} | {origin-uuid} | -- |
| 5 | Lifestyle | english | {uuid} | -- | (not localized) |

Default-site entries show which other sites have a localized version of the term in the `Localized` column. Non-default-site entries (translations) show `--` in the `Localized` column. Default-site terms with no translations in any other site show `(not localized)`.

{Repeat for each taxonomy...}

---

## 4. Blueprints

### Collections

| Collection | Blueprint | Fields | Fieldsets Imported |
|------------|-----------|--------|--------------------|
| pages | default | 2 (title, content) | -- |
| pages | home | 6 (title, hero_heading, ...) | -- |
| posts | post | 5 (title, featured_image, ...) | seo_fields |

### Taxonomies

| Taxonomy | Blueprint | Fields | Fieldsets Imported |
|----------|-----------|--------|--------------------|
| categories | categories | 2 (title, description) | -- |

### Globals

| Global | Blueprint | Fields |
|--------|-----------|--------|
| site_settings | site_settings | 5 (site_name, tagline, ...) |

### Forms

| Form | Blueprint | Fields |
|------|-----------|--------|
| contact | contact | 4 (name, email, subject, submission_message) |

### Blueprint Field Details

#### {type}: {parent} / Blueprint: {handle}

| Handle | Type | Required | Localizable | Notes |
|--------|------|----------|-------------|-------|
| title | text | Yes | Yes | -- |
| content | bard | No | Yes | buttons: h2, h3, bold, italic, link |
| featured_image | assets | No | Yes | max_files: 1 |
| author | users | No | No | max_items: 1 |
| categories | terms | No | No | taxonomies: categories |

{For replicator fields, list sets and sub-fields indented:}

| Handle | Type | Required | Localizable | Notes |
|--------|------|----------|-------------|-------|
| sections | replicator | No | Yes | -- |
| &nbsp;&nbsp;set: hero | | | | |
| &nbsp;&nbsp;&nbsp;&nbsp;heading | text | No | Yes | -- |
| &nbsp;&nbsp;&nbsp;&nbsp;image | assets | No | Yes | max_files: 1 |
| &nbsp;&nbsp;set: text_block | | | | |
| &nbsp;&nbsp;&nbsp;&nbsp;content | bard | No | Yes | -- |

{Repeat for each blueprint...}

---

## 5. Navigations

| Navigation | Max Depth | Collections | Items |
|------------|-----------|-------------|-------|
| header | 2 | pages | 5 (3 top-level) |
| footer | 1 | pages | 3 (3 top-level) |

### Navigation Details

#### {handle}
- **Title:** {title}
- **Max depth:** {N}
- **Collections:** {comma-separated list}
- **Sites:** {comma-separated list | all}
- **Tree items:** {N total (M top-level)}
- **Has blueprint:** {Yes | No}

{Repeat for each navigation...}

---

## 6. Other Content Types

### Globals

| Handle | Title | Sites |
|--------|-------|-------|
| site_settings | Site Settings | english, indonesian |
| social | Social Links | english, indonesian |

### Forms

| Handle | Title | Store | Honeypot |
|--------|-------|-------|----------|
| contact | Contact Form | Yes | website |

### Asset Containers

| Handle | Title | Disk |
|--------|-------|------|
| assets | Assets | assets |

### Fieldsets

| Handle | Title | Fields | Used In |
|--------|-------|--------|---------|
| seo_fields | SEO Fields | 3 (meta_title, meta_description, og_image) | posts/post, pages/default |

---

## 7. Relationships Map

### Taxonomy-Collection Attachments

| Taxonomy | Attached To Collections |
|----------|-------------------------|
| categories | posts |
| tags | posts |

### Collection Mounts

| Collection | Mount Page | Mount Entry ID |
|------------|------------|----------------|
| posts | Blog (pages) | {uuid} |

### Entry Field References (Cross-Collection)

| Blueprint | Field | Type | References |
|-----------|-------|------|------------|
| posts/post | author | users | Users |
| pages/home | featured_posts | entries | posts |

### Fieldset Usage

| Fieldset | Imported In |
|----------|-------------|
| seo_fields | posts/post, pages/default |

---

## 8. Users and Roles

### Roles

| Handle | Title | Permissions |
|--------|-------|-------------|
| editor | Editor | edit entries, create entries, publish entries |
| author | Author | edit own entries, create entries |

{If no roles defined, show "None found."}

### User Groups

| Handle | Title | Roles |
|--------|-------|-------|
| content_team | Content Team | editor |

{If no user groups defined, show "None found."}

### Users

| # | Name | Email | Super Admin | Roles | Groups |
|---|------|-------|-------------|-------|--------|
| 1 | John Doe | john@example.com | No | editor | content_team |
| 2 | Jane Smith | jane@example.com | Yes | -- | -- |
| 3 | Bob Writer | bob@example.com | No | author | -- |

{If no users found, show "None found."}

**Total users:** {N}
**Super admins:** {N}

---

## 9. File Inventory

| Category | Count |
|----------|-------|
| Collection configs | {N} |
| Taxonomy configs | {N} |
| Blueprints (total) | {N} |
| Entries (total) | {N} |
| Terms (total) | {N} |
| Navigation configs | {N} |
| Navigation trees | {N} |
| Global configs | {N} |
| Form configs | {N} |
| Fieldsets | {N} |
| Asset containers | {N} |
| Users | {N} |
| Roles | {N} |
| User groups | {N} |
```

Use `--` for absent/empty values. Do not omit sections — if no items exist for a category, write "None found."

## Rules

1. **Only write to** `reports/project-scan-{YYYYMMDD-HHmm}.md`. No other files (except `schemas/*.md` if explicitly requested by the user). Each scan creates a new timestamped file to avoid overwriting previous reports.
2. **Do not modify any Statamic project files.** This skill is entirely read-only with respect to the project.
3. **Scan everything.** Do not skip content types. If a directory is empty or does not exist, note it as "None found" in the report rather than omitting the section.
4. **Resolve mount UUIDs.** When a collection has a `mount` field, find the entry file with that `id` in the pages collection and include the entry title in the report. If the entry is not found, report "Unresolved (ID: {uuid})".
5. **Count and list accurately.** Entry counts must reflect actual `.md` files. Term counts must reflect actual `.yaml` files (excluding config files). For multisite, provide per-site breakdowns. Every entry and term must be listed by title under its collection/taxonomy detail block.
6. **Detect fieldset imports.** When a blueprint has `import: {handle}`, record this in both the blueprint details and the fieldset usage section.
7. **Distinguish config from data files.** For taxonomies, `content/taxonomies/{handle}.yaml` is the config; files inside `content/taxonomies/{handle}/` are terms. For globals, `content/globals/{handle}.yaml` is the config; files inside `content/globals/{site}/` are data.
8. **Handle missing directories gracefully.** If `content/navigation/` does not exist, report "None found" for navigations. Do not error.
9. **Report raw values.** Do not interpret or modify config values. Report `route`, `template`, `layout` exactly as they appear in the YAML files.
10. **Use `--` for absent values.** When a field is not present in a config (e.g., no route for a `has_single: false` collection), display `--` in the table.
11. **Handle replicator and grid sub-fields.** Blueprint fields of type `replicator` or `grid` contain nested field definitions. List these as indented sub-fields in the blueprint field details.
12. **Do not auto-generate schemas.** Only generate reverse-engineered `schemas/*.md` files if the user explicitly requests it. If schema files already exist, warn before overwriting.
13. **Check view file existence.** For every `template` and `layout` value in collection and taxonomy configs, check whether the corresponding view file exists in the project's `resources/views/` directory. Check for both `.antlers.html` and `.blade.php` extensions. Report the actual path and extension found, or mark as `MISSING` if neither exists. If the config value is absent (`--`), skip the check. If legacy taxonomy `template` or `term_template` values are present, record them as legacy config but do not require new projects to use them.
14. **Check taxonomy views per schema fields.** Read `has_index`, `has_show`, `has_collection_index`, `has_collection_show` from the schema (all default to `true`). Only check view files for enabled view types. Collection-scoped views only apply when collections are attached.

## Follow Up Questions

After writing the report, present follow-up options based on the scan results. Only include options that are relevant — skip any where the condition is not met.

### 1. Missing View Files

If any `template` or `layout` values were marked as `MISSING` in the View File Status tables (collections or taxonomies), ask the user:

> The scan found **{N}** missing view file(s):
> - `resources/views/{value}.antlers.html` — {collection/taxonomy} {view type}
> - `resources/views/{value}.antlers.html` — {collection/taxonomy} {view type}
> - ...
>
> Would you like me to generate boilerplate view files for these using the `create-view-boilerplates` skill?

List every missing view file path so the user can see exactly what would be created.

If the user confirms, invoke the `create-view-boilerplates` skill to generate the missing `.antlers.html` files. That skill will read the relevant blueprints and produce documented, field-aware boilerplate templates and layouts.

### 2. Schema Drift Check

If the `schemas/` directory exists and contains at least one `.md` file, ask the user:

> The project has **{N}** schema file(s) in `schemas/`. Would you like me to check for schema drift — comparing the schemas against the actual project state to find mismatches, missing items, or extras?

If the user confirms, proceed to run the `check-schema-drift` skill **without re-reading project files**. All project data gathered during this scan (multisite config, collection configs, taxonomy configs, global configs, form configs, navigation configs, blueprints, fieldsets, view file existence) is already in context. The drift check should:
1. Read and parse the `schemas/*.md` files (Step 1 of `check-schema-drift`)
2. Skip Step 2 (Read Actual Project State) entirely — reuse the data already gathered during this scan
3. Proceed directly to Step 3 (Compare Schema vs. Actual) using the scan data
4. Write the drift report to `reports/schema-drift-{YYYYMMDD-HHmm}.md`
5. Present drift-specific follow-up questions as defined in `check-schema-drift`

This avoids re-reading every project file a second time.

If no `schemas/` directory exists or it is empty, skip this question.

## Accuracy Checks

Before finishing, verify:
- [ ] Report file is at `reports/project-scan-{YYYYMMDD-HHmm}.md` with the current date and time
- [ ] Multisite status matches `resources/sites.yaml` content
- [ ] Every `.yaml` file in `content/collections/` has a corresponding entry in the Collections section
- [ ] Every `.yaml` file in `content/taxonomies/` (root level) has a corresponding entry in the Taxonomies section
- [ ] Entry counts match actual file counts in collection directories
- [ ] Every entry is listed by title under its collection detail block
- [ ] Term counts match actual `.yaml` file counts in taxonomy subdirectories (excluding config files)
- [ ] Every term is listed by title under its taxonomy detail block
- [ ] For multisite, each entry/term listing includes the site it belongs to
- [ ] Every blueprint file under `resources/blueprints/` is accounted for
- [ ] All relationship fields (`entries`, `terms`, `users`, `link`) are captured in the Relationships Map
- [ ] All `mount` references are resolved to page entry titles (or marked as "Unresolved")
- [ ] All `taxonomies` lists in collection configs are reflected in the Taxonomy-Collection Attachments
- [ ] All fieldset imports in blueprints are cross-referenced in the Fieldset Usage section
- [ ] Every `template` and `layout` value in collection/taxonomy configs has been checked against `resources/views/` for `.antlers.html` or `.blade.php` existence
- [ ] Taxonomy views checked per schema fields (`has_index`, `has_show`, `has_collection_index`, `has_collection_show`)
- [ ] Collection-scoped taxonomy views only checked when collections are attached
- [ ] View File Status tables are present in both Collections and Taxonomies sections
- [ ] Missing view files are clearly marked as `MISSING` in the report
- [ ] For multisite, each entry/term in the default site shows which other sites it has been localized into (or `(not localized)`)
- [ ] For multisite, `Entries localization` and `Terms localization` summary fields are populated in collection/taxonomy details
- [ ] All users in `users/` are listed with their name, email, super admin status, roles, and groups
- [ ] All roles from `resources/roles.yaml` are listed with their permissions
- [ ] User groups from `resources/user-groups.yaml` are listed (if file exists)
- [ ] No Statamic project files were created, modified, or deleted
