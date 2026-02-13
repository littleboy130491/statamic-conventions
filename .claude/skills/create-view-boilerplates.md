# Create View Boilerplates

Scan collection and taxonomy configs for `template` and `layout` values, check which view files are missing, read the corresponding blueprints, and generate documented boilerplate `.antlers.html` files with field-aware markup.

## Scope

**You MUST only create files at `resources/views/**/*.antlers.html`** — collection/taxonomy templates and layouts only.

Do NOT create, edit, or modify any other files — including but not limited to:
- Collection configs (`content/collections/`) — use `create-collections` skill instead
- Taxonomy configs (`content/taxonomies/`) — use `create-taxonomies` skill instead
- Blueprint files (`resources/blueprints/`) — use `create-blueprints` skill instead
- Fieldset files (`resources/fieldsets/`) — use `create-fieldsets` skill instead
- Entry/content files (`content/collections/{collection}/`) — use `create-entries` skill instead
- Term files (`content/taxonomies/{taxonomy}/`) — use `create-terms` skill instead
- The base layout (`resources/views/layout.antlers.html`) — use `create-page-templates` skill to work with it
- Config files (`config/`)
- Routes, controllers, or any PHP files
- CSS, JS, or frontend assets

You may **read** other project files (e.g., `resources/sites.yaml`, collection configs, taxonomy configs, blueprints, fieldsets, page entries, existing view files) to inform your work, but do not modify them.

If the task requires changes outside the allowed paths, stop and inform the user — do not make those changes yourself.

## Quick Start

1. **Detect multisite** — Read `resources/sites.yaml`
2. **Scan collection configs** — Read `content/collections/*.yaml` for `template`/`layout` values
3. **Scan mount pages** — Find mount page entries and extract their `template` values for archive views
4. **Scan taxonomy configs** — Read `content/taxonomies/*.yaml` for `layout` value (`layout: {taxonomy}/layout` signals views are enabled)
5. **Check which views are missing** — Only generate files that do not already exist
6. **Generate boilerplate views** — Read blueprints and create `.antlers.html` files with documented field markup
7. **Check for navigation** — If no navigation exists, ask whether to create it via `create-schema-navigation` and `create-navigations` skills

## Workflow

### Step 1: Detect Multisite

Read `resources/sites.yaml` (or `config/statamic/sites.php`) — **read only, do not modify**:
- Single site: One site key defined
- Multisite: Multiple site keys (e.g., `english`, `indonesian`)

Record site handles for use in later steps when scanning page entry directories.

### Step 2: Scan Collection Configs

For each `.yaml` file in `content/collections/`:

1. Read the collection config file
2. Record `template` and `layout` values
3. Record the `mount` UUID if present (used in Step 3)
4. Skip collections with no `template` and no `layout` (these have `has_single: false` — entries have no public pages)

Add each `template` and `layout` value to the list of views to check/generate, tagged with the collection handle and view type (`template` or `layout`).

### Step 3: Scan Mount Pages

For each collection that has a `mount` UUID in its config:

1. Scan page entry files in `content/collections/pages/` (single site) or `content/collections/pages/{site}/` (multisite) for a file whose frontmatter `id` matches the mount UUID
2. Read the matching page entry and extract its `template` value (e.g., `template: posts/index`)
3. Add this template to the list of views to check/generate — tagged as an **archive** template
4. Record the mounted collection handle (e.g., `posts`) — the archive template will need a `{{ collection:{handle} }}` listing tag

If the mount page entry has no `template` value, skip it (it uses the default pages template).

### Step 4: Scan Taxonomy Configs

For each root-level `.yaml` file in `content/taxonomies/` (not term files in subdirectories):

1. Read the taxonomy config file
2. Determine if the taxonomy has any public views — true if `layout` is present in the config. The convention is `layout: {taxonomy}/layout` — its presence signals that at least one view type is enabled.
3. Skip taxonomies with no `layout` (all view fields are `false` — no public pages)
4. Add the `layout` value to the views list

### Step 4b: Derive Taxonomy Views

Statamic supports 4 taxonomy view types controlled by schema fields (`has_index`, `has_show`, `has_collection_index`, `has_collection_show` — all default to `true`). Views auto-activate when the corresponding view files exist. Taxonomy configs typically do NOT contain `template` or `term_template` — views are resolved by naming convention.

If a schema file exists at `schemas/{taxonomy}.md`, read the view fields to determine which views to generate. If no schema exists, generate all applicable views.

For each taxonomy with public views:

1. **Taxonomy index view** — If `has_index` is `true` (or not specified): derive `{taxonomy}/index`. Add to the views list.
2. **Taxonomy show view** — If `has_show` is `true` (or not specified): derive `{taxonomy}/show`. Add to the views list.

For each collection config that lists a taxonomy in its `taxonomies` array:

3. **Collection-scoped taxonomy index** — If `has_collection_index` is `true` (or not specified): derive `{collection}/{taxonomy}/index`. Add to the views list.
4. **Collection-scoped term show** — If `has_collection_show` is `true` (or not specified): derive `{collection}/{taxonomy}/show`. Add to the views list.

Cross-reference collection configs (Step 2) with taxonomy configs (Step 4) to build the complete list.

### Step 5: Check View File Existence

For each template/layout value collected in Steps 2–4:

1. Check if `resources/views/{value}.antlers.html` exists
2. Check if `resources/views/{value}.blade.php` exists
3. If either exists, mark as **existing** and skip — do not overwrite
4. If neither exists, mark as **missing** and include in the generation list

Build the final list of missing view files to generate.

### Step 6: Read Blueprints

For each missing **template** view (not layouts — layouts don't need field data):

1. **Collection single templates** — Read `resources/blueprints/collections/{collection}/*.yaml` (all blueprints for the collection)
2. **Taxonomy templates** — Read `resources/blueprints/taxonomies/{taxonomy}/*.yaml` (all blueprints for the taxonomy)
3. **Archive/mount page templates** — Read the mount page entry's blueprint from `resources/blueprints/collections/pages/{blueprint}.yaml` (default to `default.yaml` if no `blueprint` field in the entry). Also note the mounted collection handle for the listing tag, and read the mounted collection's blueprints to identify common fields for entry cards (title, url, date, excerpt, featured_image, etc.)

For each blueprint:
- Parse all tabs and sections
- **Skip** fields with type `seo_pro` or `seo_pro_previews` and skip tabs named `SEO Meta` or `SEO Previews`
- Extract each field's `handle`, `type`, and notable properties (`max_files`, `buttons`, `sets`, `fields`, etc.)
- Resolve fieldset imports (`import: fieldset_handle`) by reading `resources/fieldsets/{handle}.yaml` — include imported fields with their prefix if specified
- Extract replicator sets and their sub-fields
- Extract grid columns and their sub-fields

When multiple blueprints exist for a collection/taxonomy, gather fields from ALL blueprints. Deduplicate by field handle (if the same handle appears in multiple blueprints, use the first occurrence).

### Step 7: Generate Template Boilerplates

For each missing template file, create `resources/views/{value}.antlers.html` with two parts:

**Part 1: Field documentation comment block** — An Antlers comment listing all available fields:
```antlers
{{#
  Template: {collection}/{template}
  Blueprint fields:

  {handle} ({type}) — {brief description or notable properties}
  {handle} ({type}) — {brief description or notable properties}
  ...
#}}
```

**Part 2: Functional HTML boilerplate** — Markup with all fields rendered using the appropriate pattern from the Field Type Mapping Reference (see below):
- Every field wrapped in `{{ if handle }}` conditionals
- The `title` field gets `<h1>` treatment
- Replicator fields rendered inline with `{{ if type == "set_name" }}` blocks for each set
- Grid fields rendered as loops with column access
- Group fields rendered with `{{ handle:sub_field }}` dot notation

**For archive/mount page templates**, generate:
- The mount page's own blueprint fields (title, etc.) rendered normally
- A `{{ collection:{mounted-collection-handle} }}` listing block that shows entry cards with common fields from the mounted collection's blueprint (title, url, date, excerpt/featured_image if available)

```antlers
{{ if title }}<h1>{{ title }}</h1>{{ /if }}

{{# Page content fields from the mount page blueprint #}}

{{ collection:{handle} limit="10" }}
  <article>
    <h2><a href="{{ url }}">{{ title }}</a></h2>
    {{ if date }}<time>{{ date format="F j, Y" }}</time>{{ /if }}
    {{ if excerpt }}<p>{{ excerpt }}</p>{{ /if }}
  </article>
{{ /collection:{handle} }}
```

**For taxonomy index templates** (`{taxonomy}/index`), generate:
- A listing of all terms in the taxonomy using the `{{ terms }}` tag pair

```antlers
{{ if title }}<h1>{{ title }}</h1>{{ /if }}

{{ terms taxonomy="{taxonomy}" }}
  <a href="{{ url }}">{{ title }}</a>
{{ /terms }}
```

**For taxonomy term templates** (`{taxonomy}/show`), generate:
- The term's own blueprint fields (title, description, etc.) rendered normally
- An `{{ entries }}` listing block that shows all entries tagged with this term (from all collections)

```antlers
{{ if title }}<h1>{{ title }}</h1>{{ /if }}

{{# Term blueprint fields #}}

{{ entries paginate="10" }}
  {{ results }}
    <article>
      <h2><a href="{{ url }}">{{ title }}</a></h2>
      {{ if date }}<time>{{ date format="F j, Y" }}</time>{{ /if }}
    </article>
  {{ /results }}

  {{ if total_pages > 1 }}
    <nav>
      {{ if prev_page }}<a href="{{ prev_page }}">Previous</a>{{ /if }}
      {{ if next_page }}<a href="{{ next_page }}">Next</a>{{ /if }}
    </nav>
  {{ /if }}
{{ /entries }}
```

**For collection-scoped taxonomy index templates** (`{collection}/{taxonomy}/index`), generate:
- A listing of terms associated with entries in that specific collection

```antlers
{{ if title }}<h1>{{ title }}</h1>{{ /if }}

{{ terms taxonomy="{taxonomy}" }}
  <a href="{{ url }}">{{ title }}</a>
{{ /terms }}
```

**For collection-scoped term templates** (`{collection}/{taxonomy}/show`), generate:
- The term's own blueprint fields rendered normally
- An `{{ entries }}` listing block (entries are automatically filtered to the collection in scope)

```antlers
{{ if title }}<h1>{{ title }}</h1>{{ /if }}

{{# Term blueprint fields #}}

{{ entries paginate="10" }}
  {{ results }}
    <article>
      <h2><a href="{{ url }}">{{ title }}</a></h2>
      {{ if date }}<time>{{ date format="F j, Y" }}</time>{{ /if }}
    </article>
  {{ /results }}

  {{ if total_pages > 1 }}
    <nav>
      {{ if prev_page }}<a href="{{ prev_page }}">Previous</a>{{ /if }}
      {{ if next_page }}<a href="{{ next_page }}">Next</a>{{ /if }}
    </nav>
  {{ /if }}
{{ /entries }}
```

### Step 8: Generate Layout Boilerplates

For each missing layout file, create a minimal `resources/views/{value}.antlers.html`:

```antlers
{{#
  Layout: {value}
  Extends the base layout with collection/taxonomy-specific structure.
  Field rendering belongs in the template, not here.
#}}

{{ partial:layout }}

{{ section:before_content }}
{{ partial:partials/header }}
{{ /section:before_content }}

{{ section:after_content }}
{{ partial:partials/footer }}
{{ /section:after_content }}
```

Layouts extend the base layout via `{{ partial:layout }}`. Sections inject content into the base layout's yield points. No `{{ template_content }}` is needed here — that lives in the base layout. Field rendering belongs in the template, not the layout.

### Step 9: Report Results

After generating all files, output a summary:

1. **Created files** — List each file path that was generated
2. **Skipped files** — List each file path that already existed (with which extension: `.antlers.html` or `.blade.php`)
3. **Collections/taxonomies with no template config** — List any that were skipped because they had no `template`/`layout` values

### Step 10: Check for Navigation

After reporting results, check whether any navigation exists in the project:

1. Check if `content/navigation/` directory exists and contains any `.yaml` files
2. If **no navigation exists**, ask the user whether they would like to create navigation — suggest using the `create-schema-navigation` skill (to define the navigation schema/config) and `create-navigations` skill (to create the navigation tree content)
3. If navigation already exists, skip this step silently

## Field Type → Antlers Mapping Reference

Use these patterns when generating template boilerplate. Every field MUST be wrapped in an `{{ if }}` conditional.

| Type | Pattern |
|------|---------|
| `text` (title) | `{{ if title }}<h1>{{ title }}</h1>{{ /if }}` |
| `text` (other) | `{{ if handle }}{{ handle }}{{ /if }}` |
| `textarea` | `{{ if handle }}<div>{{ handle \| nl2br }}</div>{{ /if }}` |
| `bard` | `{{ if handle }}<div class="prose">{{ handle }}</div>{{ /if }}` |
| `markdown` | `{{ if handle }}<div class="prose">{{ handle \| markdown }}</div>{{ /if }}` |
| `assets` (max_files: 1) | `{{ if handle }}<img src="{{ handle:url }}" alt="{{ handle:alt }}">{{ /if }}` |
| `assets` (multiple) | `{{ if handle }}{{ handle }}<img src="{{ url }}" alt="{{ alt }}">{{ /handle }}{{ /if }}` |
| `toggle` | `{{ if handle }}...{{ /if }}` |
| `date` | `{{ if handle }}<time>{{ handle format="F j, Y" }}</time>{{ /if }}` |
| `integer` / `float` | `{{ if handle }}{{ handle }}{{ /if }}` |
| `entries` | `{{ if handle }}{{ handle }}<a href="{{ url }}">{{ title }}</a>{{ /handle }}{{ /if }}` |
| `terms` | `{{ if handle }}{{ handle }}<a href="{{ url }}">{{ title }}</a>{{ /handle }}{{ /if }}` |
| `users` | `{{ if handle }}{{ handle }}{{ name }}{{ /handle }}{{ /if }}` |
| `select` / `radio` / `button_group` | `{{ if handle }}{{ handle }}{{ /if }}` |
| `color` | `{{ if handle }}{{ handle }}{{ /if }}` |
| `link` | `{{ if handle }}<a href="{{ handle }}">Link</a>{{ /if }}` |
| `replicator` | See inline set rendering below |
| `grid` | See loop rendering below |
| `group` | `{{ handle:sub_field }}` for each sub-field |
| `seo_pro` / `seo_pro_previews` | **SKIP — do not render** |

**Replicator inline rendering:**
```antlers
{{ if handle }}
  {{ handle }}
    {{ if type == "set_name" }}
      {{# Render set_name fields #}}
    {{ /if }}
    {{ if type == "another_set" }}
      {{# Render another_set fields #}}
    {{ /if }}
  {{ /handle }}
{{ /if }}
```

**Grid loop rendering:**
```antlers
{{ if handle }}
  {{ handle }}
    {{# Render column fields #}}
  {{ /handle }}
{{ /if }}
```

**Unknown field types:** Use `{{ if handle }}{{ handle }}{{ /if }}` with an Antlers comment noting the type: `{{# {handle}: unknown type "{type}" #}}`.

## Special Cases

- **Multiple blueprints per collection** — Generate fields from ALL blueprints (union of fields, deduplicated by handle). Add a comment noting which blueprint each field comes from if there are multiple.
- **Fieldset imports** — Resolve `import: {handle}` (and `import: {handle}` with `prefix: {prefix}`) by reading `resources/fieldsets/{handle}.yaml`. Include imported fields in the template with their prefixed handles if a prefix is specified.
- **Taxonomy view auto-resolution** — Taxonomy configs typically do NOT contain `template` or `term_template`. Statamic auto-resolves views by naming convention (`{taxonomy}/show`, `{taxonomy}/index`, `{collection}/{taxonomy}/show`, `{collection}/{taxonomy}/index`). This skill derives all 4 view paths from the taxonomy handle and its collection attachments. If `template` or `term_template` IS present in a config (set via CP), respect those values instead of the naming convention.
- **Mount page archive templates** — Read the mount page entry to get its `template` value. Generate an archive boilerplate that includes the page's own blueprint fields PLUS a `{{ collection:{mounted-collection-handle} }}` listing block. Resolve mount UUID by scanning page entries for matching `id`.
- **Subdirectories** — Create subdirectories under `resources/views/` as needed (e.g., `resources/views/posts/` for `posts/show`).

## Template Example

A generated template for `resources/views/posts/show.antlers.html` based on a blueprint with title, featured_image, content (bard), author (users), and categories (terms):

```antlers
{{#
  Template: posts/show
  Blueprint fields:

  title (text) — Entry title
  featured_image (assets) — max_files: 1
  content (bard) — Rich text content
  author (users) — Post author
  categories (terms) — taxonomies: categories
#}}

{{ if title }}<h1>{{ title }}</h1>{{ /if }}

{{ if featured_image }}
  <img src="{{ featured_image:url }}" alt="{{ featured_image:alt }}">
{{ /if }}

{{ if content }}
  <div class="prose">{{ content }}</div>
{{ /if }}

{{ if author }}
  {{ author }}
    {{ name }}
  {{ /author }}
{{ /if }}

{{ if categories }}
  {{ categories }}
    <a href="{{ url }}">{{ title }}</a>
  {{ /categories }}
{{ /if }}
```

## Archive Template Example

A generated archive template for `resources/views/posts/index.antlers.html` where the posts collection is mounted to a page:

```antlers
{{#
  Template: posts/index (archive)
  Mount page blueprint fields:

  title (text) — Page title
  content (bard) — Page introduction content

  Collection listing: posts
#}}

{{ if title }}<h1>{{ title }}</h1>{{ /if }}

{{ if content }}
  <div class="prose">{{ content }}</div>
{{ /if }}

{{ collection:posts limit="10" }}
  <article>
    <h2><a href="{{ url }}">{{ title }}</a></h2>
    {{ if date }}<time>{{ date format="F j, Y" }}</time>{{ /if }}
    {{ if featured_image }}
      <img src="{{ featured_image:url }}" alt="{{ featured_image:alt }}">
    {{ /if }}
    {{ if excerpt }}<p>{{ excerpt }}</p>{{ /if }}
  </article>
{{ /collection:posts }}
```

## Taxonomy Index Template Example

A generated taxonomy index template for `resources/views/categories/index.antlers.html`:

```antlers
{{#
  Template: categories/index
  Lists all terms in the categories taxonomy.
#}}

{{ if title }}<h1>{{ title }}</h1>{{ /if }}

{{ terms taxonomy="categories" }}
  <a href="{{ url }}">{{ title }}</a>
{{ /terms }}
```

## Collection-Scoped Taxonomy Index Template Example

A generated collection-scoped taxonomy index template for `resources/views/posts/categories/index.antlers.html`:

```antlers
{{#
  Template: posts/categories/index
  Lists categories associated with posts only.
#}}

{{ if title }}<h1>{{ title }}</h1>{{ /if }}

{{ terms taxonomy="categories" }}
  <a href="{{ url }}">{{ title }}</a>
{{ /terms }}
```

## Collection-Scoped Term Template Example

A generated collection-scoped term template for `resources/views/posts/categories/show.antlers.html`:

```antlers
{{#
  Template: posts/categories/show
  Blueprint fields:

  title (text) — Term title
  description (textarea) — Term description

  Entries: posts tagged with this term (filtered to posts collection)
#}}

{{ if title }}<h1>{{ title }}</h1>{{ /if }}

{{ if description }}
  <div>{{ description | nl2br }}</div>
{{ /if }}

{{ entries paginate="10" }}
  {{ results }}
    <article>
      <h2><a href="{{ url }}">{{ title }}</a></h2>
      {{ if date }}<time>{{ date format="F j, Y" }}</time>{{ /if }}
    </article>
  {{ /results }}

  {{ if total_pages > 1 }}
    <nav>
      {{ if prev_page }}<a href="{{ prev_page }}">Previous</a>{{ /if }}
      {{ if next_page }}<a href="{{ next_page }}">Next</a>{{ /if }}
    </nav>
  {{ /if }}
{{ /entries }}
```

## Taxonomy Term Template Example

A generated taxonomy term template for `resources/views/categories/show.antlers.html` based on a blueprint with title and description:

```antlers
{{#
  Template: categories/show
  Blueprint fields:

  title (text) — Term title
  description (textarea) — Term description

  Entries: lists all entries tagged with this term
#}}

{{ if title }}<h1>{{ title }}</h1>{{ /if }}

{{ if description }}
  <div>{{ description | nl2br }}</div>
{{ /if }}

{{ entries paginate="10" }}
  {{ results }}
    <article>
      <h2><a href="{{ url }}">{{ title }}</a></h2>
      {{ if date }}<time>{{ date format="F j, Y" }}</time>{{ /if }}
    </article>
  {{ /results }}

  {{ if total_pages > 1 }}
    <nav>
      {{ if prev_page }}<a href="{{ prev_page }}">Previous</a>{{ /if }}
      {{ if next_page }}<a href="{{ next_page }}">Next</a>{{ /if }}
    </nav>
  {{ /if }}
{{ /entries }}
```

## Layout Example

A generated layout for `resources/views/posts/layout.antlers.html`:

```antlers
{{#
  Layout: posts/layout
  Extends the base layout with posts-specific structure.
  Field rendering belongs in the template, not here.
#}}

{{ partial:layout }}

{{ section:before_content }}
{{ partial:partials/header }}
{{ /section:before_content }}

{{ section:after_content }}
{{ partial:partials/footer }}
{{ /section:after_content }}
```

## Rules

1. **Only create files in** `resources/views/`. No other directories or file types.
2. **Never overwrite existing view files.** If a `.antlers.html` or `.blade.php` file already exists at the target path, skip it and report it as existing.
3. **Never modify the base layout.** Do not touch `resources/views/layout.antlers.html` — use `create-page-templates` skill for layout work.
4. **Skip SEO fields.** Do not render fields with type `seo_pro` or `seo_pro_previews`. Do not render fields from tabs named `SEO Meta` or `SEO Previews`.
5. **Wrap all variables with conditionals.** Every field in the template must be inside an `{{ if handle }}` block. No unwrapped variables.
6. **Render replicator sets inline.** Use `{{ if type == "set_name" }}` blocks inside the replicator loop. Do not create separate set partial files.
7. **Resolve fieldset imports.** Read imported fieldsets from `resources/fieldsets/` and include their fields in the template output.
8. **Create subdirectories as needed.** If `resources/views/posts/` does not exist, create it when writing `resources/views/posts/show.antlers.html`.
9. **Use correct Antlers syntax.** Tag pairs for multi-value fields (`{{ handle }}...{{ /handle }}`), single tags for single-value fields, modifier pipe syntax for filters.
10. **Generate from all blueprints.** When a collection or taxonomy has multiple blueprints, include fields from all of them (deduplicated by handle).
11. **Do not modify collection configs, taxonomy configs, blueprints, entries, or any file outside `resources/views/`.** If changes are needed outside the allowed path, inform the user.
12. **Archive templates must include a listing tag.** Mount page templates must include a `{{ collection:{handle} }}` block for the mounted collection.
13. **Taxonomy term templates must include an `{{ entries }}` listing block.** Term show templates (both global and collection-scoped) must include an `{{ entries paginate="10" }}` block to list entries tagged with the term, with pagination support.
16. **Taxonomy index templates must include a `{{ terms }}` listing block.** Both global (`{taxonomy}/index`) and collection-scoped (`{collection}/{taxonomy}/index`) index templates must include a `{{ terms taxonomy="{taxonomy}" }}` block.
17. **Generate taxonomy views per schema fields.** Check `has_index`, `has_show`, `has_collection_index`, `has_collection_show` from the schema (all default to `true`). Only generate views for fields that are `true`. Collection-scoped views only apply when collections are attached.
14. **Always detect multisite in Step 1** before scanning for mount page entries, as page entry directories differ between single-site and multisite projects.
15. **Use the Field Type Mapping Reference** for all field type rendering. For unknown types, fall back to `{{ if handle }}{{ handle }}{{ /if }}` with a comment.

## Accuracy Checks

Before finishing, verify:
- [ ] All generated files are in `resources/views/` with `.antlers.html` extension
- [ ] No existing view files were overwritten
- [ ] Every `.yaml` file in `content/collections/` was scanned for `template`/`layout` values
- [ ] Every root-level `.yaml` file in `content/taxonomies/` was scanned for `layout` value (presence of `layout: {taxonomy}/layout` signals views are enabled)
- [ ] Mount page entries were scanned for `template` values (archive templates)
- [ ] All fields from blueprints are represented in generated templates (except SEO fields)
- [ ] Every rendered field is wrapped in an `{{ if }}` conditional
- [ ] Fieldset imports are resolved and their fields included in templates
- [ ] Replicator sets are rendered inline with type-checking blocks
- [ ] Archive templates include a `{{ collection:{handle} }}` listing block
- [ ] Taxonomy term templates include an `{{ entries paginate="10" }}` listing block with pagination
- [ ] Taxonomy index templates include a `{{ terms taxonomy="{taxonomy}" }}` listing block
- [ ] Collection-scoped taxonomy views are generated for each collection-taxonomy attachment
- [ ] Taxonomy views match schema view fields (`has_index`, `has_show`, `has_collection_index`, `has_collection_show` — all default to `true`)
- [ ] Layout boilerplates contain structural hooks only, no field rendering
- [ ] The base `resources/views/layout.antlers.html` was not modified
- [ ] No collection configs, taxonomy configs, blueprints, entries, or other out-of-scope files were created or edited
