# Statamic Conventions — LLM Instructions

## Overview

You have access to Statamic CMS conventions documentation. Before implementing any Statamic-related task, consult the relevant documentation to ensure correct file locations, naming conventions, and patterns.

**Documentation Location:** `/statamic-conventions/`

> ⚠️ **CRITICAL FIRST STEP:** Before ANY Statamic task, detect if the project is **single-site or multisite** by checking `resources/sites.yaml` (or `config/statamic/sites.php`). File paths differ significantly between the two configurations. See "Multisite Detection" section below.
> 

---

## Multisite Detection (CHECK FIRST)

**Before implementing ANY Statamic task, determine if the project is multisite.**

### How to Detect Multisite

**Check these files in order:**

1. `resources/sites.yaml` (flat-file configuration — check first)
2. `config/statamic/sites.php` (PHP configuration — fallback)

---

### Option 1: YAML Configuration (Preferred)

**File:** `resources/sites.yaml`

**Single Site Indicators:**

```yaml
default:
  name: Site Name
  locale: en_US
  url: /

```

- Only ONE site key defined
- No additional site handles

**Multisite Indicators:**

```yaml
english:
  name: English
  locale: en_US
  url: /
indonesian:
  name: Indonesian
  locale: id_ID
  url: /id/

```

- TWO OR MORE site keys defined
- Multiple site handles (e.g., `english`, `indonesian`)

---

### Option 2: PHP Configuration (Fallback)

**File:** `config/statamic/sites.php`

**Single Site Indicators:**

```php
'sites' => [
    'default' => [
        'name' => 'Site Name',
        'locale' => 'en_US',
        'url' => '/',
    ],
],

```

- Only ONE site defined
- No additional site handles

**Multisite Indicators:**

```php
'sites' => [
    'english' => [
        'name' => 'English',
        'locale' => 'en_US',
        'url' => '/',
    ],
    'indonesian' => [
        'name' => 'Indonesian',
        'locale' => 'id_ID',
        'url' => '/id/',
    ],
],

```

- TWO OR MORE sites defined
- Multiple site handles (e.g., `english`, `indonesian`)

### Alternative Detection Methods

**Check content folder structure:**

```bash
# If you see site subfolders, it's multisite:
content/collections/pages/english/
content/collections/pages/indonesian/

# If entries are directly in collection folder, it's single site:
content/collections/pages/home.md
content/collections/pages/about.md

```

**Check trees folder:**

```bash
# Multisite:
content/trees/collections/pages/english.yaml
content/trees/collections/pages/indonesian.yaml

# Single site:
content/trees/collections/pages.yaml

```

**Check globals folder:**

```bash
# Multisite (has site subfolders for data):
content/globals/site_settings.yaml          # Config only
content/globals/english/site_settings.yaml  # English data
content/globals/indonesian/site_settings.yaml  # Indonesian data

# Single site (data in same file):
content/globals/site_settings.yaml          # Config + data with data: key

```

---

### Path Differences Summary

| Content Type | Single Site Path | Multisite Path |
| --- | --- | --- |
| Entries | `content/collections/{collection}/{entry}.md` | `content/collections/{collection}/{site}/{entry}.md` |
| Collection Trees | `content/trees/collections/{collection}.yaml` | `content/trees/collections/{collection}/{site}.yaml` |
| Taxonomy Terms | `content/taxonomies/{taxonomy}/{term}.yaml` | `content/taxonomies/{taxonomy}/{site}/{term}.yaml` |
| Navigation Trees | `content/trees/navigation/{nav}.yaml` | `content/trees/navigation/{site}/{nav}.yaml` |
| Global Data | `content/globals/{global}.yaml` (with `data:` key) | `content/globals/{site}/{global}.yaml` (no `data:` key) |

**Paths that DON'T change (shared across sites):**

- `resources/blueprints/*` — All blueprints
- `resources/fieldsets/*` — All fieldsets
- `resources/forms/*` — Form configurations
- `content/collections/{collection}.yaml` — Collection config
- `content/taxonomies/{taxonomy}.yaml` — Taxonomy config
- `content/navigation/{nav}.yaml` — Navigation config
- `content/globals/{global}.yaml` — Global config (multisite: config only)

---

### Multisite Detection Checklist

Before proceeding with any task:

- [ ]  Check `resources/sites.yaml` for site definitions (primary)
- [ ]  If not found, check `config/statamic/sites.php` (fallback)
- [ ]  Count the number of sites defined
- [ ]  If multisite: note the site handles (e.g., `english`, `indonesian`, `default`)
- [ ]  Verify by checking `content/collections/` for site subfolders
- [ ]  Use correct paths based on single-site or multisite structure

---

### Quick Detection Command

If you have terminal access:

```bash
# Check sites config (YAML first, then PHP fallback)
cat resources/sites.yaml
# OR
cat config/statamic/sites.php

# Or check for site subfolders in content
ls content/collections/pages/

# If output shows .md files = single site
# If output shows folders (english/, indonesian/) = multisite

```

---

## Available Documentation

| Document | File | Covers |
| --- | --- | --- |
| File Structure | `file_structure.md` | File paths, folder organization, naming conventions |
| Extending Layouts | `extending_layouts.md` | Layout hooks, contextual body classes, template extension, SEO Pro integration |
| Static Pages | `static_pages.md` | Pages collection, page templates, page hierarchy |
| Collections | `collections.md` | Content collections (blog, products, etc.), routing, mounting |
| Taxonomies | `taxonomies.md` | Categories, tags, terms, taxonomy relationships |
| Globals | `globals.md` | Site-wide settings, reusable content blocks |
| Blueprints & Fields | `blueprints_fields.md` | Fieldtypes, validation, conditional fields, fieldsets |
| Navigation | `navigations.md` | Menus, nav trees, breadcrumbs, active states |
| Forms | `forms.md` | Contact forms, submissions, email notifications |
| Static Pages CMS Structure | `static_pages_cms_fields_structure.md` | Page-specific blueprints vs flexible page builder approaches |
| HTML to Antlers | `html_to_antlers_instruction.md` | Converting static HTML templates to dynamic Antlers |

---

## Task-to-Documentation Mapping

### Always Start With

**For ANY Statamic task:** First check `file_structure.md` to confirm correct file paths and naming conventions.

---

### File Structure (`file_structure.md`)

**Consult when:**

- Unsure where a file should be located
- Creating new content types or blueprints
- Setting up multisite structure
- Need to understand folder organization
- Checking single-site vs multisite paths

**Trigger phrases:**

- "where does this file go"
- "what's the path for"
- "file location"
- "folder structure"
- "multisite paths"
- "how is content organized"

---

### Extending Layouts (`extending_layouts.md`)

**Consult when:**

- Working with layout templates (`resources/views/layout.antlers.html`)
- Adding custom styles, scripts, or head content to pages
- Customizing body classes for styling hooks
- Implementing header, footer, or navigation in templates
- Understanding contextual body classes (WordPress-style)
- Using SEO Pro meta tags
- Overriding default wrapper or body styling
- Adding analytics or tracking code

**Trigger phrases:**

- "layout"
- "base template"
- "extend layout"
- "body class"
- "contextual class"
- "add script to page"
- "add CSS to page"
- "header template"
- "footer template"
- "SEO tags"
- "meta tags"
- "template hooks"
- "yield section"
- "WordPress body_class"
- "analytics code"
- "Google Tag Manager"

**Key Features:**

- **Contextual Body Classes** - Automatic WordPress-style classes (entry-{collection}, slug-{slug}, homepage, etc.)
- **Template Hooks** - 10+ injection points (head, styles, scripts, before/after content, etc.)
- **SEO Pro Integration** - Automatic meta tags, OG tags, JSON-LD
- **Native Statamic Only** - Uses only `{{ yield }}/{{ section }}` (no addons required)

**Often combined with:**

- `static_pages.md` — for page templates extending the layout
- `html_to_antlers_instruction.md` — for template conversion
- `file_structure.md` — for template file locations

---

### Static Pages (`static_pages.md`)

**Consult when:**

- Creating or editing pages
- Setting up page hierarchy (parent/child)
- Configuring page templates
- Working with the pages collection
- Setting up page-specific partials

**Trigger phrases:**

- "create a page"
- "page template"
- "homepage"
- "about page"
- "page hierarchy"
- "parent page"
- "child page"
- "page tree"
- "landing page"

**Often combined with:**

- `extending_layouts.md` — for template structure and hooks
- `blueprints_fields.md` — for page field configuration
- `static_pages_cms_fields_structure.md` — for choosing blueprint approach
- `html_to_antlers_instruction.md` — for converting HTML to templates
- `globals.md` — for site-wide content on pages
- `navigations.md` — for page navigation

---

### Static Pages CMS Structure (`static_pages_cms_fields_structure.md`)

**Consult when:**

- Deciding between page-specific blueprints vs flexible page builder
- Creating unique page layouts (About, Contact, Services)
- Setting up Replicator-based page builders
- Planning CMS field architecture for static pages

**Trigger phrases:**

- "page blueprint"
- "page builder"
- "flexible layout"
- "page-specific fields"
- "replicator sections"
- "unique page layout"
- "about page fields"
- "homepage sections"

**Often combined with:**

- `extending_layouts.md` — for layout template structure
- `static_pages.md` — for page structure
- `blueprints_fields.md` — for field configuration details
- `html_to_antlers_instruction.md` — for template conversion

---

### HTML to Antlers (`html_to_antlers_instruction.md`)

**Consult when:**

- Converting static HTML templates to dynamic Antlers
- Extracting content from HTML files into CMS entries
- Wrapping HTML with Statamic variables
- Creating Antlers templates from design files

**Trigger phrases:**

- "convert HTML"
- "HTML to Antlers"
- "template conversion"
- "static to dynamic"
- "extract content"
- "wrap variables"
- "make template dynamic"

**Often combined with:**

- `extending_layouts.md` — for layout hooks and structure
- `static_pages.md` — for page template structure
- `static_pages_cms_fields_structure.md` — for field planning
- `blueprints_fields.md` — for field types and configuration

---

### Collections (`collections.md`)

**Consult when:**

- Creating blog, news, or article sections
- Setting up products, services, or portfolio items
- Configuring collection routing
- Mounting collections to pages
- Working with dated or ordered content

**Trigger phrases:**

- "create a collection"
- "blog posts"
- "articles"
- "products"
- "portfolio"
- "case studies"
- "team members"
- "services"
- "collection route"
- "mount collection"
- "dated entries"
- "orderable entries"

**Often combined with:**

- `taxonomies.md` — for categorizing collection entries
- `blueprints_fields.md` — for entry field configuration
- `static_pages.md` — for mounting collections to pages

---

### Taxonomies (`taxonomies.md`)

**Consult when:**

- Creating categories or tags
- Organizing content by type/topic
- Setting up filtering or faceted navigation
- Creating author or contributor systems
- Linking related content

**Trigger phrases:**

- "categories"
- "tags"
- "taxonomy"
- "filter by"
- "group by"
- "content classification"
- "authors"
- "topics"
- "related posts"
- "term page"

**Often combined with:**

- `collections.md` — taxonomies attach to collections
- `blueprints_fields.md` — for term field configuration

---

### Globals (`globals.md`)

**Consult when:**

- Creating site-wide settings
- Setting up footer content
- Managing social media links
- Creating reusable content blocks (testimonials, CTAs)
- Configuring site metadata

**Trigger phrases:**

- "site settings"
- "global"
- "footer content"
- "social links"
- "site-wide"
- "company info"
- "contact details"
- "reusable content"
- "testimonials block"
- "CTA section"
- "shared content"

**Often combined with:**

- `blueprints_fields.md` — for global field configuration
- `static_pages.md` — globals often used in page templates

---

### Blueprints & Fields (`blueprints_fields.md`)

**Consult when:**

- Adding fields to any content type
- Choosing the right fieldtype
- Setting up validation rules
- Creating conditional fields
- Building reusable fieldsets
- Configuring Bard or Replicator

**Trigger phrases:**

- "add a field"
- "fieldtype"
- "blueprint"
- "validation"
- "required field"
- "conditional field"
- "fieldset"
- "Bard editor"
- "Replicator"
- "page builder"
- "flexible content"
- "field options"
- "select field"
- "relationship field"
- "entries field"

**Often combined with:**

- Any other document — blueprints define fields for all content types

---

### Navigation (`navigations.md`)

**Consult when:**

- Creating header or footer menus
- Setting up sidebar navigation
- Implementing breadcrumbs
- Working with dropdown menus
- Adding active states to nav items
- Using collection structure as navigation

**Trigger phrases:**

- "navigation"
- "menu"
- "nav"
- "header menu"
- "footer links"
- "sidebar nav"
- "breadcrumbs"
- "dropdown"
- "active state"
- "current page"
- "nav tree"
- "mobile menu"

**Often combined with:**

- `static_pages.md` — navigation often mirrors page structure
- `blueprints_fields.md` — for custom nav item fields

---

### Forms (`forms.md`)

**Consult when:**

- Creating contact forms
- Setting up email notifications
- Handling form submissions
- Implementing spam protection
- Building quote request or application forms
- Displaying form data on frontend

**Trigger phrases:**

- "form"
- "contact form"
- "email notification"
- "form submission"
- "newsletter signup"
- "quote request"
- "job application"
- "form validation"
- "honeypot"
- "spam protection"
- "file upload form"
- "AJAX form"

**Often combined with:**

- `blueprints_fields.md` — forms use blueprint fields
- `file_structure.md` — for email template locations

---

## Multi-Document Tasks

Some tasks require consulting multiple documents. Here are common scenarios:

**Note:** For ALL tasks below, first detect if project is single-site or multisite by checking `resources/sites.yaml` (or `config/statamic/sites.php`). This determines which paths to use.

### Creating a Blog

1. **Detect multisite** — Check `resources/sites.yaml`
2. `collections.md` — Set up blog collection
3. `taxonomies.md` — Create categories and tags
4. `blueprints_fields.md` — Define post fields
5. `static_pages.md` — Create blog index page (mount point)
6. `navigations.md` — Add blog to navigation

### Building a Services Section

1. **Detect multisite** — Check `resources/sites.yaml`
2. `collections.md` — Set up services collection
3. `blueprints_fields.md` — Define service fields (Replicator for flexible content)
4. `static_pages.md` — Create services landing page
5. `globals.md` — CTA block for service pages

### Setting Up a Contact Page

1. **Detect multisite** — Check `resources/sites.yaml`
2. `static_pages.md` — Create contact page
3. `forms.md` — Build contact form
4. `globals.md` — Company contact details
5. `blueprints_fields.md` — Form field configuration

### Creating a Portfolio

1. **Detect multisite** — Check `resources/sites.yaml`
2. `collections.md` — Set up portfolio/projects collection
3. `taxonomies.md` — Project categories or industries
4. `blueprints_fields.md` — Project fields (gallery, details)
5. `navigations.md` — Portfolio navigation/filtering

### Working with Existing Multisite Project

1. `file_structure.md` — Multisite folder structure (CRITICAL)
2. Note site handles from `resources/sites.yaml`
3. All other docs — Use multisite paths shown in each document

### Converting HTML Design to Statamic

1. **Detect multisite** — Check `resources/sites.yaml`
2. `html_to_antlers_instruction.md` — Conversion workflow
3. `extending_layouts.md` — Layout structure and hooks
4. `static_pages_cms_fields_structure.md` — Choose blueprint approach
5. `blueprints_fields.md` — Define fields
6. `static_pages.md` — Create pages and templates
7. Update entry `.md` files with extracted content
8. Create `.antlers.html` templates with wrapped variables

---

## Decision Tree

```
START: Any Statamic Task

┌─ STEP 0: Detect site type (ALWAYS DO THIS FIRST)
│  └─ Check resources/sites.yaml (or config/statamic/sites.php)
│     ├─ One site defined → SINGLE SITE (use direct paths)
│     └─ Multiple sites → MULTISITE (use {site}/ subfolders)
│
└─ STEP 1: What are you building?

   ├─ A page (single, standalone content)
   │  └─ Check: static_pages.md → extending_layouts.md → static_pages_cms_fields_structure.md → blueprints_fields.md
   │
   ├─ Converting HTML to Antlers
   │  └─ Check: html_to_antlers_instruction.md → extending_layouts.md → static_pages_cms_fields_structure.md → blueprints_fields.md
   │
   ├─ Layout/template modifications (header, footer, scripts, styles)
   │  └─ Check: extending_layouts.md
   │
   ├─ Multiple similar items (posts, products, team)
   │  └─ Check: collections.md → blueprints_fields.md
   │  │
   │  └─ Need to categorize them?
   │     └─ Check: taxonomies.md
   │
   ├─ Site-wide settings or reusable blocks
   │  └─ Check: globals.md → blueprints_fields.md
   │
   ├─ Menu or navigation
   │  └─ Check: navigations.md
   │
   ├─ Contact form or user input
   │  └─ Check: forms.md → blueprints_fields.md
   │
   ├─ Unsure about file location
   │  └─ Check: file_structure.md FIRST
   │
   └─ Field configuration for anything
      └─ Check: blueprints_fields.md

```

---

## Quick Reference by File Type

| Creating This | Check These Docs |
| --- | --- |
| `resources/sites.yaml` | file_structure.md (multisite detection) |
| `.yaml` in `content/collections/` | collections.md, file_structure.md |
| `.md` entry file | collections.md, static_pages.md |
| `.yaml` in `resources/blueprints/` | blueprints_fields.md, static_pages_cms_fields_structure.md |
| `.yaml` in `content/taxonomies/` | taxonomies.md |
| `.yaml` in `content/globals/` | globals.md |
| `.yaml` in `content/navigation/` | navigations.md |
| `.yaml` in `content/trees/` | navigations.md, static_pages.md |
| `.yaml` in `resources/forms/` | forms.md |
| `.antlers.html` template | extending_layouts.md, html_to_antlers_instruction.md, relevant content doc + file_structure.md |
| `layout.antlers.html` | extending_layouts.md |
| `partials/*.antlers.html` | extending_layouts.md, relevant content doc |
| Email template | forms.md |
| Converting HTML files | html_to_antlers_instruction.md, static_pages_cms_fields_structure.md |

---

## Before Implementation Checklist

Before writing any Statamic code or configuration:

- [ ]  **FIRST: Detected single-site or multisite** (check `resources/sites.yaml` or `config/statamic/sites.php`)
- [ ]  If multisite: noted site handles for correct paths
- [ ]  Identified the correct document(s) to consult
- [ ]  Confirmed file paths in `file_structure.md` (using correct single/multisite paths)
- [ ]  Checked naming conventions (handles, filenames)
- [ ]  Reviewed blueprint/field requirements in `blueprints_fields.md`
- [ ]  Noted any related documents for complete implementation

---

## Example Queries and Responses

**Query:** "Create a team members section"

**Action:**

1. **Detect multisite first** — Check `resources/sites.yaml` (or `config/statamic/sites.php`)
2. Read `collections.md` — for setting up team collection
3. Read `blueprints_fields.md` — for member fields (photo, bio, social links)
4. Check `file_structure.md` — confirm paths (use multisite paths if applicable)

---

**Query:** "Add categories to blog posts"

**Action:**

1. **Detect multisite first** — Check `resources/sites.yaml` (or `config/statamic/sites.php`)
2. Read `taxonomies.md` — for creating categories taxonomy
3. Read `collections.md` — for attaching taxonomy to collection
4. Check `blueprints_fields.md` — for terms field configuration

---

**Query:** "Set up the main navigation with dropdowns"

**Action:**

1. **Detect multisite first** — Check `resources/sites.yaml` (or `config/statamic/sites.php`)
2. Read `navigations.md` — for nav configuration and templates
3. Check `file_structure.md` — for nav file locations (tree path differs in multisite)

---

**Query:** "Create a quote request form with file uploads"

**Action:**

1. Read `forms.md` — for form setup, file uploads, email config (forms don't differ in multisite)
2. Read `blueprints_fields.md` — for assets field configuration
3. Check `file_structure.md` — for form and email template paths

---

**Query:** "Create a new page called Services"

**Action:**

1. **Detect multisite first** — Check `resources/sites.yaml` (or `config/statamic/sites.php`)
    - Single site: Create `content/collections/pages/services.md`
    - Multisite: Create `content/collections/pages/{site}/services.md` for each site
2. Read `static_pages.md` — for page structure and templates
3. Read `static_pages_cms_fields_structure.md` — for choosing blueprint approach
4. Read `blueprints_fields.md` — for page fields

---

**Query:** "Convert this HTML design to Statamic"

**Action:**

1. **Detect multisite first** — Check `resources/sites.yaml` (or `config/statamic/sites.php`)
2. Read `html_to_antlers_instruction.md` — for conversion workflow
3. Read `static_pages_cms_fields_structure.md` — to decide blueprint strategy
4. Extract content from HTML, update entry `.md` files
5. Create `.antlers.html` templates with conditionally-wrapped variables

---

## Important Conventions to Remember

1. **ALWAYS detect multisite first** — Check `resources/sites.yaml` (or `config/statamic/sites.php`) before doing anything; paths differ significantly
2. **Blueprint handles must match content handles** — Form blueprint handle = form handle, nav blueprint handle = nav handle
3. **Partials use underscore prefix** — Filename: `_name.antlers.html`, reference: `{{ partial:name }}`
4. **Multisite changes content paths** — Content goes in `{site}/` subdirectories, config files stay shared
5. **Globals single vs multisite differ** — Single site has `data:` wrapper, multisite site files don't
6. **Collections can be mounted** — Creating URL hierarchy by mounting to pages
7. **Taxonomies attach to collections** — Configure in collection yaml, not taxonomy yaml
8. **ALWAYS use SEO Pro addon for SEO** — Never create custom SEO fields (meta title, meta description, OG tags, etc.). Use the SEO Pro Statamic addon for all SEO-related functionality
9. **Contextual body classes are automatic** — The layout adds WordPress-style classes (entry-{collection}, slug-{slug}, etc.) automatically; use these for CSS targeting
10. **Use native Statamic tags only** — Layout uses `{{ yield }}/{{ section }}` (native); `{{ push }}/{{ stack }}` require addons
11. **Layout hooks pattern** — Use `{{ section:name }}...{{ /section:name }}` in templates, `{{ yield:name }}` in layout; sections don't accumulate (last wins)

---

## When Documentation Conflicts with Memory

If you have prior knowledge that conflicts with these conventions:

1. **Trust the documentation** — It reflects project-specific standards
2. **Check file_structure.md first** — Paths may differ from Statamic defaults
3. **Follow the patterns shown** — Consistency matters more than alternatives
