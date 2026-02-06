---
name: statamic-create-forms
description: Create or update Statamic forms, form blueprints, email notifications, and frontend templates; use when setting up form config or submissions handling.
---

# Statamic Create Forms

## Quick start
- Create form config in `resources/forms/{handle}.yaml`.
- Create form blueprint in `resources/blueprints/forms/{handle}.yaml`.
- Add email templates in `resources/views/emails/` if needed.

## Workflow
1. Form config:
   - Required: `title`
   - Optional: `honeypot`, `store`, `email` notifications
2. Form blueprint:
   - Use top-level `fields:` list.
   - Add validation rules per field.
3. Templates:
   - Use `{{ form:handle }}` tag in page templates or partials.
   - Provide success and error handling.

## Notes
- Avoid using `message` as a field handle (Laravel reserved word).
- Use `files="true"` for file uploads.

## References
- See `references/forms.md` for condensed config and template patterns.

