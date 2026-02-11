---
name: screenshot-to-tailwind
description: Convert design screenshots or mockup images into production-ready HTML with Tailwind CSS. Use when the user uploads a screenshot, image, or mockup of a design and asks to build, code, replicate, or convert it into HTML/Tailwind. Does NOT require Figma access. Triggers on phrases like "build this from the screenshot," "convert this image to HTML," "replicate this design," "code this UI," or when an image is uploaded alongside a frontend build request.
---

# Screenshot to Tailwind HTML (Image Workflow)

## Prerequisites
- User provides one or more design screenshots (PNG, JPG, or PDF)
- Vision capability to analyze the image

## Workflow

### Step 1: Analyze the Design Image

Examine the screenshot carefully and extract:

1. **Color palette** — identify all distinct colors (backgrounds, text, accents, borders). Estimate hex values as accurately as possible.
2. **Typography** — identify font styles (serif/sans-serif/mono), approximate sizes, weights, and hierarchy (H1 > H2 > body > caption).
3. **Layout structure** — identify grid system, columns, flex directions, alignment, spacing rhythm.
4. **Components** — list every distinct section and UI element visible.
5. **Imagery** — note image placements, aspect ratios, and whether they're decorative or content images.

Compile a token summary and confirm with the user:

```
Colors I detected:
- Primary: ~#2563EB (blue)
- Background: ~#FFFFFF
- Text: ~#1F2937
- Accent: ~#10B981

Typography: Appears to be Inter or similar sans-serif
- Headings: ~36-48px bold
- Body: ~16-18px regular

Layout: 12-column grid, ~1200px max-width, ~24px gutters
```

Ask: "Do these look correct? Any specific fonts, colors, or brand guidelines I should use instead?"

### Step 2: Plan Component Breakdown

List all sections top-to-bottom:

```
1. Navbar
2. Hero section
3. Features grid
4. ...
```

Build each component individually.

### Step 3: Build Each Component

For each component:

1. Study the relevant portion of the screenshot
2. Determine the layout model:
   - Side by side → `flex flex-row` or `grid grid-cols-N`
   - Stacked → `flex flex-col`
   - Cards → `grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3`
3. Estimate spacing by visual proportion:
   - Tight spacing (~8-12px) → `gap-2` / `gap-3` / `p-2` / `p-3`
   - Normal spacing (~16-24px) → `gap-4` / `gap-6` / `p-4` / `p-6`
   - Generous spacing (~32-48px) → `gap-8` / `gap-12` / `p-8` / `p-12`
   - Section spacing (~64-96px) → `py-16` / `py-24`
4. Write semantic HTML with Tailwind classes
5. Use placeholder images: `https://placehold.co/WxH`

### Step 4: Responsive Adaptation

Screenshots are usually one breakpoint. Infer responsive behavior:

- Default (mobile-first) → single column, stacked layout, smaller text
- `md:` (tablet 768px) → partial grid, medium text
- `lg:` (desktop 1024px) → full layout matching the screenshot

Common patterns:
- `flex-col lg:flex-row` — stack on mobile, side-by-side on desktop
- `grid-cols-1 md:grid-cols-2 lg:grid-cols-3` — responsive card grid
- `text-2xl lg:text-5xl` — scale headings
- `hidden lg:block` / `lg:hidden` — show/hide elements per breakpoint
- `px-4 lg:px-0` — edge padding on mobile, none on centered desktop

### Step 5: Assemble & Output

Single HTML file structure:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[Page Title]</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#DETECTED_COLOR',
                        secondary: '#DETECTED_COLOR',
                    },
                    fontFamily: {
                        heading: ['Inter', 'sans-serif'],
                        body: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=[Font]&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Components here -->
</body>
</html>
```

### Step 6: Visual QA — Side-by-Side Comparison

After generating the HTML:

1. Render/preview the output
2. Compare with the original screenshot
3. Check these common mismatches:
   - **Spacing rhythm** — is vertical spacing between sections consistent?
   - **Color accuracy** — do colors look right or shifted?
   - **Text hierarchy** — are heading sizes proportionally correct?
   - **Alignment** — are elements centered/aligned as in the design?
   - **Image proportions** — do placeholders match the original aspect ratios?
4. Ask user: "Here's the result. How does it compare to your design? What needs adjusting?"
5. Iterate based on feedback

## Spacing Estimation Guide

When exact values aren't available, use visual proportion rules:

| Visual Impression      | Tailwind Value       | Pixels (approx) |
|------------------------|----------------------|------------------|
| Barely visible gap     | `gap-1` / `p-1`     | 4px              |
| Tight                  | `gap-2` / `p-2`     | 8px              |
| Compact                | `gap-3` / `p-3`     | 12px             |
| Standard               | `gap-4` / `p-4`     | 16px             |
| Comfortable            | `gap-6` / `p-6`     | 24px             |
| Spacious               | `gap-8` / `p-8`     | 32px             |
| Wide                   | `gap-12` / `p-12`   | 48px             |
| Section gap            | `py-16` / `py-20`   | 64-80px          |
| Large section gap      | `py-24` / `py-32`   | 96-128px         |

## Common Pitfalls

- **Don't guess colors when you can ask** — always confirm brand colors with the user; screenshots can be color-shifted
- **Don't assume the font** — ask the user or note "appears to be X" and let them correct
- **Don't ignore subtle details** — borders, shadows, hover states, and micro-interactions matter
- **Don't build the entire page in one pass** — component-by-component produces better results
- **Don't forget mobile** — even if the screenshot is desktop-only, always add responsive classes
- **Watch for overlapping text on images** — use proper positioning (`relative`/`absolute`) or overlay gradients