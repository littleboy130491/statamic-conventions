# Complete Statamic File Structure Reference

## 1. ENTRIES (Collection Entries)

**Extension:** `.md`

**Purpose:** Pages, posts, articles - any content in a collection

**Path Pattern:**

- Single site: `content/collections/{collection}/{slug}.md`
- Single site (dated): `content/collections/{collection}/{date}.{slug}.md`
- Multisite: `content/collections/{collection}/{site}/{slug}.md`
- Multisite (dated): `content/collections/{collection}/{site}/{date}.{slug}.md`

**Structure:** YAML frontmatter + Markdown content

**Required Fields:**

- `id` — Unique identifier (UUID format)
- `title` — Entry title

**Common Fields:**

- `blueprint` — Which blueprint to use (defaults to collection's default)
- `published` — Boolean, whether entry is published
- `slug` — URL slug (if not using filename)
- `date` — Publication date (for dated collections)
- `author` — Reference to user(s)
- Custom fields defined in blueprint

**Content Area:** Markdown content below the frontmatter becomes the `content` field

**Reference:** https://statamic.dev/collections#entries

---

## 2. COLLECTIONS

**Extension:** `.yaml`

**Purpose:** Collection configuration

**Path Pattern:** `content/collections/{handle}.yaml`

**Required Fields:**

- `title` — Display name in Control Panel

**Common Fields:**

- `route` — URL pattern using variables like `{slug}`, `{parent_uri}`, `{mount}`
- `template` — Default template for entries
- `layout` — Default layout for entries
- `blueprints` — Array of available blueprint handles
- `sites` — Array of site handles (multisite only)
- `taxonomies` — Array of taxonomy handles to attach
- `date` — Boolean, enable dated entries
- `date_behavior` — Configure past/future visibility
- `sort_by` / `sort_dir` — Default sorting
- `orderable` — Boolean, enable manual ordering
- `structure` — Object with `root`, `max_depth` for hierarchical collections
- `mount` — Entry ID to mount collection under
- `revisions` — Boolean, enable revision history
- `inject` — Default values for all entries

**Reference:** https://statamic.dev/collections

---

## 3. BLUEPRINTS

**Extension:** `.yaml`

**Purpose:** Field definitions for content types

**Path Pattern:**

- Collections: `resources/blueprints/collections/{collection}/{handle}.yaml`
- Taxonomies: `resources/blueprints/taxonomies/{taxonomy}/{handle}.yaml`
- Globals: `resources/blueprints/globals/{handle}.yaml`
- Forms: `resources/blueprints/forms/{handle}.yaml`
- Assets: `resources/blueprints/assets/{handle}.yaml`
- Users: `resources/blueprints/user.yaml`

**Required Fields:**

- `title` — Display name

**Structure:**

- `tabs` — Object containing tab definitions
    - Each tab has `display` (label) and `sections`
    - Each section has `fields` array
    - Each field has `handle` and `field` (type, display, validation, etc.)

**Field Definition:**

- `handle` — Field identifier (used in templates)
- `field.type` — Fieldtype (text, textarea, markdown, bard, assets, etc.)
- `field.display` — Label in Control Panel
- `field.instructions` — Help text
- `field.validate` — Validation rules
- `field.required` — Boolean
- `field.localizable` — Boolean (multisite)

**Reference:** https://statamic.dev/blueprints

---

## 4. TAXONOMIES

**Extension:** `.yaml`

**Purpose:** Taxonomy configuration (categories, tags, etc.)

**Path Pattern:** `content/taxonomies/{handle}.yaml`

**Required Fields:**

- `title` — Display name

**Common Fields:**

- `layout` — Default layout for taxonomy views when public taxonomy routes are enabled
- `sites` — Array of site handles (multisite only)
- `revisions` — Boolean

Do not add `route`, `template`, or `term_template` for new taxonomy configs. Statamic 6 resolves taxonomy URLs and views by naming convention when the matching view files exist.

**Reference:** https://statamic.dev/taxonomies

---

## 5. TERMS (Taxonomy Terms)

**Extension:** `.yaml`

**Purpose:** Individual taxonomy terms

**Path Pattern:**

- Single site: `content/taxonomies/{taxonomy}/{slug}.yaml`
- Multisite: `content/taxonomies/{taxonomy}/{site}/{slug}.yaml`

**Required Fields:**

- `title` — Term title

**Common Fields:**

- `id` — Unique identifier
- `slug` — URL slug
- Custom fields defined in taxonomy blueprint

**Reference:** https://statamic.dev/taxonomies#terms

---

## 6. COLLECTION TREES (Structure)

**Extension:** `.yaml`

**Purpose:** Page hierarchy/order for structured collections

**Path Pattern:**

- Single site: `content/trees/collections/{collection}.yaml`
- Multisite: `content/trees/collections/{site}/{collection}.yaml`

**Structure:**

- `tree` — Array of tree nodes
    - Each node has `entry` (entry ID)
    - Optional `children` array for nested entries

**Reference:** https://statamic.dev/structures

---

## 7. NAVIGATION

**Extension:** `.yaml`

**Purpose:** Navigation menu configuration

**Path Pattern:** `content/navigation/{handle}.yaml`

**Required Fields:**

- `title` — Display name

**Common Fields:**

- `max_depth` — Maximum nesting level
- `collections` — Array of collection handles to select entries from
- `sites` — Array of site handles (multisite only)

**Reference:** https://statamic.dev/navigation

---

## 8. NAVIGATION TREES

**Extension:** `.yaml`

**Purpose:** Navigation menu items

**Path Pattern:**

- Single site: `content/trees/navigation/{nav}.yaml`
- Multisite: `content/navigation/{site}/{nav}.yaml`

**Structure:**

- `tree` — Array of nav items
    - `id` — Unique identifier for the nav item
    - `entry` — Entry ID (for entry references)
    - `url` — Hardcoded URL (for custom links)
    - `title` — Display title (required for non-entry items)
    - `children` — Array for nested items

**Reference:** https://statamic.dev/navigation

---

## 9. GLOBALS

**Extension:** `.yaml`

**Purpose:** Site-wide variables and settings

**Path Pattern:**

- Single site: `content/globals/{handle}.yaml`
- Multisite (config): `content/globals/{handle}.yaml`
- Multisite (data): `content/globals/{site}/{handle}.yaml`

**Single Site Structure:**

- `title` — Display name
- `data` — Object containing all global variables

**Multisite Structure:**

- Config file contains `title` only
- Site-specific files contain data directly (no `data` wrapper)

**Reference:** https://statamic.dev/globals

---

## 10. FORMS

**Extension:** `.yaml`

**Purpose:** Form configuration

**Path Pattern:**

- Config: `resources/forms/{handle}.yaml`
- Blueprint: `resources/blueprints/forms/{handle}.yaml`

**Required Fields:**

- `title` — Display name

**Common Fields:**

- `honeypot` — Honeypot field name for spam protection
- `store` — Boolean, whether to store submissions
- `email` — Array of email configurations
    - `to` — Recipient email
    - `from` — Sender email (can use form variables)
    - `subject` — Email subject
    - `template` — Email template path

**Reference:** https://statamic.dev/forms

---

## 11. FORM SUBMISSIONS

**Extension:** `.yaml`

**Purpose:** Stored form submissions

**Path Pattern:** `storage/forms/{form}/{timestamp}.yaml`

**Structure:** Contains all submitted field values plus metadata

- `id` — Submission ID
- All form field handles with their submitted values

**Note:** Automatically generated by Statamic when forms are submitted

**Reference:** https://statamic.dev/forms#submissions

---

## 12. ASSET CONTAINERS

**Extension:** `.yaml`

**Purpose:** Asset container configuration

**Path Pattern:** `content/assets/{handle}.yaml`

**Required Fields:**

- `title` — Display name
- `disk` — Filesystem disk name (from Laravel config)

**Common Fields:**

- `allow_uploads` — Boolean
- `allow_downloading` — Boolean
- `allow_moving` — Boolean
- `allow_renaming` — Boolean
- `create_folders` — Boolean
- `source_preset` — Glide preset for source images
- `warm_presets` — Array of presets to generate on upload

**Reference:** https://statamic.dev/assets

---

## 13. USERS

**Extension:** `.yaml`

**Purpose:** User accounts

**Path Pattern:** `users/{email}.yaml`

**Required Fields:**

- `id` — Unique identifier
- `name` — Display name

**Common Fields:**

- `email` — Email address (also in filename)
- `password` — Hashed password
- `super` — Boolean, super admin status
- `roles` — Array of role handles
- `groups` — Array of group handles
- `preferences` — User preferences object
- Custom fields from user blueprint

**Reference:** https://statamic.dev/users

---

## 14. ROLES

**Extension:** `.yaml`

**Purpose:** Permission roles

**Path Pattern:** `resources/users/roles.yaml`

**Structure:** Object keyed by role handle

- Each role has `title` and `permissions` array
- Permissions are strings matching Statamic permission names

**Reference:** https://statamic.dev/users#roles

---

## 15. USER GROUPS

**Extension:** `.yaml`

**Purpose:** User groups for bulk role assignment

**Path Pattern:** `resources/users/groups.yaml`

**Structure:** Object keyed by group handle

- Each group has `title` and `roles` array

**Reference:** https://statamic.dev/users#groups

---

## 16. SITES CONFIG

**Extension:** `.yaml`

**Purpose:** Multi-site configuration

**Path Pattern:** `resources/sites.yaml`

**Structure:** Object keyed by site handle

- `name` — Display name
- `url` — Site URL (absolute or relative)
- `locale` — Locale code (e.g., `en_US`, `id_ID`)
- `lang` — Language code (e.g., `en`, `id`)
- `attributes` — Custom attributes accessible in templates

**Reference:** https://statamic.dev/multi-site

---

## 17. FIELDSETS

**Extension:** `.yaml`

**Purpose:** Reusable field groups for blueprints

**Path Pattern:** `resources/fieldsets/{handle}.yaml`

**Required Fields:**

- `title` — Display name

**Structure:**

- `fields` — Array of field definitions (same format as blueprint fields)

**Usage:** Import into blueprints with `import: {fieldset_handle}`

**Reference:** https://statamic.dev/fieldsets

---

## Summary Table

| Entity | Extension | Path Pattern | Multisite Path |
| --- | --- | --- | --- |
| Entries | `.md` | `content/collections/{collection}/{slug}.md` | `.../{collection}/{site}/{slug}.md` |
| Collections | `.yaml` | `content/collections/{handle}.yaml` | — |
| Blueprints | `.yaml` | `resources/blueprints/{namespace}/{handle}.yaml` | — |
| Taxonomies | `.yaml` | `content/taxonomies/{handle}.yaml` | — |
| Terms | `.yaml` | `content/taxonomies/{taxonomy}/{slug}.yaml` | `.../{taxonomy}/{site}/{slug}.yaml` |
| Collection Trees | `.yaml` | `content/trees/collections/{collection}.yaml` | `.../{site}/{collection}.yaml` |
| Navigation | `.yaml` | `content/navigation/{handle}.yaml` | — |
| Nav Trees | `.yaml` | `content/trees/navigation/{nav}.yaml` | `content/navigation/{site}/{nav}.yaml` |
| Globals (config) | `.yaml` | `content/globals/{handle}.yaml` | — |
| Globals (data) | `.yaml` | (under `data:` key in config) | `content/globals/{site}/{handle}.yaml` |
| Forms | `.yaml` | `resources/forms/{handle}.yaml` | — |
| Form Blueprints | `.yaml` | `resources/blueprints/forms/{handle}.yaml` | — |
| Submissions | `.yaml` | `storage/forms/{form}/{timestamp}.yaml` | — |
| Asset Containers | `.yaml` | `content/assets/{handle}.yaml` | — |
| Users | `.yaml` | `users/{email}.yaml` | — |
| Roles | `.yaml` | `resources/users/roles.yaml` | — |
| Groups | `.yaml` | `resources/users/groups.yaml` | — |
| Sites | `.yaml` | `resources/sites.yaml` | — |
| Fieldsets | `.yaml` | `resources/fieldsets/{handle}.yaml` | — |

---

## Multisite Content Organization

When multisite is enabled, content that varies per site is organized into site-handle subdirectories:

**Affected paths:**

- `content/collections/{collection}/{site}/`
- `content/taxonomies/{taxonomy}/{site}/`
- `content/globals/{site}/`
- `content/trees/collections/{site}/`
- `content/navigation/{site}/`

**Shared across sites (no site subdirectory):**

- Collection config (`content/collections/{handle}.yaml`)
- Taxonomy config (`content/taxonomies/{handle}.yaml`)
- Global config (`content/globals/{handle}.yaml`)
- Navigation config (`content/navigation/{handle}.yaml`)
- All blueprints (`resources/blueprints/`)
- All fieldsets (`resources/fieldsets/`)
- Sites config (`resources/sites.yaml`)
- Forms (`resources/forms/`)
- Users (`users/`)
- Roles & Groups (`resources/users/`)

**Reference:** https://statamic.dev/multi-site
