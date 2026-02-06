# Create Forms

Create or update Statamic form configuration, blueprints, email notifications, and templates.

## Quick Start

1. Create form config: `resources/forms/{handle}.yaml`
2. Create form blueprint: `resources/blueprints/forms/{handle}.yaml`
3. Create email templates (optional)
4. Add form to page template

## Form Config

**Path:** `resources/forms/{handle}.yaml`

```yaml
title: Contact Form
honeypot: website
store: true
email:
  -
    to: admin@example.com
    from: "{{ email }}"
    reply_to: "{{ email }}"
    subject: "New contact form submission"
    html: emails/contact
    text: emails/contact-text
  -
    to: "{{ email }}"
    from: noreply@example.com
    subject: "Thanks for contacting us"
    html: emails/contact-confirmation
```

### Config Fields

| Field | Description |
|-------|-------------|
| `title` | Display name in CP (required) |
| `honeypot` | Honeypot field name for spam protection |
| `store` | Boolean, store submissions (default: true) |
| `email` | Array of email configurations |

### Email Configuration

```yaml
email:
  -
    to: admin@example.com           # Recipient (can use form vars)
    from: "{{ email }}"             # Sender
    reply_to: "{{ email }}"         # Reply-to address
    subject: "Message from {{ name }}"  # Subject line
    html: emails/contact            # HTML template path
    text: emails/contact-text       # Plain text template
    attachments: true               # Attach uploaded files
```

**Multiple Recipients:**
```yaml
to:
  - admin@example.com
  - support@example.com
```

## Form Blueprint

**Path:** `resources/blueprints/forms/{handle}.yaml`

**Blueprint handle MUST match form handle.**

```yaml
title: Contact Form
fields:
  -
    handle: name
    field:
      type: text
      display: Name
      validate:
        - required
  -
    handle: email
    field:
      type: text
      input_type: email
      display: Email
      validate:
        - required
        - email
  -
    handle: subject
    field:
      type: select
      display: Subject
      options:
        general: General Inquiry
        support: Support
        sales: Sales
      validate:
        - required
  -
    handle: message
    field:
      type: textarea
      display: Message
      validate:
        - required
```

### Available Form Fieldtypes

- `text` — Single line text
- `textarea` — Multi-line text
- `select` — Dropdown
- `radio` — Radio buttons
- `checkboxes` — Multiple checkboxes
- `assets` — File uploads
- `integer` — Number input
- `toggle` — Yes/no toggle

## Email Templates

**Location:** `resources/views/emails/{template}.antlers.html`

**HTML Template:**
```antlers
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
</head>
<body>
  <h1>New Contact Form Submission</h1>

  <p><strong>Name:</strong> {{ name }}</p>
  <p><strong>Email:</strong> {{ email }}</p>
  <p><strong>Subject:</strong> {{ subject }}</p>
  <p><strong>Message:</strong></p>
  <p>{{ message }}</p>

  <hr>
  <p>Submitted on {{ date format="F j, Y \a\t g:i a" }}</p>
</body>
</html>
```

**Plain Text Template:**
```
New Contact Form Submission

Name: {{ name }}
Email: {{ email }}
Subject: {{ subject }}

Message:
{{ message }}

---
Submitted on {{ date format="F j, Y \a\t g:i a" }}
```

## Frontend Form Template

**Basic Form:**
```antlers
{{ form:contact }}
  {{ if errors }}
    <div class="alert alert-error">
      <ul>
        {{ errors }}
          <li>{{ value }}</li>
        {{ /errors }}
      </ul>
    </div>
  {{ /if }}

  {{ if success }}
    <div class="alert alert-success">
      <p>{{ success }}</p>
    </div>
  {{ else }}
    <div>
      <label for="name">Name</label>
      <input type="text" name="name" id="name" value="{{ old:name }}">
      {{ if error:name }}<span class="error">{{ error:name }}</span>{{ /if }}
    </div>

    <div>
      <label for="email">Email</label>
      <input type="email" name="email" id="email" value="{{ old:email }}">
      {{ if error:email }}<span class="error">{{ error:email }}</span>{{ /if }}
    </div>

    <div>
      <label for="message">Message</label>
      <textarea name="message" id="message">{{ old:message }}</textarea>
      {{ if error:message }}<span class="error">{{ error:message }}</span>{{ /if }}
    </div>

    <button type="submit">Send</button>
  {{ /if }}
{{ /form:contact }}
```

**With File Uploads:**
```antlers
{{ form:contact files="true" }}
  <input type="file" name="resume">
  <button type="submit">Submit</button>
{{ /form:contact }}
```

**Form Tag Parameters:**
```antlers
{{ form:contact
   redirect="/thank-you"
   error_redirect="/form-error"
   files="true"
   id="contact-form"
   class="form"
}}
```

## Spam Protection

**Honeypot (in config):**
```yaml
honeypot: website
```

**In template (hide with CSS):**
```antlers
<div style="position: absolute; left: -9999px;">
  <label for="website">Website</label>
  <input type="text" name="website" id="website">
</div>
```

## Form Submissions

**Storage:** `storage/forms/{form}/{timestamp}.yaml`

**Disable storage:**
```yaml
store: false
```

**Display on frontend:**
```antlers
{{ form:submissions in="testimonials" limit="5" }}
  <blockquote>
    <p>{{ testimonial }}</p>
    <cite>{{ name }}</cite>
  </blockquote>
{{ /form:submissions }}
```

## Common Form Examples

### Contact Form
```yaml
# resources/forms/contact.yaml
title: Contact Form
honeypot: website
email:
  -
    to: "{{ site_settings:email }}"
    from: "{{ email }}"
    reply_to: "{{ email }}"
    subject: "Contact: {{ subject }}"
    html: emails/contact
```

### Newsletter Signup
```yaml
# resources/forms/newsletter.yaml
title: Newsletter Signup
store: false
honeypot: fax
email:
  -
    to: marketing@example.com
    subject: "New subscriber"
    html: emails/newsletter-notification
```

### Job Application
```yaml
# resources/forms/job-application.yaml
title: Job Application
email:
  -
    to: hr@example.com
    subject: "Application: {{ position }}"
    html: emails/job-application
    attachments: true
  -
    to: "{{ email }}"
    subject: "We received your application"
    html: emails/application-confirmation
```

## File Structure

```
resources/
├── forms/
│   ├── contact.yaml
│   └── newsletter.yaml
├── blueprints/forms/
│   ├── contact.yaml
│   └── newsletter.yaml
└── views/
    ├── emails/
    │   ├── contact.antlers.html
    │   └── contact-text.antlers.html
    └── partials/
        └── _form-contact.antlers.html
```

## Boundaries

- Forms don't change for multisite (shared)
- Blueprint handle MUST match form handle

## Accuracy Checks

- Form config in `resources/forms/`
- Blueprint in `resources/blueprints/forms/`
- Email templates in `resources/views/emails/`
- Honeypot field hidden but rendered in form

## Template Quick Reference

| Task | Syntax |
|------|--------|
| Render form | `{{ form:contact }}...{{ /form:contact }}` |
| File uploads | `{{ form:contact files="true" }}` |
| Set redirect | `{{ form:contact redirect="/thank-you" }}` |
| Check success | `{{ if success }}...{{ /if }}` |
| Check errors | `{{ if errors }}...{{ /if }}` |
| Field error | `{{ error:fieldname }}` |
| Old input | `{{ old:fieldname }}` |
