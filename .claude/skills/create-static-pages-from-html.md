# Create Static Pages from HTML

Map static HTML into Statamic static page fields, entries, and Antlers templates.

## Quick Start

1. **Detect multisite first** — Check `resources/sites.yaml`
2. Check blueprint for available fields
3. Scan HTML and extract content values
4. Update entry `.md` file with extracted values
5. Create Antlers template with wrapped variables

## Workflow

### Step 1: Detect Multisite

Check `resources/sites.yaml`:
- Single site: One site key
- Multisite: Multiple site keys

Entry paths differ based on this.

### Step 2: Decide Blueprint Strategy

| Page Type | Approach |
|-----------|----------|
| Homepage | Hybrid (fixed hero + flexible sections) |
| About | Page-specific blueprint |
| Services | Flexible (replicator) |
| Contact | Page-specific blueprint |
| Landing Pages | Flexible (replicator) |
| Legal Pages | Simple (title + bard) |

**Decision Tree:**
- Fixed, unique layout? → Page-specific blueprint
- Reusable sections? → Flexible page builder (replicator)
- Simple content? → Default blueprint

### Step 3: Check Blueprint Fields

Read: `resources/blueprints/collections/{collection}/{blueprint}.yaml`

Create field inventory:

| Handle | Type | Required |
|--------|------|----------|
| `title` | text | yes |
| `hero_image` | assets | no |
| `content` | bard | no |
| `features` | replicator | no |

### Step 4: Scan HTML and Extract Content

**Example HTML:**
```html
<section class="hero">
  <h1>About Our Company</h1>
  <p class="subtitle">Building trust since 1999</p>
  <img src="images/hero.jpg" alt="Our office">
</section>

<section class="features">
  <div class="feature">
    <img src="icons/quality.svg">
    <h3>Quality First</h3>
    <p>We never compromise</p>
  </div>
</section>
```

**Extraction Mapping:**

| HTML Element | Value | Field |
|--------------|-------|-------|
| `<h1>` | "About Our Company" | `title` |
| `.subtitle` | "Building trust..." | `subtitle` |
| `.hero img` | "images/hero.jpg" | `hero_image` |
| `.feature` | Multiple items | `features` (replicator) |

### Step 5: Update Entry File

**Path:**
- Single: `content/collections/pages/{slug}.md`
- Multisite: `content/collections/pages/{site}/{slug}.md`

**Entry Structure:**
```yaml
---
id: 550e8400-e29b-41d4-a716-446655440000
title: About Our Company
blueprint: about
subtitle: Building trust since 1999
hero_image: images/hero.jpg
features:
  -
    type: feature
    icon: icons/quality.svg
    title: Quality First
    description: We never compromise
  -
    type: feature
    icon: icons/support.svg
    title: 24/7 Support
    description: Always here for you
cta_heading: Ready to work with us?
cta_button_text: Get in Touch
cta_button_link: /contact
---
```

### Step 6: Create Antlers Template

**Path:** `resources/views/pages/{blueprint}.antlers.html`

## Critical Rule: Always Wrap Variables

**NEVER output bare variables. Always wrap with `{{ if }}`:**

```antlers
{{-- WRONG --}}
<h1>{{ title }}</h1>
<p>{{ subtitle }}</p>

{{-- CORRECT --}}
{{ if title }}<h1>{{ title }}</h1>{{ /if }}
{{ if subtitle }}<p>{{ subtitle }}</p>{{ /if }}
```

**Exception:** Required fields can skip conditionals.

## Antlers Syntax Patterns

### Text Fields
```antlers
{{ if title }}<h1>{{ title }}</h1>{{ /if }}
{{ title ?? 'Default Title' }}
```

### Assets
```antlers
{{ if hero_image }}
  <img src="{{ hero_image:url }}" alt="{{ hero_image:alt ?? title }}">
{{ /if }}

{{-- Gallery --}}
{{ if gallery }}
  {{ gallery }}
    <img src="{{ url }}" alt="{{ alt }}">
  {{ /gallery }}
{{ /if }}
```

### Links
```antlers
{{ if cta_link }}
  <a href="{{ cta_link }}">{{ cta_text ?? 'Learn More' }}</a>
{{ /if }}
```

### Replicator
```antlers
{{ if sections }}
  {{ sections }}
    {{ if type == 'hero' }}
      <section class="hero">
        {{ if heading }}<h1>{{ heading }}</h1>{{ /if }}
        {{ if image }}<img src="{{ image:url }}">{{ /if }}
      </section>
    {{ elseif type == 'features' }}
      <section class="features">
        {{ if items }}
          {{ items }}
            <div class="feature">
              {{ if icon }}<img src="{{ icon:url }}">{{ /if }}
              {{ if title }}<h3>{{ title }}</h3>{{ /if }}
              {{ if description }}<p>{{ description }}</p>{{ /if }}
            </div>
          {{ /items }}
        {{ /if }}
      </section>
    {{ /if }}
  {{ /sections }}
{{ /if }}
```

**Using partials for sets:**
```antlers
{{ sections }}
  {{ partial src="sets/{type}" }}
{{ /sections }}
```

### Grid
```antlers
{{ if team_members }}
  <div class="team-grid">
    {{ team_members }}
      <div class="member">
        {{ if photo }}<img src="{{ photo:url }}">{{ /if }}
        {{ if name }}<h3>{{ name }}</h3>{{ /if }}
        {{ if role }}<p>{{ role }}</p>{{ /if }}
      </div>
    {{ /team_members }}
  </div>
{{ /if }}
```

### Bard
```antlers
{{ if content }}
  <div class="prose">
    {{ content }}
  </div>
{{ /if }}
```

### Section Conditional
```antlers
{{-- Only show section if has content --}}
{{ if cta_heading || cta_button_text }}
  <section class="cta">
    {{ if cta_heading }}<h2>{{ cta_heading }}</h2>{{ /if }}
    {{ if cta_button_link }}
      <a href="{{ cta_button_link }}">
        {{ cta_button_text ?? 'Contact Us' }}
      </a>
    {{ /if }}
  </section>
{{ /if }}
```

## Complete Template Example

```antlers
{{-- resources/views/pages/about.antlers.html --}}

{{-- Hero Section --}}
{{ if title || subtitle || hero_image }}
  <section class="hero">
    {{ if hero_image }}
      <img src="{{ hero_image:url }}"
           alt="{{ hero_image:alt ?? title }}"
           class="hero-image">
    {{ /if }}
    <div class="hero-content">
      {{ if title }}<h1>{{ title }}</h1>{{ /if }}
      {{ if subtitle }}<p class="subtitle">{{ subtitle }}</p>{{ /if }}
    </div>
  </section>
{{ /if }}

{{-- Intro Section --}}
{{ if intro }}
  <section class="intro">
    <div class="prose">
      {{ intro | nl2br }}
    </div>
  </section>
{{ /if }}

{{-- Features Section --}}
{{ if features }}
  <section class="features">
    <div class="features-grid">
      {{ features }}
        {{ if type == 'feature' }}
          <div class="feature">
            {{ if icon }}
              <div class="feature-icon">
                <img src="{{ icon:url }}" alt="">
              </div>
            {{ /if }}
            {{ if title }}<h3>{{ title }}</h3>{{ /if }}
            {{ if description }}<p>{{ description }}</p>{{ /if }}
          </div>
        {{ /if }}
      {{ /features }}
    </div>
  </section>
{{ /if }}

{{-- CTA Section --}}
{{ if cta_heading || cta_button_text }}
  <section class="cta">
    <div class="cta-content">
      {{ if cta_heading }}<h2>{{ cta_heading }}</h2>{{ /if }}
      {{ if cta_button_link }}
        <a href="{{ cta_button_link }}" class="btn">
          {{ cta_button_text ?? 'Get in Touch' }}
        </a>
      {{ /if }}
    </div>
  </section>
{{ /if }}
```

## Entry YAML Syntax Reference

| Field Type | YAML |
|------------|------|
| Text | `field: Value` |
| Multiline | `field: \|`<br>`  Line 1`<br>`  Line 2` |
| Single asset | `field: path/to/file.jpg` |
| Multiple | `field:`<br>`  - item1`<br>`  - item2` |
| Replicator | `field:`<br>`  -`<br>`    type: set_name`<br>`    handle: value` |
| Grid | `field:`<br>`  -`<br>`    col1: value`<br>`    col2: value` |

## Handling Missing Fields

If HTML content doesn't map to blueprint field:

1. **Flag for blueprint update**
2. **Use existing field creatively** (e.g., use `subtitle` for tagline)
3. **Hardcode if truly static** (copyright year, site name from global)

## Conversion Report Template

After conversion, provide:

```markdown
## Conversion Complete

### Files Modified
- Entry: `content/collections/pages/about.md`
- Template: `resources/views/pages/about.antlers.html`

### Fields Used
| Field | Type | Source |
|-------|------|--------|
| title | text | `<h1>` |
| subtitle | text | `.subtitle` |

### Missing Fields (Blueprint Update Needed)
| Handle | Type | Source |
|--------|------|--------|
| founded_year | integer | `.founded` |

### Conditionals Applied
- All optional fields wrapped with `{{ if }}`
```

## Boundaries

- Do NOT create blueprints here — Use `create-blueprints`
- Do NOT create collection config — Use `create-collections`
- Always wrap ALL variables with conditionals

## Accuracy Checks

- Entry is `.md` with YAML frontmatter
- Template is `.antlers.html`
- All non-required fields wrapped with `{{ if }}`
- Replicator items have `type` key
- Asset paths relative to container

## Checklist

- [ ] Detected single-site or multisite
- [ ] Checked blueprint for available fields
- [ ] Scanned HTML and mapped content to fields
- [ ] Created entry with extracted values
- [ ] Created template with wrapped variables
- [ ] All sections have conditionals
- [ ] Reported missing fields
