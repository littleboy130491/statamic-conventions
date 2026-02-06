# Create Globals

Create or update Statamic global sets, per-site global data, and global blueprints.

## Quick Start

1. **Detect multisite first** — Check `resources/sites.yaml`
2. Create global config: `content/globals/{handle}.yaml`
3. Create blueprint (optional): `resources/blueprints/globals/{handle}.yaml`
4. Access in templates with `{{ global_handle:field }}`

## Global Set Config

### Single Site

**Path:** `content/globals/{handle}.yaml`

```yaml
title: Site Settings
data:
  site_name: "My Website"
  tagline: "Your success is our mission"
  logo: images/logo.png
  email: contact@example.com
  phone: "+1 234 567 890"
  social_links:
    -
      platform: twitter
      url: https://twitter.com/example
    -
      platform: linkedin
      url: https://linkedin.com/company/example
```

### Multisite

**Config file:** `content/globals/{handle}.yaml`
```yaml
title: Site Settings
sites:
  - english
  - indonesian
```

**Per-site data:** `content/globals/{site}/{handle}.yaml`
```yaml
# content/globals/english/site_settings.yaml
site_name: "My Website"
tagline: "Your success is our mission"
logo: images/logo.png
```

```yaml
# content/globals/indonesian/site_settings.yaml
site_name: "Situs Saya"
tagline: "Sukses Anda adalah misi kami"
logo: images/logo-id.png
```

**Note:** Multisite data files have NO `data:` wrapper.

## Global Blueprint

**Path:** `resources/blueprints/globals/{handle}.yaml`

```yaml
title: Site Settings
tabs:
  general:
    display: General
    sections:
      -
        fields:
          -
            handle: site_name
            field:
              type: text
              display: Site Name
              required: true
          -
            handle: tagline
            field:
              type: text
              display: Tagline
          -
            handle: logo
            field:
              type: assets
              display: Logo
              max_files: 1
  contact:
    display: Contact
    sections:
      -
        fields:
          -
            handle: email
            field:
              type: text
              input_type: email
          -
            handle: phone
            field:
              type: text
              input_type: tel
          -
            handle: address
            field:
              type: textarea
  social:
    display: Social
    sections:
      -
        fields:
          -
            handle: social_links
            field:
              type: grid
              fields:
                -
                  handle: platform
                  field:
                    type: select
                    options:
                      twitter: Twitter
                      facebook: Facebook
                      instagram: Instagram
                      linkedin: LinkedIn
                -
                  handle: url
                  field:
                    type: text
                    input_type: url
```

## Using Globals in Templates

**Basic access:**
```antlers
{{ site_settings:site_name }}
{{ site_settings:email }}
```

**Tag pair:**
```antlers
{{ global:site_settings }}
  {{ site_name }}
  {{ tagline }}
{{ /global:site_settings }}
```

**Loop through array:**
```antlers
{{ site_settings:social_links }}
  <a href="{{ url }}">{{ platform }}</a>
{{ /site_settings:social_links }}
```

**Conditional display:**
```antlers
{{ if site_settings:show_banner }}
  {{ partial:partials/banner }}
{{ /if }}
```

**In layouts (with fallback):**
```antlers
<title>{{ title ?? site_settings:site_name }}</title>
```

## Common Global Sets

### Site Settings
```yaml
title: Site Settings
data:
  site_name: "Company Name"
  tagline: "Your tagline here"
  logo: images/logo.png
  favicon: images/favicon.ico
  default_meta_description: "Default site description"
  google_analytics_id: "UA-XXXXX-X"
```

### Social Media
```yaml
title: Social Media
data:
  social_links:
    -
      platform: twitter
      url: https://twitter.com/example
  default_share_image: images/og-default.jpg
  twitter_handle: "@example"
```

### Contact Information
```yaml
title: Contact
data:
  email: contact@example.com
  phone: "+1 234 567 890"
  address: |
    123 Main Street
    City, State 12345
  google_maps_embed: "<iframe>...</iframe>"
  business_hours:
    -
      day: Monday - Friday
      hours: 9:00 AM - 5:00 PM
```

### Footer
```yaml
title: Footer
data:
  footer_text: "© 2024 Company Name. All rights reserved."
  footer_links:
    -
      title: Privacy Policy
      url: /privacy
    -
      title: Terms of Service
      url: /terms
  newsletter_heading: "Subscribe to our newsletter"
```

### Reusable Content Blocks
```yaml
title: Carousel
data:
  enabled: true
  slides:
    -
      image: carousel/slide1.jpg
      title: First Slide
      caption: Description here
    -
      image: carousel/slide2.jpg
      title: Second Slide
```

## Global-Driven Partials

**Partial:** `resources/views/partials/_carousel.antlers.html`
```antlers
{{ global:carousel }}
  {{ if enabled }}
    <div class="carousel">
      {{ slides }}
        <div class="carousel-slide">
          <img src="{{ image }}" alt="{{ title }}">
          {{ if caption }}<p>{{ caption }}</p>{{ /if }}
        </div>
      {{ /slides }}
    </div>
  {{ /if }}
{{ /global:carousel }}
```

**Usage:**
```antlers
{{ partial:partials/carousel }}
```

## Multisite Considerations

**Localizable fields:**
```yaml
field:
  type: text
  localizable: true  # Different value per site
```

**Site-specific globals:**
```yaml
title: Site Settings
sites:
  - english
  - indonesian
```

## Recommended Global Sets

**Minimal:**
```
content/globals/
└── site_settings.yaml
```

**Standard:**
```
content/globals/
├── site_settings.yaml
├── social.yaml
└── footer.yaml
```

**Full:**
```
content/globals/
├── site_settings.yaml
├── seo.yaml
├── social.yaml
├── contact.yaml
├── footer.yaml
├── theme.yaml
├── scripts.yaml
├── carousel.yaml
└── testimonials.yaml
```

## Boundaries

- Blueprint is optional but recommended for custom fieldtypes
- Blueprint handle must match global handle

## Accuracy Checks

- Single site: data under `data:` key
- Multisite: data files have NO `data:` wrapper
- Global config shared, data per-site in multisite
- Access with `{{ handle:field }}` syntax

## Quick Reference

| Task | Syntax |
|------|--------|
| Access global | `{{ site_settings:field_name }}` |
| Global tag pair | `{{ global:site_settings }}...{{ /global:site_settings }}` |
| Loop array | `{{ site_settings:items }}...{{ /site_settings:items }}` |
| Conditional | `{{ if site_settings:enabled }}...{{ /if }}` |
| Fallback | `{{ field ?? site_settings:default }}` |
