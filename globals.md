# Statamic Conventions: Globals

## Global Set (Configuration)

**Location:** `content/globals/{handle}.yaml`

**Purpose:** Defines a set of site-wide variables accessible from any template.

**Controls:**

- Global variables and their values
- Localization (sites)

**Required Fields:**

- `title` — Display name in Control Panel (e.g., "Site Settings", "Footer")

**Single Site Structure:**

- `title` — Display name
- `data` — Object containing all global variables

**Multisite Structure:**

- Config file contains `title` and `sites` array only
- Data stored separately per site

**Reference:** https://statamic.dev/globals

**Mental model:** Global set groups related site-wide variables together.

---

## Global Blueprint

**Location:** `resources/blueprints/globals/{handle}.yaml`

**Purpose:** Defines what fields the global set has and how the editor UI looks.

**Controls:**

- Fields available in the global set
- Field types
- Tabs & sections in Control Panel
- Validation rules

**Structure:**

- `title` — Blueprint display name
- `tabs` — Object containing tab definitions
    - Each tab has `display` and `sections`
    - Each section has `fields` array
    - Each field has `handle` and `field` configuration

**Notes:**

- Blueprint is optional — without it, Statamic treats all YAML keys as text fields
- Create blueprint when you need specific fieldtypes, validation, or organized UI
- Blueprint handle must match global set handle

**Reference:** https://statamic.dev/blueprints

**Mental model:** Blueprint defines what fields the global set contains.

---

## Global Data (Single Site)

**Location:** `content/globals/{handle}.yaml`

**Purpose:** Stores the global variable values.

**Structure:**

```yaml
title: Site Settings
data:
  site_name: "My Website"
  logo: /assets/logo.png
  social_links:
    - platform: twitter
      url: https://twitter.com/example

```

**Key rules:**

- All data lives under the `data` key
- Field handles must match blueprint (if using one)
- Values can be any type supported by fieldtypes

**Reference:** https://statamic.dev/globals

**Mental model:** Data file holds the actual values.

---

## Global Data (Multisite)

**Locations:**

- Config: `content/globals/{handle}.yaml`
- Data: `content/globals/{site}/{handle}.yaml`

**Purpose:** Separate global values per site while sharing structure.

**Config File Structure:**

```yaml
# content/globals/site_settings.yaml
title: Site Settings
sites:
  - english
  - indonesian

```

**Site Data File Structure:**

```yaml
# content/globals/english/site_settings.yaml
site_name: "My Website"
logo: /assets/logo.png

# content/globals/indonesian/site_settings.yaml
site_name: "Situs Saya"
logo: /assets/logo-id.png

```

**Key rules:**

- Config file contains only `title` and `sites`
- Site files contain data directly (no `data` wrapper)
- Global set unavailable for a site if no file exists in that site's folder
- Blueprint is shared across all sites

**Reference:** https://statamic.dev/tips/localizing-globals

**Mental model:** Each site has its own values, sharing structure.

---

## Global Partials

**Location:** `resources/views/partials/_name.antlers.html`

**Purpose:** Reusable template fragments driven by global content, available across all pages and collections.

**Naming convention:**

- Prefix filename with `_`
- Reference WITHOUT the underscore: `{{ partial:partials/name }}`

**Use cases:**

- Content blocks that appear on multiple pages (carousels, testimonials, CTA banners)
- Site-wide UI elements (header, footer, newsletter signup)
- Promotional banners or announcements

**Pattern — Global-driven partial:**

```
{{# resources/views/partials/_carousel.antlers.html #}}
{{ global:carousel }}
  {{ if enabled }}
    <div class="carousel">
      {{ slides }}
        <div class="carousel-slide">
          <img src="{{ image }}" alt="{{ title }}">
          <p>{{ caption }}</p>
        </div>
      {{ /slides }}
    </div>
  {{ /if }}
{{ /global:carousel }}

```

**Usage in any template:**

```
{{ partial:partials/carousel }}

```

**Key rules:**

- Global partials live in root `partials/` folder
- Domain-specific partials stay in their domain folder (e.g., `pages/partials/`)
- Global partials pull data from global sets
- Can include enable/disable toggle in global for conditional display

**Reference:** https://statamic.dev/tags/partial

**Mental model:** Global partials = reusable components with content managed in globals.

---

## Using Globals in Templates

### Basic Access

**Pattern:**

```
{{# Using global tag pair #}}
{{ global:site_settings }}
  {{ site_name }}
  {{ tagline }}
{{ /global:site_settings }}

{{# Shorthand syntax #}}
{{ site_settings:site_name }}
{{ site_settings:tagline }}

```

### Accessing Nested Data

**Pattern:**

```
{{# Loop through array #}}
{{ site_settings:social_links }}
  <a href="{{ url }}">{{ platform }}</a>
{{ /site_settings:social_links }}

{{# Access nested object #}}
{{ site_settings:company:address }}

```

### Conditional Display

**Pattern:**

```
{{ if site_settings:show_banner }}
  {{ partial:partials/banner }}
{{ /if }}

```

### In Layouts

**Common usage in layout:**

```
<title>{{ title ?? site_settings:site_name }}</title>
<meta name="description" content="{{ meta_description ?? site_settings:default_meta_description }}">

```

**Reference:** https://statamic.dev/globals#templating

---

## Common Global Set Patterns

### Site Settings

**Purpose:** Core site configuration

**Common Fields:**

- `site_name` — Website name
- `tagline` — Site tagline/slogan
- `logo` — Site logo (assets)
- `favicon` — Favicon (assets)
- `default_meta_description` — Fallback meta description
- `google_analytics_id` — Analytics tracking ID

### Social Media

**Purpose:** Social media links and sharing settings

**Common Fields:**

- `social_links` — Replicator/grid with platform & URL
- `default_share_image` — Default OG image (assets)
- `twitter_handle` — Twitter username

### Contact Information

**Purpose:** Business contact details

**Common Fields:**

- `email` — Contact email
- `phone` — Phone number
- `address` — Physical address (textarea or group)
- `google_maps_embed` — Map embed code
- `business_hours` — Operating hours (replicator)

### Footer

**Purpose:** Footer content and links

**Common Fields:**

- `footer_text` — Copyright or footer message
- `footer_links` — Array of links (replicator)
- `newsletter_heading` — Newsletter signup heading
- `newsletter_text` — Newsletter description

### Theme Settings

**Purpose:** Design customization options

**Common Fields:**

- `primary_color` — Brand color (color picker)
- `secondary_color` — Accent color
- `font_family` — Font selection (select)
- `dark_mode` — Enable dark mode (toggle)

### Scripts & Integrations

**Purpose:** Third-party code and integrations

**Common Fields:**

- `head_scripts` — Code for `<head>` (textarea/code)
- `body_start_scripts` — Code after `<body>` (textarea/code)
- `body_end_scripts` — Code before `</body>` (textarea/code)
- `google_tag_manager_id` — GTM container ID

### Reusable Content Blocks

**Purpose:** Content blocks used across multiple pages

**Common Global Sets:**

- `carousel` — Image carousel with slides, enabled toggle
- `testimonials` — Customer testimonials list
- `cta_banner` — Call-to-action banner content
- `announcements` — Site-wide announcements/alerts

**Common Fields per block:**

- `enabled` — Toggle to show/hide (toggle)
- `title` — Block heading
- `content` — Block content (bard/markdown)
- `items` — Repeatable items (replicator/grid)
- `button_text` / `button_url` — CTA button

---

## Blueprint Organization

### Single Tab (Simple)

**Use case:** Few fields, simple global set

**Structure:**

- All fields in one section
- No tabs needed

### Multiple Tabs (Organized)

**Use case:** Many fields, grouped logically

**Common Tab Structure:**

- `general` — Core settings
- `branding` — Logo, colors, fonts
- `social` — Social media links
- `scripts` — Third-party integrations
- `advanced` — Developer options

---

## Multisite Considerations

### Localizable Fields

**Purpose:** Fields that can have different values per site

**How to enable:**

- Set `localizable: true` on field in blueprint
- Each site can then have unique values

### Shared Fields

**Purpose:** Fields that are the same across all sites

**Behavior:**

- Without `localizable: true`, field value is shared
- Editing in one site affects all sites

### Site-Specific Globals

**Purpose:** Global sets that only exist for certain sites

**How to configure:**

- Add `sites` array to config with specific site handles
- Only listed sites will have this global set

---

## Recommended View Structure

```
resources/views/
├── partials/                    # Global/shared partials
│   ├── _header.antlers.html
│   ├── _footer.antlers.html
│   ├── _carousel.antlers.html
│   ├── _testimonials.antlers.html
│   ├── _cta-banner.antlers.html
│   └── _announcement.antlers.html
├── pages/
│   ├── layout.antlers.html
│   ├── default.antlers.html
│   └── partials/                # Page-specific partials
│       └── _hero.antlers.html
└── posts/
    ├── layout.antlers.html
    ├── show.antlers.html
    └── partials/                # Post-specific partials
        └── _card.antlers.html

```

---

## Recommended Global Sets

### Minimal Setup

```
content/globals/
└── site_settings.yaml     # Core site config (name, logo, contact)

```

### Standard Setup

```
content/globals/
├── site_settings.yaml     # Core site config
├── social.yaml            # Social media links
└── footer.yaml            # Footer content

```

### Full Setup

```
content/globals/
├── site_settings.yaml     # Core site config
├── seo.yaml               # Default SEO settings
├── social.yaml            # Social media
├── contact.yaml           # Contact information
├── footer.yaml            # Footer content
├── theme.yaml             # Theme/design settings
├── scripts.yaml           # Third-party integrations
├── carousel.yaml          # Reusable carousel content
├── testimonials.yaml      # Testimonials block
└── cta_banner.yaml        # CTA banner content

```

---

## Recommended Blueprint Structure

```
resources/blueprints/globals/
├── site_settings.yaml
├── seo.yaml
├── social.yaml
├── contact.yaml
├── footer.yaml
├── theme.yaml
├── scripts.yaml
├── carousel.yaml
├── testimonials.yaml
└── cta_banner.yaml

```

---

## Quick Reference

| Concept | Location | Defines | Docs |
| --- | --- | --- | --- |
| Global Set (single) | `content/globals/{handle}.yaml` | Variables & values | [Link](https://statamic.dev/globals) |
| Global Set (multi config) | `content/globals/{handle}.yaml` | Set configuration | [Link](https://statamic.dev/globals) |
| Global Set (multi data) | `content/globals/{site}/{handle}.yaml` | Per-site values | [Link](https://statamic.dev/tips/localizing-globals) |
| Blueprint | `resources/blueprints/globals/{handle}.yaml` | Field definitions | [Link](https://statamic.dev/blueprints) |
| Global Partial | `resources/views/partials/_*.antlers.html` | Reusable templates | [Link](https://statamic.dev/tags/partial) |

---

## Template Quick Reference

| Task | Syntax |
| --- | --- |
| Access global | `{{ site_settings:field_name }}` |
| Global tag pair | `{{ global:site_settings }}...{{ /global:site_settings }}` |
| Loop array field | `{{ site_settings:items }}...{{ /site_settings:items }}` |
| Conditional | `{{ if site_settings:show_feature }}...{{ /if }}` |
| Fallback value | `{{ field ?? site_settings:default_field }}` |
| Include global partial | `{{ partial:partials/carousel }}` |
| Include domain partial | `{{ partial:pages/partials/hero }}` |