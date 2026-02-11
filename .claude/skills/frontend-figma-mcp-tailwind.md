---
name: figma-to-tailwind-mcp
description: Convert Figma designs to production-ready HTML with Tailwind CSS using the Figma MCP server. Use when the user asks to build, code, or convert a Figma design into HTML/Tailwind, provides a Figma file URL or frame link, or asks to "implement this design." Requires Figma MCP connection. Triggers on phrases like "convert this Figma," "build this from Figma," "code this design," "Figma to HTML," or when a Figma URL is shared with a frontend build request.
---

# Figma to Tailwind HTML (MCP Workflow)

## Prerequisites
- Figma MCP server connected
- User provides a Figma file URL, frame URL, or node ID

## Workflow

### Step 1: Extract Design Data from Figma

Use the Figma MCP tools to read the design file. Extract in this order:

1. **Design tokens** — colors, typography (font family, sizes, weights, line heights), spacing scale, border radii, shadows
2. **Layout structure** — frame hierarchy, auto-layout directions (→ flex), constraints, responsive breakpoints
3. **Component inventory** — list all distinct components/sections on the page

Compile tokens into a reference block:

```
Colors: primary=#XXXX, secondary=#XXXX, text=#XXXX, bg=#XXXX, ...
Fonts: heading=Inter/700, body=Inter/400
Spacing: section-gap=80px, card-gap=24px, content-padding=16px/24px/48px
Radii: sm=8px, md=12px, lg=16px
```

### Step 2: Plan Component Breakdown

Before writing any code, list the sections/components to build:

```
1. Navbar (logo + nav links + CTA button)
2. Hero (heading + subtext + CTA + hero image)
3. Features grid (icon + title + description × N)
4. Testimonials (avatar + quote + name)
5. CTA section
6. Footer
```

Build each component individually, then assemble.

### Step 3: Build Each Component

For each component, follow this pattern:

1. Read the Figma node/frame for that component
2. Map Figma properties to Tailwind:
   - Auto-layout horizontal → `flex flex-row`
   - Auto-layout vertical → `flex flex-col`
   - Gap → `gap-[Npx]`
   - Padding → `p-[N]` or `px-[N] py-[N]`
   - Fill container → `w-full`
   - Fixed width → `w-[Npx]`
   - Text styles → `text-[size] font-[weight] leading-[lh] text-[#color]`
   - Border radius → `rounded-[value]`
   - Shadows → `shadow-[custom]` or Tailwind preset
   - Background → `bg-[#color]`
3. Write semantic HTML with Tailwind classes
4. Use `max-w-7xl mx-auto` for content containers
5. Use placeholder images: `https://placehold.co/WxH` or Unsplash

### Step 4: Responsive Adaptation

Figma designs are typically desktop-only. Add responsive behavior:

- Default styles = mobile
- `md:` prefix = tablet (768px)
- `lg:` prefix = desktop (1024px)
- Convert fixed widths to fluid: `w-full lg:w-[fixed]`
- Stack horizontal layouts on mobile: `flex-col lg:flex-row`
- Adjust text sizes: `text-2xl lg:text-4xl`
- Adjust spacing: `p-4 lg:p-8`

### Step 5: Assemble & Output

Combine all components into a single HTML file:

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
                        primary: '#EXTRACTED_COLOR',
                        secondary: '#EXTRACTED_COLOR',
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

### Step 6: Visual QA

After generating, review against the Figma design:
- Re-read the Figma nodes to verify colors, spacing, and typography match
- Check all text content matches the design
- Verify image aspect ratios match Figma frames
- Confirm hover/active states if specified in Figma

## Figma-to-Tailwind Mapping Reference

| Figma Property         | Tailwind Equivalent                    |
|------------------------|----------------------------------------|
| Auto Layout Horizontal | `flex flex-row`                        |
| Auto Layout Vertical   | `flex flex-col`                        |
| Gap                    | `gap-[N]`                              |
| Padding                | `p-[N]`, `px-[N] py-[N]`              |
| Fill Container         | `w-full` / `flex-1`                    |
| Hug Contents           | `w-fit`                                |
| Fixed Size             | `w-[Npx] h-[Npx]`                     |
| Align Items            | `items-start/center/end`               |
| Justify Content        | `justify-start/center/between/end`     |
| Absolute Position      | `absolute top-[N] left-[N]`           |
| Opacity                | `opacity-[N]`                          |
| Blend Mode             | `mix-blend-[mode]`                     |
| Clip Content           | `overflow-hidden`                      |
| Corner Radius          | `rounded-[N]`                          |
| Stroke                 | `border border-[#color]`              |
| Drop Shadow            | `shadow-[custom]` or `drop-shadow-lg`  |
| Background Blur        | `backdrop-blur-[N]`                    |

## Common Pitfalls

- **Don't ignore Figma's spacing values** — use exact values via `[Npx]` syntax when Tailwind defaults don't match
- **Don't skip font imports** — always include Google Fonts link for non-system fonts
- **Don't hardcode widths on containers** — use max-width + auto margins for centering
- **Don't forget the viewport meta tag** — responsive won't work without it
- **Check for overlapping/absolute elements** — Figma allows free positioning; convert to relative/flex where possible