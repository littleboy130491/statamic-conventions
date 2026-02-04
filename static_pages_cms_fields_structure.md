# Static Pages CMS Structure — Flexible Approach

## Overview

For static pages where each page has a unique template, choose between:

| Approach | Best For | Pros | Cons |
|----------|----------|------|------|
| **Page-specific blueprints** | Fixed layouts (About, Contact) | Exact field mapping, clear for editors | More blueprints to maintain |
| **Replicator page builder** | Flexible layouts | One blueprint, rearrangeable blocks | Can be complex, more sets to define |
| **Hybrid** | Most projects | Balance of structure and flexibility | Requires planning upfront |

**Recommendation:** Start with page-specific blueprints. Add replicator sections only where flexibility is needed.

---

## Approach 1: Page-Specific Blueprints

Each static page gets its own blueprint that mirrors the HTML structure exactly.

### When to Use

- Page layout is fixed and won't change
- Content editors need clear, labeled fields
- Design-to-CMS mapping should be 1:1

### File Structure

```
resources/blueprints/collections/pages/
├── home.yaml           # Homepage blueprint
├── about.yaml          # About page blueprint
├── services.yaml       # Services page blueprint
├── contact.yaml        # Contact page blueprint
└── default.yaml        # Fallback for simple pages
```

### Example: About Page Blueprint

**HTML structure to support:**
```html
<section class="hero">
  <h1>About Us</h1>
  <p class="lead">Building trust since 1999</p>
  <img src="hero.jpg" alt="Our team">
</section>

<section class="story">
  <h2>Our Story</h2>
  <div class="content">...</div>
  <img src="founder.jpg" alt="Founder">
</section>

<section class="values">
  <h2>Our Values</h2>
  <div class="value">
    <img src="icon1.svg">
    <h3>Quality</h3>
    <p>Description...</p>
  </div>
  <!-- more values -->
</section>

<section class="team">
  <h2>Meet the Team</h2>
  <div class="member">...</div>
</section>

<section class="cta">
  <h2>Ready to work with us?</h2>
  <a href="/contact">Get in Touch</a>
</section>
```

**Blueprint:**
```yaml
# resources/blueprints/collections/pages/about.yaml
title: About Page
sections:
  hero:
    display: Hero Section
    fields:
      -
        handle: title
        field:
          type: text
          display: Page Title
          required: true
      -
        handle: hero_lead
        field:
          type: text
          display: Hero Lead Text
          instructions: Subtitle below the main title
      -
        handle: hero_image
        field:
          type: assets
          display: Hero Image
          max_files: 1
          
  story:
    display: Our Story
    fields:
      -
        handle: story_heading
        field:
          type: text
          display: Section Heading
          default: Our Story
      -
        handle: story_content
        field:
          type: bard
          display: Story Content
          buttons:
            - h3
            - bold
            - italic
            - unorderedlist
            - anchor
      -
        handle: story_image
        field:
          type: assets
          display: Story Image
          max_files: 1
      -
        handle: story_image_position
        field:
          type: button_group
          display: Image Position
          options:
            left: Left
            right: Right
          default: right
          
  values:
    display: Our Values
    fields:
      -
        handle: values_heading
        field:
          type: text
          display: Section Heading
          default: Our Values
      -
        handle: values
        field:
          type: grid
          display: Values
          fields:
            -
              handle: icon
              field:
                type: assets
                display: Icon
                max_files: 1
            -
              handle: title
              field:
                type: text
                display: Title
            -
              handle: description
              field:
                type: textarea
                display: Description
                
  team:
    display: Team Section
    fields:
      -
        handle: team_heading
        field:
          type: text
          display: Section Heading
          default: Meet the Team
      -
        handle: team_members
        field:
          type: entries
          display: Team Members
          collections:
            - team
          instructions: Select team members to display
          
  cta:
    display: Call to Action
    fields:
      -
        handle: cta_heading
        field:
          type: text
          display: CTA Heading
      -
        handle: cta_button_text
        field:
          type: text
          display: Button Text
          default: Get in Touch
      -
        handle: cta_button_link
        field:
          type: link
          display: Button Link
```

### Entry File

```yaml
# content/collections/pages/about.md
---
id: 550e8400-e29b-41d4-a716-446655440000
title: About Us
blueprint: about
hero_lead: Building trust since 1999
hero_image: images/hero-about.jpg
story_heading: Our Story
story_content:
  -
    type: paragraph
    content:
      -
        type: text
        text: 'We started in a small garage in 1999...'
story_image: images/founder.jpg
story_image_position: right
values_heading: Our Values
values:
  -
    icon: icons/quality.svg
    title: Quality
    description: We never compromise on quality
  -
    icon: icons/integrity.svg
    title: Integrity
    description: Honesty in everything we do
  -
    icon: icons/innovation.svg
    title: Innovation
    description: Always pushing boundaries
team_heading: Meet the Team
team_members:
  - team-member-uuid-1
  - team-member-uuid-2
  - team-member-uuid-3
cta_heading: Ready to work with us?
cta_button_text: Get in Touch
cta_button_link: entry::contact-page-uuid
---
```

---

## Approach 2: Replicator Page Builder

One flexible blueprint with modular content blocks.

### When to Use

- Multiple pages share similar section types
- Content editors need to add/remove/reorder sections
- Design system has defined components

### Blueprint with Section Sets

```yaml
# resources/blueprints/collections/pages/flexible.yaml
title: Flexible Page
sections:
  main:
    display: Page Content
    fields:
      -
        handle: title
        field:
          type: text
          display: Page Title
          required: true
      -
        handle: sections
        field:
          type: replicator
          display: Page Sections
          collapse: true
          previews: true
          sets:
            hero:
              display: Hero Section
              icon: home
              fields:
                -
                  handle: heading
                  field:
                    type: text
                    display: Heading
                -
                  handle: lead
                  field:
                    type: text
                    display: Lead Text
                -
                  handle: image
                  field:
                    type: assets
                    display: Background Image
                    max_files: 1
                -
                  handle: buttons
                  field:
                    type: grid
                    display: Buttons
                    fields:
                      -
                        handle: text
                        field: { type: text, display: Text }
                      -
                        handle: link
                        field: { type: link, display: Link }
                      -
                        handle: style
                        field:
                          type: select
                          display: Style
                          options:
                            primary: Primary
                            secondary: Secondary
                            outline: Outline
                            
            text_block:
              display: Text Block
              icon: pilcrow
              fields:
                -
                  handle: heading
                  field:
                    type: text
                    display: Heading
                -
                  handle: content
                  field:
                    type: bard
                    display: Content
                    
            text_with_image:
              display: Text with Image
              icon: media
              fields:
                -
                  handle: heading
                  field:
                    type: text
                    display: Heading
                -
                  handle: content
                  field:
                    type: bard
                    display: Content
                -
                  handle: image
                  field:
                    type: assets
                    display: Image
                    max_files: 1
                -
                  handle: image_position
                  field:
                    type: button_group
                    display: Image Position
                    options:
                      left: Left
                      right: Right
                    default: right
                    
            features_grid:
              display: Features Grid
              icon: layout-grid
              fields:
                -
                  handle: heading
                  field:
                    type: text
                    display: Section Heading
                -
                  handle: columns
                  field:
                    type: button_group
                    display: Columns
                    options:
                      '2': '2 Columns'
                      '3': '3 Columns'
                      '4': '4 Columns'
                    default: '3'
                -
                  handle: features
                  field:
                    type: grid
                    display: Features
                    fields:
                      -
                        handle: icon
                        field: { type: assets, display: Icon, max_files: 1 }
                      -
                        handle: title
                        field: { type: text, display: Title }
                      -
                        handle: description
                        field: { type: textarea, display: Description }
                      -
                        handle: link
                        field: { type: link, display: Link }
                        
            image_gallery:
              display: Image Gallery
              icon: images
              fields:
                -
                  handle: heading
                  field:
                    type: text
                    display: Section Heading
                -
                  handle: images
                  field:
                    type: assets
                    display: Images
                    mode: grid
                -
                  handle: columns
                  field:
                    type: button_group
                    display: Columns
                    options:
                      '2': '2 Columns'
                      '3': '3 Columns'
                      '4': '4 Columns'
                    default: '3'
                    
            testimonials:
              display: Testimonials
              icon: quote
              fields:
                -
                  handle: heading
                  field:
                    type: text
                    display: Section Heading
                -
                  handle: testimonials
                  field:
                    type: grid
                    display: Testimonials
                    fields:
                      -
                        handle: quote
                        field: { type: textarea, display: Quote }
                      -
                        handle: author
                        field: { type: text, display: Author Name }
                      -
                        handle: role
                        field: { type: text, display: Role/Company }
                      -
                        handle: photo
                        field: { type: assets, display: Photo, max_files: 1 }
                        
            cta:
              display: Call to Action
              icon: megaphone
              fields:
                -
                  handle: heading
                  field:
                    type: text
                    display: Heading
                -
                  handle: text
                  field:
                    type: textarea
                    display: Supporting Text
                -
                  handle: button_text
                  field:
                    type: text
                    display: Button Text
                -
                  handle: button_link
                  field:
                    type: link
                    display: Button Link
                -
                  handle: background
                  field:
                    type: button_group
                    display: Background
                    options:
                      light: Light
                      dark: Dark
                      primary: Primary Color
                    default: primary
                    
            accordion:
              display: FAQ / Accordion
              icon: list
              fields:
                -
                  handle: heading
                  field:
                    type: text
                    display: Section Heading
                -
                  handle: items
                  field:
                    type: grid
                    display: Items
                    fields:
                      -
                        handle: question
                        field: { type: text, display: Question }
                      -
                        handle: answer
                        field: { type: bard, display: Answer }
                        
            form:
              display: Form Section
              icon: form
              fields:
                -
                  handle: heading
                  field:
                    type: text
                    display: Section Heading
                -
                  handle: text
                  field:
                    type: textarea
                    display: Introduction Text
                -
                  handle: form_handle
                  field:
                    type: form
                    display: Form
                    max_items: 1
                    
            spacer:
              display: Spacer
              icon: minus
              fields:
                -
                  handle: size
                  field:
                    type: button_group
                    display: Size
                    options:
                      sm: Small
                      md: Medium
                      lg: Large
                      xl: Extra Large
                    default: md
```

### Section Partials

Create a partial for each set type:

```
resources/views/
├── pages/
│   └── flexible.antlers.html
└── sets/
    ├── _hero.antlers.html
    ├── _text_block.antlers.html
    ├── _text_with_image.antlers.html
    ├── _features_grid.antlers.html
    ├── _image_gallery.antlers.html
    ├── _testimonials.antlers.html
    ├── _cta.antlers.html
    ├── _accordion.antlers.html
    ├── _form.antlers.html
    └── _spacer.antlers.html
```

**Main template:**
```antlers
{{-- resources/views/pages/flexible.antlers.html --}}

{{ if title }}
  {{-- Optional: Page title could be hidden if hero has heading --}}
{{ /if }}

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
  <section class="hero {{ if image }}has-background{{ /if }}">
    {{ if image }}
      <div class="hero-background">
        <img src="{{ image:url }}" alt="{{ image:alt ?? heading }}">
      </div>
    {{ /if }}
    
    <div class="hero-content">
      {{ if heading }}<h1>{{ heading }}</h1>{{ /if }}
      {{ if lead }}<p class="lead">{{ lead }}</p>{{ /if }}
      
      {{ if buttons }}
        <div class="hero-buttons">
          {{ buttons }}
            {{ if link }}
              <a href="{{ link }}" class="btn btn-{{ style ?? 'primary' }}">
                {{ text ?? 'Learn More' }}
              </a>
            {{ /if }}
          {{ /buttons }}
        </div>
      {{ /if }}
    </div>
  </section>
{{ /if }}
```

```antlers
{{-- resources/views/sets/_features_grid.antlers.html --}}

{{ if features }}
  <section class="features-section">
    {{ if heading }}<h2>{{ heading }}</h2>{{ /if }}
    
    <div class="features-grid columns-{{ columns ?? '3' }}">
      {{ features }}
        <div class="feature">
          {{ if icon }}
            <div class="feature-icon">
              <img src="{{ icon:url }}" alt="">
            </div>
          {{ /if }}
          {{ if title }}<h3>{{ title }}</h3>{{ /if }}
          {{ if description }}<p>{{ description }}</p>{{ /if }}
          {{ if link }}
            <a href="{{ link }}" class="feature-link">Learn more →</a>
          {{ /if }}
        </div>
      {{ /features }}
    </div>
  </section>
{{ /if }}
```

---

## Approach 3: Hybrid (Recommended)

Combine page-specific fields with flexible sections.

### Blueprint Structure

```yaml
# resources/blueprints/collections/pages/about.yaml
title: About Page
sections:
  hero:
    display: Hero
    fields:
      # Fixed hero fields specific to About page
      -
        handle: title
        field: { type: text, required: true }
      -
        handle: hero_lead
        field: { type: text }
      -
        handle: hero_image
        field: { type: assets, max_files: 1 }
        
  main_content:
    display: Main Content
    fields:
      # Fixed fields for About page structure
      -
        handle: story_heading
        field: { type: text, default: 'Our Story' }
      -
        handle: story_content
        field: { type: bard }
      -
        handle: story_image
        field: { type: assets, max_files: 1 }
        
  flexible:
    display: Additional Sections
    fields:
      # Flexible sections for optional content
      -
        handle: additional_sections
        field:
          type: replicator
          display: Additional Sections
          instructions: Add optional sections below the main content
          sets:
            # Import from fieldset or define inline
            testimonials:
              display: Testimonials
              fields: ...
            cta:
              display: Call to Action
              fields: ...
            team_grid:
              display: Team Grid
              fields: ...
```

---

## Recommended Set Library

Define these commonly used sets for maximum flexibility:

### Content Sets

| Set | Purpose | Key Fields |
|-----|---------|------------|
| `hero` | Page header with title, image, CTA | heading, lead, image, buttons |
| `text_block` | Simple text section | heading, content |
| `text_with_image` | Text + image side by side | heading, content, image, position |
| `text_with_video` | Text + video embed | heading, content, video_url, position |

### Grid Sets

| Set | Purpose | Key Fields |
|-----|---------|------------|
| `features_grid` | Icon + title + description grid | heading, columns, features[] |
| `cards_grid` | Linked cards with images | heading, columns, cards[] |
| `logo_grid` | Client/partner logos | heading, logos[] |
| `stats_grid` | Numbers/statistics | heading, stats[] |

### Media Sets

| Set | Purpose | Key Fields |
|-----|---------|------------|
| `image_gallery` | Image grid/masonry | heading, images[], columns |
| `video_embed` | Single video | heading, video_url, caption |
| `image_full_width` | Full-width image | image, caption, parallax |

### Social Proof Sets

| Set | Purpose | Key Fields |
|-----|---------|------------|
| `testimonials` | Customer quotes | heading, testimonials[] |
| `reviews` | Star ratings + text | heading, reviews[] |
| `case_study_preview` | Featured case study | heading, case_study_entry |

### Conversion Sets

| Set | Purpose | Key Fields |
|-----|---------|------------|
| `cta` | Call to action banner | heading, text, button, background |
| `cta_split` | CTA with image | heading, text, button, image |
| `newsletter` | Email signup | heading, text, form_handle |
| `contact_form` | Contact form section | heading, text, form_handle |

### Utility Sets

| Set | Purpose | Key Fields |
|-----|---------|------------|
| `spacer` | Vertical spacing | size (sm/md/lg/xl) |
| `divider` | Horizontal line | style, width |
| `anchor` | Scroll anchor point | anchor_id |

---

## Decision Guide

```
Does this page have a FIXED, UNIQUE layout?
│
├─ YES: Use page-specific blueprint
│  │
│  └─ Does it need some flexible sections?
│     ├─ YES: Hybrid (fixed fields + replicator for extras)
│     └─ NO: Pure page-specific blueprint
│
└─ NO: Use flexible page builder
   │
   └─ Is this a one-off page or pattern for multiple pages?
      ├─ One-off: Page-specific with replicator
      └─ Pattern: Shared "flexible" blueprint
```

### Examples by Page Type

| Page | Recommended Approach | Why |
|------|---------------------|-----|
| **Homepage** | Hybrid | Fixed hero + flexible sections for promotions |
| **About** | Page-specific | Fixed structure (story, team, values) |
| **Services** | Flexible | Services may change, need reordering |
| **Contact** | Page-specific | Fixed form + map + info structure |
| **Blog Index** | Collection listing | Not a static page |
| **Landing Pages** | Flexible | Marketing needs to change often |
| **Legal (Privacy, Terms)** | Simple (title + bard) | Just needs rich text |

---

## Quick Setup Checklist

### For Page-Specific Blueprint

1. [ ] Analyze HTML structure
2. [ ] Create blueprint with tabs matching page sections
3. [ ] Define fields matching each HTML element
4. [ ] Create template with conditionals
5. [ ] Populate entry with HTML content

### For Flexible Page Builder

1. [ ] Define set library (start with 5-8 common sets)
2. [ ] Create set partials in `views/sets/`
3. [ ] Create flexible blueprint importing sets
4. [ ] Create main template that loops sections
5. [ ] Test with sample content

### For Hybrid

1. [ ] Identify fixed sections (always present)
2. [ ] Identify flexible sections (optional/reorderable)
3. [ ] Create blueprint with fixed tabs + flexible replicator
4. [ ] Create template handling both