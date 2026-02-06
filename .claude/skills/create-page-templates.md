# Create Page Templates

Create or update Antlers page templates that extend the base layout using hooks, partials, contextual body classes, and SEO Pro integration.

## Quick Start

1. Create template file in `resources/views/{collection}/{template}.antlers.html`
2. Add `{{ section:* }}` tags to inject into layout hooks
3. Use `{{ partial:* }}` for reusable components
4. Wrap all variables with `{{ if }}` conditionals

## Workflow

### Step 1: Choose Template Location

**Path:** `resources/views/{collection}/{template}.antlers.html`

**Common patterns:**
```
resources/views/
├── layout.antlers.html              # Global HTML wrapper (do NOT modify here)
├── partials/                        # Shared components
│   ├── _header.antlers.html
│   ├── _footer.antlers.html
│   └── _navigation.antlers.html
└── pages/                           # Pages collection templates
    ├── default.antlers.html
    ├── home.antlers.html
    ├── about.antlers.html
    └── contact.antlers.html
```

### Step 2: Understand Layout vs Template

| Aspect | Layout | Template |
|--------|--------|----------|
| **Purpose** | HTML shell/frame | Content-specific markup |
| **Contains** | `<head>`, global styles, shared components | Entry-specific content |
| **Reusability** | Used by all pages | Used by specific page types |
| **Entry Data** | Minimal (globals only) | Full access to entry variables |
| **Location** | `resources/views/layout.antlers.html` | `resources/views/{collection}/{template}.antlers.html` |

Templates are automatically wrapped by the layout. The template content renders where `{{ template_content }}` appears in the layout.

### Step 3: Use Layout Hooks

The base layout provides injection points via `{{ yield }}`/`{{ section }}` tags (native Statamic, no addons required).

**Important:** `{{ section }}`/`{{ yield }}` works like replacement (last definition wins), NOT accumulation. Combine everything in one section.

#### Head Hooks

```antlers
{{-- Add custom CSS --}}
{{ section:styles }}
<style>
    .custom-class { color: red; }
</style>
<link rel="stylesheet" href="/css/custom-page.css">
{{ /section:styles }}

{{-- Add fonts, preconnects, meta tags --}}
{{ section:head }}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
{{ /section:head }}
```

#### Body Hooks

```antlers
{{-- Add custom body classes (on top of auto-generated contextual classes) --}}
{{ section:body_class }}
custom-page featured-content
{{ /section:body_class }}

{{-- Override default styling classes (keeps contextual classes) --}}
{{ section:body_style_class }}
bg-white text-gray-900
{{ /section:body_style_class }}

{{-- Add body attributes (include leading space) --}}
{{ section:body_attrs }}
 data-page-type="landing" data-version="2"
{{ /section:body_attrs }}

{{-- Content after <body> opens (analytics, GTM noscript) --}}
{{ section:body_start }}
<noscript><iframe src="..."></iframe></noscript>
{{ /section:body_start }}
```

#### Content Hooks

```antlers
{{-- Override wrapper div classes --}}
{{ section:wrapper_class }}
container mx-auto px-4 max-w-7xl
{{ /section:wrapper_class }}

{{-- Header/navigation before main content --}}
{{ section:before_content }}
<header>
    {{ partial:partials/navigation }}
</header>
{{ /section:before_content }}

{{-- Footer after main content --}}
{{ section:after_content }}
<footer>
    {{ partial:partials/footer }}
</footer>
{{ /section:after_content }}
```

#### Script Hooks

```antlers
{{-- Custom JavaScript (combine all scripts in one section) --}}
{{ section:scripts }}
<script src="/js/analytics.js"></script>
<script src="/js/modal.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page loaded');
    });
</script>
{{ /section:scripts }}

{{-- Content before </body> --}}
{{ section:body_end }}
<!-- Final tracking scripts -->
{{ /section:body_end }}
```

### Step 4: Leverage Contextual Body Classes

The layout automatically adds WordPress-style classes to `<body>`:

| Context | Classes Generated |
|---------|-------------------|
| Homepage | `homepage locale-en` |
| Entry | `entry entry-{collection} entry-id-{id} slug-{slug} template-{template} layout-{layout} locale-{locale}` |
| Taxonomy Term | `term taxonomy-{taxonomy} term-{slug}` |
| Any page | `template-{current_template} layout-{current_layout} locale-{site:short_locale}` |

**CSS targeting examples:**
```css
.entry-blog .hero { background: blue; }
.homepage .cta { display: block; }
.slug-about-us { font-family: serif; }
.template-post { /* All pages using post template */ }
```

**Add custom classes for archive pages:**
```antlers
{{ section:body_class }}
archive archive-blog
{{ /section:body_class }}
```

### Step 5: Create Partials

**Path:** `resources/views/partials/_name.antlers.html`

**Naming:** Prefix with `_`, reference WITHOUT underscore: `{{ partial:partials/name }}`

**Basic partial:**
```antlers
{{-- resources/views/partials/_card.antlers.html --}}
<div class="card">
  {{ if image }}<img src="{{ image:url }}" alt="{{ title }}">{{ /if }}
  {{ if title }}<h3>{{ title }}</h3>{{ /if }}
  {{ if description }}<p>{{ description }}</p>{{ /if }}
</div>
```

**Using partials:**
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
  <p>Content here</p>
{{ /partial:partials/section }}
```

### Step 6: Replicator Set Partials

For pages using replicator-based page builders:

**Path:** `resources/views/sets/_setname.antlers.html`

**Main template:**
```antlers
{{ if sections }}
  {{ sections }}
    {{ partial src="sets/{type}" }}
  {{ /sections }}
{{ /if }}
```

**Example set partial:**
```antlers
{{-- resources/views/sets/_hero.antlers.html --}}
{{ if heading || lead || image }}
  <section class="hero">
    {{ if image }}
      <img src="{{ image:url }}" alt="{{ image:alt ?? heading }}">
    {{ /if }}
    <div class="hero-content">
      {{ if heading }}<h1>{{ heading }}</h1>{{ /if }}
      {{ if lead }}<p class="lead">{{ lead }}</p>{{ /if }}
    </div>
  </section>
{{ /if }}
```

## SEO Pro Integration

The base layout includes `{{ seo_pro:meta }}` which auto-generates meta tags, OG tags, Twitter cards, and JSON-LD.

**Add SEO field to blueprint:**
```yaml
-
  handle: seo
  field:
    type: seo_pro
    display: SEO
    localizable: true
```

**NEVER create custom SEO fields.** Always use SEO Pro addon.

## Complete Template Example

```antlers
{{-- resources/views/pages/about.antlers.html --}}

{{-- Add custom body classes --}}
{{ section:body_class }}
about-page
{{ /section:body_class }}

{{-- Override wrapper --}}
{{ section:wrapper_class }}
container mx-auto px-4 py-8 max-w-6xl
{{ /section:wrapper_class }}

{{-- Header --}}
{{ section:before_content }}
{{ partial:partials/header }}
{{ /section:before_content }}

{{-- Main content --}}
{{ if title || subtitle || hero_image }}
  <section class="hero">
    {{ if hero_image }}
      <img src="{{ hero_image:url }}" alt="{{ hero_image:alt ?? title }}">
    {{ /if }}
    {{ if title }}<h1>{{ title }}</h1>{{ /if }}
    {{ if subtitle }}<p class="subtitle">{{ subtitle }}</p>{{ /if }}
  </section>
{{ /if }}

{{ if content }}
  <section class="content">
    <div class="prose">{{ content }}</div>
  </section>
{{ /if }}

{{-- Footer --}}
{{ section:after_content }}
{{ partial:partials/footer }}
{{ /section:after_content }}

{{-- Page-specific scripts --}}
{{ section:scripts }}
<script src="/js/about-animations.js"></script>
{{ /section:scripts }}
```

## Available Hooks Reference

| Hook | Purpose | Type |
|------|---------|------|
| `{{ yield:styles }}` | Custom CSS | Head |
| `{{ yield:head }}` | Additional head content | Head |
| `{{ yield:body_class }}` | Add body classes | Body |
| `{{ yield:body_style_class }}` | Override styling classes | Body |
| `{{ yield:body_attrs }}` | Add body attributes | Body |
| `{{ yield:body_start }}` | After body opens | Body |
| `{{ yield:wrapper_class }}` | Override wrapper classes | Wrapper |
| `{{ yield:before_content }}` | Header/navigation | Content |
| `{{ yield:after_content }}` | Footer | Content |
| `{{ yield:scripts }}` | Custom JavaScript | Scripts |
| `{{ yield:body_end }}` | Before body closes | Scripts |

## Common Template Patterns

### Simple Content Page
```antlers
{{ section:wrapper_class }}
container mx-auto px-4 py-8 max-w-4xl
{{ /section:wrapper_class }}

{{ section:before_content }}
{{ partial:partials/header }}
{{ /section:before_content }}

<article class="prose">
  {{ if title }}<h1>{{ title }}</h1>{{ /if }}
  {{ if content }}{{ content }}{{ /if }}
</article>

{{ section:after_content }}
{{ partial:partials/footer }}
{{ /section:after_content }}
```

### Landing Page (Full-width)
```antlers
{{ section:body_style_class }}
bg-white
{{ /section:body_style_class }}

{{ section:wrapper_class }}
w-full min-h-screen
{{ /section:wrapper_class }}

{{ section:styles }}
<style>
    .hero { min-height: 100vh; display: flex; align-items: center; }
</style>
{{ /section:styles }}

<div class="hero">
  {{ if title }}<h1>{{ title }}</h1>{{ /if }}
  {{ if cta_link }}
    <a href="{{ cta_link }}" class="btn">{{ cta_text ?? 'Learn More' }}</a>
  {{ /if }}
</div>
```

### Blog Post
```antlers
{{ section:head }}
<meta property="article:published_time" content="{{ date format="c" }}">
{{ /section:head }}

<article class="prose lg:prose-xl">
  {{ if title }}<h1>{{ title }}</h1>{{ /if }}
  {{ if date || author }}
    <div class="meta">
      {{ if date }}{{ date format="F j, Y" }}{{ /if }}
      {{ if author }}{{ author }}by {{ name }}{{ /author }}{{ /if }}
    </div>
  {{ /if }}
  {{ if content }}<div class="content">{{ content }}</div>{{ /if }}
</article>
```

## Boundaries

- Do NOT modify `layout.antlers.html` — Use hooks to extend it
- Do NOT create custom SEO fields — Use SEO Pro addon
- Sections do NOT accumulate — Last definition wins, combine in one section
- Template content renders at `{{ template_content }}` in layout

## Accuracy Checks

- Template file extension is `.antlers.html`
- Partial filenames prefixed with `_`, referenced without it
- All non-required variables wrapped with `{{ if }}` conditionals
- `{{ section:name }}` in template corresponds to `{{ yield:name }}` in layout
- Set partials go in `resources/views/sets/`, not `partials/`

## Quick Reference

| Task | Syntax |
|------|--------|
| Inject into layout | `{{ section:hook_name }}...{{ /section:hook_name }}` |
| Include partial | `{{ partial:partials/name }}` |
| Partial with params | `{{ partial:partials/name :title="var" }}` |
| Partial with slot | `{{ partial:partials/name }}content{{ /partial:partials/name }}` |
| Replicator set partials | `{{ partial src="sets/{type}" }}` |
| Conditional variable | `{{ if field }}{{ field }}{{ /if }}` |
| Fallback value | `{{ field ?? 'default' }}` |
| Date format | `{{ date format="F j, Y" }}` |
| Asset URL | `{{ image:url }}` |
| Global access | `{{ site_settings:field_name }}` |
| Nav loop | `{{ nav:header }}...{{ /nav:header }}` |
| Collection listing | `{{ collection:posts limit="10" }}...{{ /collection:posts }}` |
