# HTML to Antlers Conversion — LLM Instructions

## Overview

This document provides instructions for converting static HTML files into dynamic Antlers templates for Statamic 5+ projects. The conversion process involves:

1. Scanning HTML to extract content values
2. Checking available blueprint fields
3. Updating the `.md` entry file with extracted values
4. Creating `.antlers.html` template with variables wrapped in conditionals

> ⚠️ **PREREQUISITE:** Before converting, check `resources/sites.yaml` to detect if the project is single-site or multisite. Entry file paths differ between configurations.

---

## Conversion Workflow

```
START: HTML to Antlers Conversion

┌─ STEP 1: Detect multisite
│  └─ Check resources/sites.yaml → determines entry file paths
│
├─ STEP 2: Check available blueprint fields FIRST
│  └─ Read: resources/blueprints/collections/{collection}/{blueprint}.yaml
│  └─ List all field handles and their types
│
├─ STEP 3: Scan HTML and extract content
│  └─ Identify: headings, paragraphs, images, links, lists, repeating blocks
│  └─ Map each content piece to an available field (or flag missing fields)
│
├─ STEP 4: Update the entry .md file
│  └─ Location (single): content/collections/{collection}/{slug}.md
│  └─ Location (multi): content/collections/{collection}/{site}/{slug}.md
│  └─ Populate field values with content extracted from HTML
│
├─ STEP 5: Create the Antlers template
│  └─ Location: resources/views/{collection}/{template}.antlers.html
│  └─ Replace hardcoded HTML with variables
│  └─ WRAP ALL VARIABLES with {{ if }} conditionals
│
└─ STEP 6: Verify and report
   └─ List fields used, fields missing, suggested blueprint additions
```

---

## Critical Rules

### Rule 1: Always Check Blueprint First

Before scanning HTML, read the blueprint to know what fields are available:

```bash
# Check blueprint
cat resources/blueprints/collections/{collection}/{blueprint}.yaml
```

**Extract field inventory:**
- List all field handles
- Note field types (text, textarea, bard, assets, replicator, etc.)
- Note which fields are required vs optional
- Note nested structures (replicator sets, grid columns)

### Rule 2: Always Wrap Variables with Conditionals

**NEVER output a bare variable.** Always wrap with `{{ if }}` to prevent errors when empty:

```antlers
{{-- ❌ WRONG: Will error or show nothing awkwardly if empty --}}
<h1>{{ title }}</h1>
<p>{{ subtitle }}</p>
<img src="{{ hero_image:url }}">

{{-- ✅ CORRECT: Safe output with conditionals --}}
{{ if title }}<h1>{{ title }}</h1>{{ /if }}
{{ if subtitle }}<p>{{ subtitle }}</p>{{ /if }}
{{ if hero_image }}<img src="{{ hero_image:url }}" alt="{{ hero_image:alt }}">{{ /if }}
```

**Exception:** Fields marked as `required` in blueprint can skip conditionals if guaranteed to have value.

### Rule 3: Update Entry File with HTML Content

Extract values from HTML and populate the entry `.md` file:

```yaml
---
id: existing-uuid-or-generate-new
title: 'Extracted from <h1>'
subtitle: 'Extracted from subtitle element'
hero_image: path/to/image.jpg
description: 'Extracted from <p> content'
features:
  -
    title: 'Feature 1 from HTML'
    description: 'Feature 1 description from HTML'
  -
    title: 'Feature 2 from HTML'
    description: 'Feature 2 description from HTML'
---
```

---

## Step 1: Detect Multisite

Check `resources/sites.yaml` (or `config/statamic/sites.php`) first:

```yaml
# Single site
default:
  name: Site Name
  url: /

# Multisite
english:
  name: English
  url: /
indonesian:
  name: Indonesian
  url: /id/
```

This determines entry file paths in subsequent steps.

---

## Step 2: Check Blueprint Fields FIRST

**Before looking at HTML**, inventory all available fields:

### Read the Blueprint

```bash
cat resources/blueprints/collections/{collection}/{blueprint}.yaml
```

### Create Field Inventory

Document each field with:

| Handle | Type | Required | Notes |
|--------|------|----------|-------|
| `title` | text | yes | Page title |
| `subtitle` | text | no | Optional subtitle |
| `hero_image` | assets | no | Single image |
| `content` | bard | no | Rich text with sets |
| `features` | replicator | no | Repeating blocks |

### Identify Field Capabilities

**Simple fields:**
- `text`, `textarea`, `markdown` → Single value
- `toggle` → Boolean
- `select`, `radio`, `button_group` → Single choice
- `checkboxes` → Multiple choices

**Complex fields:**
- `assets` → Single or multiple files
- `bard` → Rich text, may have sets
- `replicator` → Repeating structured blocks
- `grid` → Repeating rows with columns
- `entries`, `terms` → Relationships

**Note any replicator/bard sets:**
```yaml
# If blueprint has replicator with sets
features:
  type: replicator
  sets:
    feature:
      fields:
        - handle: icon
          field: { type: assets }
        - handle: title
          field: { type: text }
        - handle: description
          field: { type: textarea }
```

---

## Step 3: Scan HTML and Extract Content

### Scanning Process

1. **Read the HTML file** completely
2. **Identify content elements** that should be dynamic
3. **Map each element** to a blueprint field
4. **Flag gaps** where fields don't exist

### Content Extraction Table

Create a mapping table:

| HTML Element | Content Value | Maps to Field | Field Exists? |
|--------------|---------------|---------------|---------------|
| `<h1>` | "About Our Company" | `title` | ✅ Yes |
| `<p class="subtitle">` | "Since 1999" | `subtitle` | ✅ Yes |
| `<img class="hero">` | "hero.jpg" | `hero_image` | ✅ Yes |
| `<div class="features">` | 3 feature blocks | `features` | ✅ Yes (replicator) |
| `<p class="founded">` | "1999" | `founded_year` | ❌ No — flag for addition |

### HTML Scanning Example

**Input HTML:**
```html
<section class="hero">
  <h1>About Our Company</h1>
  <p class="subtitle">Building trust since 1999</p>
  <img src="images/hero-about.jpg" alt="Our office">
</section>

<section class="intro">
  <p>We are a leading provider of web solutions...</p>
  <p>Our team of experts delivers...</p>
</section>

<section class="features">
  <div class="feature">
    <img src="icons/quality.svg" alt="">
    <h3>Quality First</h3>
    <p>We never compromise on quality</p>
  </div>
  <div class="feature">
    <img src="icons/support.svg" alt="">
    <h3>24/7 Support</h3>
    <p>Always here when you need us</p>
  </div>
</section>

<section class="cta">
  <h2>Ready to work with us?</h2>
  <a href="/contact" class="btn">Get in Touch</a>
</section>
```

**Extracted content mapping:**

| Content | Value | Target Field | Type |
|---------|-------|--------------|------|
| Hero heading | "About Our Company" | `title` | text |
| Hero subtitle | "Building trust since 1999" | `subtitle` | text |
| Hero image | "images/hero-about.jpg" | `hero_image` | assets |
| Hero image alt | "Our office" | (asset alt field) | - |
| Intro paragraphs | "We are a leading..." | `intro` or `content` | textarea/bard |
| Features | 2 items | `features` | replicator |
| Feature 1 icon | "icons/quality.svg" | `features.0.icon` | assets |
| Feature 1 title | "Quality First" | `features.0.title` | text |
| Feature 1 desc | "We never compromise..." | `features.0.description` | textarea |
| Feature 2 icon | "icons/support.svg" | `features.1.icon` | assets |
| Feature 2 title | "24/7 Support" | `features.1.title` | text |
| Feature 2 desc | "Always here..." | `features.1.description` | textarea |
| CTA heading | "Ready to work with us?" | `cta_heading` | text |
| CTA button text | "Get in Touch" | `cta_button_text` | text |
| CTA button link | "/contact" | `cta_button_link` | link/text |

---

## Step 4: Update Entry .md File

### Locate Entry File

**Single site:**
```
content/collections/{collection}/{slug}.md
```

**Multisite:**
```
content/collections/{collection}/{site}/{slug}.md
```

### Populate Entry with Extracted Values

**Before (empty or minimal entry):**
```yaml
---
id: 550e8400-e29b-41d4-a716-446655440000
title: About
---
```

**After (populated with HTML content):**
```yaml
---
id: 550e8400-e29b-41d4-a716-446655440000
title: About Our Company
subtitle: Building trust since 1999
hero_image: images/hero-about.jpg
intro: |
  We are a leading provider of web solutions...
  
  Our team of experts delivers...
features:
  -
    type: feature
    icon: icons/quality.svg
    title: Quality First
    description: We never compromise on quality
  -
    type: feature
    icon: icons/support.svg
    title: 24/7 Support
    description: Always here when you need us
cta_heading: Ready to work with us?
cta_button_text: Get in Touch
cta_button_link: /contact
---
```

### Entry Update Rules

1. **Preserve existing `id`** — Never change the UUID
2. **Preserve existing values** — Only update empty fields or confirm overwrites
3. **Match field types:**
   - Text: Quote if contains special characters
   - Multiline: Use `|` for literal blocks
   - Arrays: Use YAML list syntax
   - Replicator: Include `type` key for each set
4. **Asset paths** — Use relative paths from assets container

### Handling Different Field Types

**Text (single line):**
```yaml
title: About Our Company
tagline: Your success is our mission
```

**Textarea (multiline):**
```yaml
description: |
  This is a longer description
  that spans multiple lines.
  
  It can have paragraphs too.
```

**Assets (single):**
```yaml
hero_image: images/hero.jpg
# or with alt text if supported
hero_image:
  src: images/hero.jpg
  alt: Hero image description
```

**Assets (multiple):**
```yaml
gallery:
  - images/photo1.jpg
  - images/photo2.jpg
  - images/photo3.jpg
```

**Replicator:**
```yaml
sections:
  -
    type: hero
    heading: Welcome
    subheading: To our site
  -
    type: features
    items:
      -
        title: Feature 1
        description: Description 1
      -
        title: Feature 2
        description: Description 2
  -
    type: cta
    text: Get Started
    link: /contact
```

**Grid:**
```yaml
team_members:
  -
    name: John Doe
    role: CEO
    photo: team/john.jpg
  -
    name: Jane Smith
    role: CTO
    photo: team/jane.jpg
```

---

## Step 5: Create Antlers Template with Conditionals

### Template Location

```
resources/views/{collection}/{template}.antlers.html
```

### Conditional Wrapping Patterns

**ALWAYS wrap variables with conditionals unless field is required.**

#### Simple Fields

```antlers
{{-- Text field --}}
{{ if title }}<h1>{{ title }}</h1>{{ /if }}

{{-- With fallback for required display --}}
{{ if title }}
  <h1>{{ title }}</h1>
{{ else }}
  <h1>Untitled</h1>
{{ /if }}

{{-- Textarea/multiline --}}
{{ if description }}
  <div class="description">
    {{ description | nl2br }}
  </div>
{{ /if }}
```

#### Asset Fields

```antlers
{{-- Single image --}}
{{ if hero_image }}
  <img 
    src="{{ hero_image:url }}" 
    alt="{{ hero_image:alt ?? title }}"
    {{ if hero_image:width }}width="{{ hero_image:width }}"{{ /if }}
    {{ if hero_image:height }}height="{{ hero_image:height }}"{{ /if }}
  >
{{ /if }}

{{-- Image gallery --}}
{{ if gallery }}
  <div class="gallery">
    {{ gallery }}
      <img src="{{ url }}" alt="{{ alt }}">
    {{ /gallery }}
  </div>
{{ /if }}
```

#### Link Fields

```antlers
{{-- Link with text --}}
{{ if cta_button_link }}
  <a href="{{ cta_button_link }}" class="btn">
    {{ cta_button_text ?? 'Learn More' }}
  </a>
{{ /if }}

{{-- Link field (object) --}}
{{ if link }}
  {{ link }}
    <a href="{{ url }}" {{ if target }}target="{{ target }}"{{ /if }}>
      {{ text ?? 'Click here' }}
    </a>
  {{ /link }}
{{ /if }}
```

#### Replicator Fields

```antlers
{{-- Check if replicator has items --}}
{{ if features }}
  <section class="features">
    {{ features }}
      {{ if type == 'feature' }}
        <div class="feature">
          {{ if icon }}
            <img src="{{ icon:url }}" alt="" class="feature-icon">
          {{ /if }}
          {{ if title }}<h3>{{ title }}</h3>{{ /if }}
          {{ if description }}<p>{{ description }}</p>{{ /if }}
        </div>
      {{ /if }}
    {{ /features }}
  </section>
{{ /if }}
```

#### Bard Fields

```antlers
{{-- Simple bard output --}}
{{ if content }}
  <div class="prose">
    {{ content }}
  </div>
{{ /if }}

{{-- Bard with sets --}}
{{ if content }}
  <div class="content">
    {{ content }}
      {{ if type == 'text' }}
        <div class="prose">{{ text }}</div>
      {{ elseif type == 'image' }}
        {{ if image }}
          <figure>
            <img src="{{ image:url }}" alt="{{ image:alt }}">
            {{ if caption }}<figcaption>{{ caption }}</figcaption>{{ /if }}
          </figure>
        {{ /if }}
      {{ elseif type == 'quote' }}
        {{ if quote }}
          <blockquote>
            <p>{{ quote }}</p>
            {{ if author }}<cite>{{ author }}</cite>{{ /if }}
          </blockquote>
        {{ /if }}
      {{ /if }}
    {{ /content }}
  </div>
{{ /if }}
```

#### Grid Fields

```antlers
{{-- Check grid has rows --}}
{{ if team_members }}
  <div class="team-grid">
    {{ team_members }}
      <div class="team-member">
        {{ if photo }}
          <img src="{{ photo:url }}" alt="{{ name }}">
        {{ /if }}
        {{ if name }}<h3>{{ name }}</h3>{{ /if }}
        {{ if role }}<p class="role">{{ role }}</p>{{ /if }}
      </div>
    {{ /team_members }}
  </div>
{{ /if }}
```

#### Nested Conditionals for Sections

```antlers
{{-- Only show section if it has content --}}
{{ if cta_heading || cta_button_text }}
  <section class="cta">
    {{ if cta_heading }}<h2>{{ cta_heading }}</h2>{{ /if }}
    {{ if cta_button_link }}
      <a href="{{ cta_button_link }}" class="btn">
        {{ cta_button_text ?? 'Contact Us' }}
      </a>
    {{ /if }}
  </section>
{{ /if }}
```

### Complete Template Example

**Template: `resources/views/pages/about.antlers.html`**

```antlers
{{-- Hero Section --}}
{{ if title || subtitle || hero_image }}
  <section class="hero">
    {{ if hero_image }}
      <img 
        src="{{ hero_image:url }}" 
        alt="{{ hero_image:alt ?? title }}"
        class="hero-image"
      >
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
        <a href="{{ cta_button_link }}" class="btn btn-primary">
          {{ cta_button_text ?? 'Get in Touch' }}
        </a>
      {{ /if }}
    </div>
  </section>
{{ /if }}
```

---

## Step 6: Verify and Report

After conversion, provide a summary:

### Conversion Report Template

```markdown
## Conversion Complete

### Files Modified
- Entry: `content/collections/pages/about.md`
- Template: `resources/views/pages/about.antlers.html`

### Fields Used
| Field | Type | Value Source |
|-------|------|--------------|
| title | text | `<h1>` |
| subtitle | text | `.subtitle` |
| hero_image | assets | `.hero img` |
| intro | textarea | `.intro p` |
| features | replicator | `.feature` elements |
| cta_heading | text | `.cta h2` |
| cta_button_text | text | `.cta .btn` |
| cta_button_link | text | `.cta .btn[href]` |

### Missing Fields (Blueprint Update Needed)
| Suggested Handle | Type | Content Source |
|------------------|------|----------------|
| founded_year | text | `.founded` element |
| team_size | integer | `.stats .team-count` |

### Conditionals Applied
- All optional fields wrapped with `{{ if }}`
- Sections only render if they have content
- Fallback values provided for required display elements

### Manual Review Needed
- [ ] Verify asset paths are correct
- [ ] Check replicator set types match blueprint
- [ ] Confirm link URLs are valid
- [ ] Test template renders without errors
```

---

## Step 6: Antlers Syntax Reference

### Basic Variables

**Text fields:**
```html
<!-- HTML -->
<h1>About Our Company</h1>
<p>We are a leading provider...</p>

<!-- Antlers -->
<h1>{{ title }}</h1>
<p>{{ description }}</p>
```

**With fallback:**
```antlers
<h1>{{ title ?? 'Default Title' }}</h1>
```

---

### Conditional Output

**If field exists:**
```html
<!-- HTML -->
<p class="subtitle">Our Mission</p>

<!-- Antlers -->
{{ if subtitle }}
  <p class="subtitle">{{ subtitle }}</p>
{{ /if }}
```

**If/else:**
```antlers
{{ if featured }}
  <span class="badge">Featured</span>
{{ else }}
  <span class="badge">Regular</span>
{{ /if }}
```

**Unless (inverse if):**
```antlers
{{ unless hide_cta }}
  <a href="{{ cta_link }}">{{ cta_text }}</a>
{{ /unless }}
```

---

### Assets (Images, Files)

**Single image:**
```html
<!-- HTML -->
<img src="images/hero.jpg" alt="Hero Image">

<!-- Antlers -->
<img src="{{ hero_image:url }}" alt="{{ hero_image:alt }}">

<!-- With additional attributes -->
<img 
  src="{{ hero_image:url }}"
  alt="{{ hero_image:alt }}"
  width="{{ hero_image:width }}"
  height="{{ hero_image:height }}"
>
```

**Image with Glide (resizing):**
```antlers
<img src="{{ glide:hero_image width='800' height='600' fit='crop' }}">
```

**Multiple images (gallery):**
```html
<!-- HTML -->
<div class="gallery">
  <img src="image1.jpg">
  <img src="image2.jpg">
  <img src="image3.jpg">
</div>

<!-- Antlers -->
<div class="gallery">
  {{ gallery }}
    <img src="{{ url }}" alt="{{ alt }}">
  {{ /gallery }}
</div>
```

**Conditional image:**
```antlers
{{ if hero_image }}
  <img src="{{ hero_image:url }}" alt="{{ hero_image:alt }}">
{{ /if }}
```

---

### Links

**Link field:**
```html
<!-- HTML -->
<a href="/contact" class="btn">Contact Us</a>

<!-- Antlers -->
<a href="{{ cta_link }}" class="btn">{{ cta_text }}</a>
```

**Link field (with URL object):**
```antlers
{{ link }}
  <a href="{{ url }}" {{ if target }}target="{{ target }}"{{ /if }}>
    {{ text ?? title }}
  </a>
{{ /link }}
```

**Internal entry link:**
```antlers
{{ related_page }}
  <a href="{{ url }}">{{ title }}</a>
{{ /related_page }}
```

---

### Rich Text (Bard/Markdown)

**Bard field (outputs HTML):**
```html
<!-- HTML -->
<div class="content">
  <p>First paragraph...</p>
  <h2>Subheading</h2>
  <p>Second paragraph...</p>
</div>

<!-- Antlers -->
<div class="content">
  {{ content }}
</div>
```

**Markdown field:**
```antlers
<div class="content">
  {{ content | markdown }}
</div>
```

**Bard with sets (flexible content):**
```antlers
<div class="content">
  {{ content }}
    {{ if type == 'text' }}
      <div class="prose">{{ text }}</div>
    {{ elseif type == 'image' }}
      <figure>
        <img src="{{ image:url }}" alt="{{ image:alt }}">
        {{ if caption }}<figcaption>{{ caption }}</figcaption>{{ /if }}
      </figure>
    {{ elseif type == 'video' }}
      <div class="video-embed">{{ video_url | embed_url }}</div>
    {{ /if }}
  {{ /content }}
</div>
```

---

### Replicator (Flexible Content Blocks)

**Entry structure:**
```yaml
sections:
  -
    type: hero
    heading: Welcome
    background: images/hero-bg.jpg
  -
    type: features
    items:
      - title: Feature 1
        description: Description 1
      - title: Feature 2
        description: Description 2
  -
    type: cta
    text: Get Started
    link: /contact
```

**Antlers template:**
```html
<!-- HTML (static) -->
<section class="hero">
  <h1>Welcome</h1>
</section>
<section class="features">
  <div class="feature">
    <h3>Feature 1</h3>
    <p>Description 1</p>
  </div>
</section>

<!-- Antlers (dynamic) -->
{{ sections }}
  {{ if type == 'hero' }}
    <section class="hero" style="background-image: url('{{ background:url }}')">
      <h1>{{ heading }}</h1>
    </section>
  {{ elseif type == 'features' }}
    <section class="features">
      {{ items }}
        <div class="feature">
          <h3>{{ title }}</h3>
          <p>{{ description }}</p>
        </div>
      {{ /items }}
    </section>
  {{ elseif type == 'cta' }}
    <section class="cta">
      <a href="{{ link }}" class="btn">{{ text }}</a>
    </section>
  {{ /if }}
{{ /sections }}
```

**Using partials for sets:**
```antlers
{{ sections }}
  {{ partial src="sets/{type}" }}
{{ /sections }}
```

With partial files:
- `resources/views/sets/_hero.antlers.html`
- `resources/views/sets/_features.antlers.html`
- `resources/views/sets/_cta.antlers.html`

---

### Grid (Repeating Structured Data)

**Entry structure:**
```yaml
team_members:
  -
    name: John Doe
    role: CEO
    photo: images/john.jpg
  -
    name: Jane Smith
    role: CTO
    photo: images/jane.jpg
```

**Antlers template:**
```html
<!-- HTML -->
<div class="team-grid">
  <div class="member">
    <img src="john.jpg">
    <h3>John Doe</h3>
    <p>CEO</p>
  </div>
  <div class="member">
    <img src="jane.jpg">
    <h3>Jane Smith</h3>
    <p>CTO</p>
  </div>
</div>

<!-- Antlers -->
<div class="team-grid">
  {{ team_members }}
    <div class="member">
      <img src="{{ photo:url }}" alt="{{ name }}">
      <h3>{{ name }}</h3>
      <p>{{ role }}</p>
    </div>
  {{ /team_members }}
</div>
```

---

### Entries Relationship

**Related entries field:**
```yaml
# Entry
related_posts:
  - post-uuid-1
  - post-uuid-2
```

**Antlers template:**
```html
<!-- HTML -->
<div class="related-posts">
  <article>
    <h3>Related Post 1</h3>
    <p>Excerpt...</p>
  </article>
</div>

<!-- Antlers -->
<div class="related-posts">
  {{ related_posts }}
    <article>
      <a href="{{ url }}">
        <h3>{{ title }}</h3>
        <p>{{ excerpt }}</p>
      </a>
    </article>
  {{ /related_posts }}
</div>
```

---

### Taxonomy Terms

**Entry with taxonomy:**
```yaml
categories:
  - news
  - announcements
tags:
  - featured
  - update
```

**Antlers template:**
```html
<!-- HTML -->
<div class="tags">
  <span>News</span>
  <span>Announcements</span>
</div>

<!-- Antlers -->
<div class="tags">
  {{ categories }}
    <a href="{{ url }}">{{ title }}</a>
  {{ /categories }}
</div>
```

---

### Globals

**Accessing global sets:**
```html
<!-- HTML -->
<footer>
  <p>© 2024 Company Name</p>
  <a href="https://facebook.com/company">Facebook</a>
</footer>

<!-- Antlers -->
<footer>
  <p>© {{ now format="Y" }} {{ site_settings:company_name }}</p>
  <a href="{{ social:facebook }}">Facebook</a>
</footer>
```

**Global scope syntax:**
```antlers
{{ global:site_settings }}
  {{ company_name }}
  {{ email }}
{{ /global:site_settings }}

{{-- Or direct access --}}
{{ site_settings:company_name }}
{{ site_settings:email }}
```

---

### Navigation

**Static navigation:**
```html
<!-- HTML -->
<nav>
  <a href="/">Home</a>
  <a href="/about">About</a>
  <a href="/contact">Contact</a>
</nav>

<!-- Antlers -->
<nav>
  {{ nav:header }}
    <a href="{{ url }}" {{ if is_current }}class="active"{{ /if }}>
      {{ title }}
    </a>
  {{ /nav:header }}
</nav>
```

**With dropdowns:**
```antlers
<nav>
  {{ nav:header }}
    <div class="nav-item {{ if children }}has-dropdown{{ /if }}">
      <a href="{{ url }}" class="{{ if is_current || is_parent }}active{{ /if }}">
        {{ title }}
      </a>
      {{ if children }}
        <div class="dropdown">
          {{ children }}
            <a href="{{ url }}">{{ title }}</a>
          {{ /children }}
        </div>
      {{ /if }}
    </div>
  {{ /nav:header }}
</nav>
```

---

### Collection Listings

**Listing entries:**
```html
<!-- HTML -->
<div class="blog-posts">
  <article>
    <h2>Post Title 1</h2>
    <p>Posted on January 1, 2024</p>
  </article>
  <article>
    <h2>Post Title 2</h2>
    <p>Posted on January 2, 2024</p>
  </article>
</div>

<!-- Antlers -->
<div class="blog-posts">
  {{ collection:posts limit="10" }}
    <article>
      <a href="{{ url }}">
        <h2>{{ title }}</h2>
        <p>Posted on {{ date format="F j, Y" }}</p>
      </a>
    </article>
  {{ /collection:posts }}
</div>
```

**With filtering and sorting:**
```antlers
{{ collection:posts 
   limit="6"
   sort="date:desc"
   taxonomy:categories="news"
}}
  <article>
    {{ if featured_image }}
      <img src="{{ featured_image:url }}" alt="{{ title }}">
    {{ /if }}
    <h2>{{ title }}</h2>
    <p>{{ excerpt | truncate:150 }}</p>
  </article>
{{ /collection:posts }}
```

---

### Dates

**Date formatting:**
```html
<!-- HTML -->
<time>January 15, 2024</time>

<!-- Antlers -->
<time datetime="{{ date format='Y-m-d' }}">{{ date format="F j, Y" }}</time>
```

**Common formats:**
```antlers
{{ date format="Y-m-d" }}        {{-- 2024-01-15 --}}
{{ date format="F j, Y" }}       {{-- January 15, 2024 --}}
{{ date format="M d, Y" }}       {{-- Jan 15, 2024 --}}
{{ date format="d/m/Y" }}        {{-- 15/01/2024 --}}
{{ date format="l, F jS" }}      {{-- Monday, January 15th --}}
```

**Relative dates:**
```antlers
{{ date | relative }}            {{-- 2 days ago --}}
```

---

### Modifiers

**Text modifiers:**
```antlers
{{ title | upper }}              {{-- UPPERCASE --}}
{{ title | lower }}              {{-- lowercase --}}
{{ title | ucfirst }}            {{-- Capitalize first --}}
{{ title | slugify }}            {{-- url-friendly-slug --}}
{{ content | truncate:100 }}     {{-- Truncate to 100 chars --}}
{{ content | strip_tags }}       {{-- Remove HTML tags --}}
{{ content | nl2br }}            {{-- Newlines to <br> --}}
{{ content | markdown }}         {{-- Parse markdown --}}
```

**Array modifiers:**
```antlers
{{ items | count }}              {{-- Number of items --}}
{{ items | first }}              {{-- First item --}}
{{ items | last }}               {{-- Last item --}}
{{ items | shuffle }}            {{-- Randomize order --}}
{{ items | limit:5 }}            {{-- Limit to 5 items --}}
```

**Conditional modifiers:**
```antlers
{{ if items | count > 0 }}
  {{ items }}...{{ /items }}
{{ /if }}
```

---

## Partials Extraction

### When to Extract Partials

Extract repeated HTML blocks into partials:
- Header/footer
- Navigation
- Cards/list items
- Form elements
- Reusable sections

### Partial Syntax

**Creating partial:**
```antlers
{{-- resources/views/partials/_card.antlers.html --}}
<div class="card">
  <img src="{{ image:url }}" alt="{{ title }}">
  <h3>{{ title }}</h3>
  <p>{{ description }}</p>
  <a href="{{ url }}">Read More</a>
</div>
```

**Using partial:**
```antlers
{{ partial:partials/card }}

{{-- With parameters --}}
{{ partial:partials/card :title="post_title" :image="featured_image" }}

{{-- Inside loop --}}
{{ collection:posts }}
  {{ partial:partials/card }}
{{ /collection:posts }}
```

**Partial with slots:**
```antlers
{{-- resources/views/partials/_section.antlers.html --}}
<section class="section {{ class }}">
  <div class="container">
    {{ slot }}
  </div>
</section>

{{-- Usage --}}
{{ partial:partials/section class="bg-gray" }}
  <h2>Section Title</h2>
  <p>Section content here</p>
{{ /partial:partials/section }}
```

---

## Layouts

### Layout Structure

**Layout file:**
```antlers
{{-- resources/views/layout.antlers.html --}}
<!DOCTYPE html>
<html lang="{{ site:locale }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ title }} | {{ site_settings:site_name }}</title>
  {{ if seo_description }}
    <meta name="description" content="{{ seo_description }}">
  {{ /if }}
</head>
<body>
  {{ partial:partials/header }}
  
  <main>
    {{ template_content }}
  </main>
  
  {{ partial:partials/footer }}
</body>
</html>
```

**Page template:**
```antlers
{{-- resources/views/pages/about.antlers.html --}}
{{-- Automatically wrapped by layout --}}

<section class="hero">
  <h1>{{ title }}</h1>
  {{ if subtitle }}<p>{{ subtitle }}</p>{{ /if }}
</section>

<section class="content">
  {{ content }}
</section>
```

---

## Common Conversion Patterns

### Pattern 1: Simple Page

**Step 1: Check Blueprint**
```yaml
# resources/blueprints/collections/pages/default.yaml
fields:
  - handle: title
    field: { type: text, required: true }
  - handle: description
    field: { type: textarea }
  - handle: team_image
    field: { type: assets, max_files: 1 }
```

**Step 2: Scan HTML**
```html
<!DOCTYPE html>
<html>
<body>
  <main>
    <h1>About Us</h1>
    <p>We are a company that builds great websites...</p>
    <img src="team.jpg" alt="Our Team">
  </main>
</body>
</html>
```

**Step 3: Update Entry**
```yaml
# content/collections/pages/about.md
---
id: 550e8400-e29b-41d4-a716-446655440000
title: About Us
description: We are a company that builds great websites...
team_image: images/team.jpg
---
```

**Step 4: Create Template**
```antlers
{{-- resources/views/pages/default.antlers.html --}}

{{ if title }}
  <section class="page-header">
    <h1>{{ title }}</h1>
  </section>
{{ /if }}

{{ if description || team_image }}
  <section class="content">
    {{ if description }}
      <p>{{ description }}</p>
    {{ /if }}
    {{ if team_image }}
      <img src="{{ team_image:url }}" alt="{{ team_image:alt ?? 'Our Team' }}">
    {{ /if }}
  </section>
{{ /if }}
```

---

### Pattern 2: Blog Post

**Step 1: Check Blueprint**
```yaml
# resources/blueprints/collections/posts/post.yaml
fields:
  - handle: title
    field: { type: text, required: true }
  - handle: date
    field: { type: date, required: true }
  - handle: author
    field: { type: users, max_items: 1 }
  - handle: categories
    field: { type: terms, taxonomy: categories }
  - handle: featured_image
    field: { type: assets, max_files: 1 }
  - handle: content
    field: { type: bard }
```

**Step 2: Scan HTML**
```html
<article>
  <header>
    <h1>How to Build a Website</h1>
    <p>Posted on January 15, 2024 by John Doe</p>
    <span class="category">Tutorials</span>
  </header>
  <img src="featured.jpg" alt="Featured Image">
  <div class="content">
    <p>Content here...</p>
  </div>
</article>
```

**Step 3: Update Entry**
```yaml
# content/collections/posts/how-to-build-website.md
---
id: 660e8400-e29b-41d4-a716-446655440001
title: How to Build a Website
date: 2024-01-15
author:
  - user-john-doe-uuid
categories:
  - tutorials
featured_image: images/featured.jpg
content:
  -
    type: paragraph
    content:
      -
        type: text
        text: 'Content here...'
---
```

**Step 4: Create Template**
```antlers
{{-- resources/views/posts/post.antlers.html --}}

<article>
  <header>
    {{ if title }}<h1>{{ title }}</h1>{{ /if }}
    
    {{ if date || author }}
      <p>
        {{ if date }}Posted on {{ date format="F j, Y" }}{{ /if }}
        {{ if author }}
          {{ author }}
            by {{ name }}
          {{ /author }}
        {{ /if }}
      </p>
    {{ /if }}
    
    {{ if categories }}
      {{ categories }}
        <span class="category">{{ title }}</span>
      {{ /categories }}
    {{ /if }}
  </header>
  
  {{ if featured_image }}
    <img src="{{ featured_image:url }}" alt="{{ featured_image:alt ?? title }}">
  {{ /if }}
  
  {{ if content }}
    <div class="content">
      {{ content }}
    </div>
  {{ /if }}
</article>
```

---

### Pattern 3: Services Page with Replicator

**Step 1: Check Blueprint**
```yaml
# resources/blueprints/collections/pages/services.yaml
fields:
  - handle: title
    field: { type: text, required: true }
  - handle: subtitle
    field: { type: text }
  - handle: sections
    field:
      type: replicator
      sets:
        services_grid:
          fields:
            - handle: services
              field:
                type: grid
                fields:
                  - handle: icon
                    field: { type: assets, max_files: 1 }
                  - handle: title
                    field: { type: text }
                  - handle: description
                    field: { type: textarea }
        cta:
          fields:
            - handle: heading
              field: { type: text }
            - handle: button_text
              field: { type: text }
            - handle: button_link
              field: { type: link }
```

**Step 2: Scan HTML**
```html
<section class="hero">
  <h1>Our Services</h1>
  <p>We offer comprehensive solutions</p>
</section>
<section class="services-grid">
  <div class="service">
    <img src="service1.svg">
    <h3>Web Design</h3>
    <p>Beautiful, responsive websites</p>
  </div>
  <div class="service">
    <img src="service2.svg">
    <h3>Development</h3>
    <p>Custom web applications</p>
  </div>
</section>
<section class="cta">
  <h2>Ready to get started?</h2>
  <a href="/contact">Contact Us</a>
</section>
```

**Step 3: Update Entry**
```yaml
# content/collections/pages/services.md
---
id: 770e8400-e29b-41d4-a716-446655440002
title: Our Services
subtitle: We offer comprehensive solutions
sections:
  -
    type: services_grid
    services:
      -
        icon: icons/service1.svg
        title: Web Design
        description: Beautiful, responsive websites
      -
        icon: icons/service2.svg
        title: Development
        description: Custom web applications
  -
    type: cta
    heading: Ready to get started?
    button_text: Contact Us
    button_link: /contact
---
```

**Step 4: Create Template**
```antlers
{{-- resources/views/pages/services.antlers.html --}}

{{-- Hero Section --}}
{{ if title || subtitle }}
  <section class="hero">
    {{ if title }}<h1>{{ title }}</h1>{{ /if }}
    {{ if subtitle }}<p>{{ subtitle }}</p>{{ /if }}
  </section>
{{ /if }}

{{-- Dynamic Sections --}}
{{ if sections }}
  {{ sections }}
    
    {{ if type == 'services_grid' }}
      {{ if services }}
        <section class="services-grid">
          {{ services }}
            <div class="service">
              {{ if icon }}
                <img src="{{ icon:url }}" alt="">
              {{ /if }}
              {{ if title }}<h3>{{ title }}</h3>{{ /if }}
              {{ if description }}<p>{{ description }}</p>{{ /if }}
            </div>
          {{ /services }}
        </section>
      {{ /if }}
    {{ /if }}
    
    {{ if type == 'cta' }}
      {{ if heading || button_text }}
        <section class="cta">
          {{ if heading }}<h2>{{ heading }}</h2>{{ /if }}
          {{ if button_link }}
            <a href="{{ button_link }}">{{ button_text ?? 'Learn More' }}</a>
          {{ /if }}
        </section>
      {{ /if }}
    {{ /if }}
    
  {{ /sections }}
{{ /if }}
```

---

## Handling Missing Fields

When HTML content doesn't map to any existing blueprint field:

### Option 1: Flag for Blueprint Update

If the content should be editable, report it as a missing field:

```markdown
### Missing Fields (Blueprint Update Needed)
| Suggested Handle | Type | Content Source | Reason |
|------------------|------|----------------|--------|
| `founded_year` | integer | "Since 1999" text | Year should be editable |
| `team_photo` | assets | Hero background image | Image should be replaceable |
| `cta_secondary_link` | link | Second CTA button | Missing from blueprint |
```

**Suggested blueprint addition:**
```yaml
# Add to resources/blueprints/collections/pages/about.yaml
- handle: founded_year
  field:
    type: integer
    display: Founded Year
    instructions: Year the company was founded
```

### Option 2: Use Existing Field Creatively

Map to a similar existing field if appropriate:

| HTML Content | No Direct Field | Use Instead |
|--------------|-----------------|-------------|
| Company tagline | No `tagline` field | `subtitle` field |
| Secondary CTA | No `cta_secondary` | Add to `cta_links` array |
| Background color | No `bg_color` field | Part of `sections` replicator |

### Option 3: Hardcode if Truly Static

Some content should remain hardcoded (not editable):

- Copyright year (use `{{ now format="Y" }}`)
- Site name (use global: `{{ site_settings:name }}`)
- Navigation structure (use nav tag: `{{ nav:main }}`)
- Form (use form tag: `{{ form:contact }}`)

```antlers
{{-- These don't need entry fields --}}
<footer>
  <p>© {{ now format="Y" }} {{ site_settings:company_name }}</p>
</footer>
```

---

## Conversion Checklist

### Before Starting
- [ ] Detected single-site or multisite (`resources/sites.yaml`)
- [ ] Identified collection and entry file path
- [ ] **Read blueprint FIRST** to inventory available fields
- [ ] Created field inventory table with handles and types

### HTML Scanning
- [ ] Identified all dynamic content in HTML
- [ ] Created content extraction mapping table
- [ ] Mapped each content piece to blueprint field
- [ ] Flagged content that has no matching field

### Entry File Update
- [ ] Located correct entry `.md` file
- [ ] Preserved existing `id` (UUID)
- [ ] Populated all mapped fields with extracted values
- [ ] Used correct YAML syntax for each field type
- [ ] Verified asset paths are relative and correct

### Template Creation
- [ ] Created template in correct location
- [ ] **Wrapped ALL variables with `{{ if }}` conditionals**
- [ ] Used correct Antlers syntax for each field type
- [ ] Added fallback values where appropriate
- [ ] Wrapped sections with conditionals (show only if has content)

### Verification
- [ ] Template renders without errors
- [ ] All dynamic content displays correctly
- [ ] Empty fields don't cause errors or broken layouts
- [ ] Reported missing fields that need blueprint addition

---

## Quick Reference

### Variable Syntax (Always Wrapped)

| Content Type | Antlers Syntax (with conditional) |
|--------------|-----------------------------------|
| Text field | `{{ if title }}<h1>{{ title }}</h1>{{ /if }}` |
| Text with fallback | `{{ title ?? 'Default' }}` |
| Asset URL | `{{ if image }}<img src="{{ image:url }}">{{ /if }}` |
| Asset alt | `{{ image:alt ?? 'Default alt' }}` |
| Date | `{{ if date }}{{ date format="F j, Y" }}{{ /if }}` |
| Loop | `{{ if items }}{{ items }}...{{ /items }}{{ /if }}` |
| Global | `{{ if site_settings:company }}{{ site_settings:company }}{{ /if }}` |
| Nav | `{{ nav:handle }}...{{ /nav:handle }}` |
| Collection | `{{ collection:handle }}...{{ /collection:handle }}` |
| Partial | `{{ partial:path/name }}` |

### Conditional Patterns

| Check | Syntax |
|-------|--------|
| Field has value | `{{ if field }}...{{ /if }}` |
| Field is empty | `{{ if !field }}` or `{{ unless field }}` |
| Multiple fields (OR) | `{{ if field_a \|\| field_b }}` |
| Multiple fields (AND) | `{{ if field_a && field_b }}` |
| Equals value | `{{ if field == 'value' }}` |
| Not equals | `{{ if field != 'value' }}` |
| With fallback | `{{ field ?? 'fallback' }}` |
| Ternary | `{{ field ? 'yes' : 'no' }}` |

### Asset Field Properties

| Property | Access |
|----------|--------|
| URL | `{{ image:url }}` |
| Alt text | `{{ image:alt }}` |
| Width | `{{ image:width }}` |
| Height | `{{ image:height }}` |
| Filename | `{{ image:filename }}` |
| Extension | `{{ image:extension }}` |
| Size (bytes) | `{{ image:size }}` |
| MIME type | `{{ image:mime_type }}` |

### Entry File YAML Syntax

| Field Type | YAML Syntax |
|------------|-------------|
| Text | `field: Value here` |
| Text (special chars) | `field: 'Value with "quotes"'` |
| Multiline | `field: \|`<br>`  Line 1`<br>`  Line 2` |
| Array | `field:`<br>`  - item1`<br>`  - item2` |
| Single asset | `field: path/to/file.jpg` |
| Multiple assets | `field:`<br>`  - image1.jpg`<br>`  - image2.jpg` |
| Replicator | `field:`<br>`  -`<br>`    type: set_name`<br>`    handle: value` |
| Grid | `field:`<br>`  -`<br>`    col1: value`<br>`    col2: value` |

---

## Workflow Summary

```
1. CHECK BLUEPRINT FIRST
   └─ cat resources/blueprints/collections/{collection}/{blueprint}.yaml
   └─ List all field handles and types

2. SCAN HTML
   └─ Extract all dynamic content
   └─ Map to blueprint fields
   └─ Flag missing fields

3. UPDATE ENTRY .MD
   └─ Populate fields with extracted values
   └─ Use correct YAML syntax per field type

4. CREATE TEMPLATE
   └─ Replace HTML with Antlers variables
   └─ WRAP EVERYTHING with {{ if }} conditionals
   └─ Use fallbacks where needed

5. REPORT
   └─ List fields used
   └─ List missing fields needing blueprint update
   └─ Confirm all conditionals in place
```