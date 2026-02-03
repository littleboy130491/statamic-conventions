# Statamic Conventions: Forms

## Form (Configuration)

**Location:** `resources/forms/{handle}.yaml`

**Purpose:** Defines form settings, email notifications, and behavior.

**Controls:**

- Form title and handle
- Email notifications
- Honeypot spam protection
- Submission storage

**Required Fields:**

- `title` — Display name in Control Panel (e.g., "Contact Form", "Newsletter Signup")

**Common Fields:**

- `honeypot` — Honeypot field name for spam protection
- `store` — Boolean, whether to store submissions (default: true)
- `email` — Array of email notification configurations

**Reference:** https://statamic.dev/forms

**Mental model:** Form config defines how the form behaves and who gets notified.

---

## Form Blueprint

**Location:** `resources/blueprints/forms/{handle}.yaml`

**Purpose:** Defines the form fields and validation rules.

**Controls:**

- Form fields and their types
- Validation rules
- Field display and instructions

**Structure:**

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
    handle: message
    field:
      type: textarea
      display: Message
      validate:
        - required

```

**Available Fieldtypes:**

- `text` — Single line text
- `textarea` — Multi-line text
- `select` — Dropdown selection
- `radio` — Radio buttons
- `checkboxes` — Multiple checkboxes
- `assets` — File uploads
- `integer` — Number input
- `toggle` — Yes/no toggle

**Notes:**

- Blueprint handle must match form handle
- Not all fieldtypes available by default (can be enabled in service provider)
- Validation uses Laravel validation rules

**Reference:** https://statamic.dev/forms#blueprint

**Mental model:** Blueprint defines what fields the form has and how to validate them.

---

## Form Submissions

**Location:** `storage/forms/{form}/{timestamp}.yaml`

**Purpose:** Stores submitted form data.

**Structure:**

```yaml
name: John Doe
email: john@example.com
message: Hello, I have a question...

```

**Key rules:**

- Automatically generated on form submission
- Filename is timestamp-based
- Contains all submitted field values
- Viewable in Control Panel under Tools > Forms

**Disabling Storage:**

```yaml
# resources/forms/newsletter.yaml
title: Newsletter Signup
store: false

```

**Reference:** https://statamic.dev/forms#submissions

**Mental model:** Submissions are stored form responses.

---

## Email Notifications

**Location:** Configured in `resources/forms/{handle}.yaml`

**Purpose:** Send email notifications when form is submitted.

**Structure:**

```yaml
title: Contact Form
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

**Email Configuration Fields:**

- `to` — Recipient email (can use form variables)
- `from` — Sender email (can use form variables)
- `reply_to` — Reply-to address
- `subject` — Email subject (can use form variables)
- `html` — HTML email template path
- `text` — Plain text email template path
- `markdown` — Markdown email template path
- `attachments` — Boolean, attach uploaded files

**Using Form Variables:**

```yaml
to: "{{ email }}"
subject: "Message from {{ name }}"

```

**Multiple Recipients:**

```yaml
to:
  - admin@example.com
  - support@example.com

```

**Reference:** https://statamic.dev/forms#email

**Mental model:** Email config defines who gets notified and how.

---

## Email Templates

**Location:** `resources/views/emails/{template}.antlers.html`

**Purpose:** Define email content and layout.

**HTML Template:**

```
{{# resources/views/emails/contact.antlers.html #}}
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
</head>
<body>
  <h1>New Contact Form Submission</h1>

  <p><strong>Name:</strong> {{ name }}</p>
  <p><strong>Email:</strong> {{ email }}</p>
  <p><strong>Message:</strong></p>
  <p>{{ message }}</p>

  <hr>
  <p>Submitted on {{ date format="F j, Y \a\t g:i a" }}</p>
</body>
</html>

```

**Plain Text Template:**

```
{{# resources/views/emails/contact-text.antlers.html #}}
New Contact Form Submission

Name: {{ name }}
Email: {{ email }}

Message:
{{ message }}

---
Submitted on {{ date format="F j, Y \a\t g:i a" }}

```

**Available Variables:**

- All form field values by handle
- `date` — Submission timestamp
- `site_url` — Site URL

**Key rules:**

- Template path is relative to `resources/views/`
- Provide both HTML and text versions for best compatibility
- Avoid using `message` as field handle (Laravel reserved word)

**Reference:** https://statamic.dev/forms#email

---

## Form Templates (Frontend)

**Location:** `resources/views/{path}/form.antlers.html` or inline in page templates

**Purpose:** Render the form on the frontend.

### Basic Form

**Pattern:**

```
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

### Dynamic Fields

**Pattern:**

```
{{ form:contact }}
  {{ if success }}
    <p>{{ success }}</p>
  {{ else }}
    {{ fields }}
      <div class="form-group">
        <label for="{{ handle }}">{{ display }}</label>
        {{ field }}
        {{ if error }}<span class="error">{{ error }}</span>{{ /if }}
      </div>
    {{ /fields }}

    <button type="submit">Send</button>
  {{ /if }}
{{ /form:contact }}

```

**Available Variables in `{{ fields }}`:**

- `handle` — Field handle
- `display` — Field display name
- `type` — Fieldtype
- `field` — Pre-rendered field HTML
- `error` — Field-specific error message
- `old` — Previously submitted value
- `instructions` — Field instructions

### With File Uploads

**Pattern:**

```
{{ form:contact files="true" }}
  {{# files="true" adds enctype="multipart/form-data" #}}

  <div>
    <label for="resume">Resume</label>
    <input type="file" name="resume" id="resume">
  </div>

  <button type="submit">Submit</button>
{{ /form:contact }}

```

**Reference:** https://statamic.dev/tags/form

---

## Form Tag Parameters

**Common Parameters:**

```
{{ form:contact
   redirect="/thank-you"
   error_redirect="/form-error"
   allow_request_redirect="true"
   files="true"
   id="contact-form"
   class="form"
   attr:data-analytics="contact"
}}

```

**Parameters:**

- `redirect` — URL to redirect after successful submission
- `error_redirect` — URL to redirect on validation errors
- `allow_request_redirect` — Allow redirect URL from request
- `files` — Enable file uploads (adds enctype)
- `id` — Form element ID
- `class` — Form element class
- `attr:*` — Custom HTML attributes

**Reference:** https://statamic.dev/tags/form-create

---

## Success & Error Handling

### Success Message

**Default:**

```
{{ if success }}
  <p>{{ success }}</p>
{{ /if }}

```

**Custom Message (in form config):**

```yaml
# resources/forms/contact.yaml
title: Contact Form
success: Thank you for your message! We'll respond within 24 hours.

```

**Via Redirect:**

```
{{ form:contact redirect="/thank-you" }}
  ...
{{ /form:contact }}

```

### Error Display

**All Errors:**

```
{{ if errors }}
  <ul>
    {{ errors }}
      <li>{{ value }}</li>
    {{ /errors }}
  </ul>
{{ /if }}

```

**Field-Specific Errors:**

```
{{ if error:email }}
  <span class="error">{{ error:email }}</span>
{{ /if }}

```

**Check for Any Errors:**

```
{{ if errors }}
  {{# Form has validation errors #}}
{{ /if }}

```

### Preserving Old Input

**Pattern:**

```
<input type="text" name="name" value="{{ old:name }}">
<textarea name="message">{{ old:message }}</textarea>
<select name="topic">
  <option value="general" {{ if old:topic == "general" }}selected{{ /if }}>General</option>
  <option value="support" {{ if old:topic == "support" }}selected{{ /if }}>Support</option>
</select>

```

---

## Spam Protection

### Honeypot Field

**Configuration:**

```yaml
# resources/forms/contact.yaml
title: Contact Form
honeypot: website

```

**Template (hide with CSS):**

```
{{ form:contact }}
  {{# Honeypot field - hide with CSS, bots will fill it #}}
  <div style="position: absolute; left: -9999px;">
    <label for="website">Website</label>
    <input type="text" name="website" id="website">
  </div>

  {{# Real fields #}}
  ...
{{ /form:contact }}

```

**How it works:**

- Real users won't see/fill the hidden field
- Bots automatically fill all fields
- Submissions with honeypot filled are rejected

### Using Laravel's CSRF Protection

- CSRF token automatically included by `{{ form:* }}` tags
- No additional configuration needed

### Rate Limiting

- Configure in Laravel's `RouteServiceProvider` or middleware
- Not built into Statamic forms by default

**Reference:** https://statamic.dev/forms#spam-prevention

---

## AJAX Form Submission

**Pattern:**

```html
<script>
document.getElementById('contact-form').addEventListener('submit', async function(e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);

  try {
    const response = await fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    const data = await response.json();

    if (data.success) {
      // Handle success
      form.innerHTML = '<p>Thank you!</p>';
    } else {
      // Handle validation errors
      console.log(data.errors);
    }
  } catch (error) {
    console.error('Error:', error);
  }
});
</script>

```

**Response Format (AJAX):**

```json
// Success
{
  "success": true,
  "submission_created": true
}

// Validation Error
{
  "success": false,
  "errors": {
    "email": ["The email field is required."]
  }
}

```

**Key rules:**

- Set `X-Requested-With: XMLHttpRequest` header
- Response is JSON when AJAX detected
- Include CSRF token in form data

**Reference:** https://statamic.dev/forms#ajax

---

## Displaying Submissions

### In Control Panel

- Navigate to Tools > Forms > {Form Name}
- View, export, or delete submissions

### On Frontend

**Pattern:**

```
<h2>Recent Testimonials</h2>
{{ form:submissions in="testimonials" limit="5" }}
  <blockquote>
    <p>{{ testimonial }}</p>
    <cite>{{ name }}</cite>
  </blockquote>
{{ /form:submissions }}

```

**Parameters:**

- `in` — Form handle
- `limit` — Number of submissions
- `sort` — Field to sort by
- `dir` — Sort direction (asc/desc)

**Reference:** https://statamic.dev/tags/form-submissions

---

## Exporting Submissions

**In Control Panel:**

- Go to Tools > Forms > {Form}
- Click Export button
- Choose CSV or JSON format

**Custom Exporters:**

- Create class extending `Statamic\Forms\Exporters\Exporter`
- Register in `config/statamic/forms.php`

**Reference:** https://statamic.dev/forms#exporters

---

## Common Form Patterns

### Contact Form

**Config:**

```yaml
# resources/forms/contact.yaml
title: Contact Form
honeypot: website
email:
  -
    to: "{{ site_settings:email }}"
    from: "{{ email }}"
    reply_to: "{{ email }}"
    subject: "Contact form: {{ subject }}"
    html: emails/contact

```

**Blueprint:**

```yaml
# resources/blueprints/forms/contact.yaml
title: Contact Form
fields:
  -
    handle: name
    field:
      type: text
      display: Name
      validate: required
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
      validate: required
  -
    handle: message
    field:
      type: textarea
      display: Message
      validate: required

```

### Newsletter Signup

**Config:**

```yaml
# resources/forms/newsletter.yaml
title: Newsletter Signup
store: false
honeypot: fax
email:
  -
    to: marketing@example.com
    subject: "New newsletter subscriber"
    html: emails/newsletter-notification

```

**Blueprint:**

```yaml
# resources/blueprints/forms/newsletter.yaml
title: Newsletter Signup
fields:
  -
    handle: email
    field:
      type: text
      input_type: email
      display: Email Address
      validate:
        - required
        - email
  -
    handle: interests
    field:
      type: checkboxes
      display: Interests
      options:
        news: Company News
        products: Product Updates
        tips: Tips & Tutorials

```

### Job Application

**Config:**

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

**Blueprint:**

```yaml
# resources/blueprints/forms/job-application.yaml
title: Job Application
fields:
  -
    handle: name
    field:
      type: text
      display: Full Name
      validate: required
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
    handle: phone
    field:
      type: text
      display: Phone
  -
    handle: position
    field:
      type: select
      display: Position
      options:
        developer: Developer
        designer: Designer
        marketing: Marketing
      validate: required
  -
    handle: resume
    field:
      type: assets
      display: Resume
      max_files: 1
      validate: required
  -
    handle: cover_letter
    field:
      type: textarea
      display: Cover Letter

```

---

## Form Partials

**Location:** `resources/views/partials/_form-{handle}.antlers.html`

**Purpose:** Reusable form components.

**Common Partials:**

- `_form-contact.antlers.html` — Contact form
- `_form-newsletter.antlers.html` — Newsletter signup
- `_form-field.antlers.html` — Generic field wrapper

**Example — Field Wrapper:**

```
{{# resources/views/partials/_form-field.antlers.html #}}
<div class="form-group {{ if error }}has-error{{ /if }}">
  <label for="{{ handle }}">
    {{ display }}
    {{ if validate | contains:required }}<span class="required">*</span>{{ /if }}
  </label>

  {{ field }}

  {{ if instructions }}
    <small class="form-text">{{ instructions }}</small>
  {{ /if }}

  {{ if error }}
    <span class="error-message">{{ error }}</span>
  {{ /if }}
</div>

```

**Reference:** https://statamic.dev/tags/partial

---

## Recommended File Structure

```
resources/
├── forms/
│   ├── contact.yaml
│   ├── newsletter.yaml
│   └── job-application.yaml
├── blueprints/forms/
│   ├── contact.yaml
│   ├── newsletter.yaml
│   └── job-application.yaml
└── views/
    ├── emails/
    │   ├── contact.antlers.html
    │   ├── contact-text.antlers.html
    │   ├── newsletter-notification.antlers.html
    │   └── application-confirmation.antlers.html
    └── partials/
        ├── _form-contact.antlers.html
        ├── _form-newsletter.antlers.html
        └── _form-field.antlers.html

```

---

## Quick Reference

| Concept | Location | Defines | Docs |
| --- | --- | --- | --- |
| Form Config | `resources/forms/{handle}.yaml` | Form settings & emails | [Link](https://statamic.dev/forms) |
| Form Blueprint | `resources/blueprints/forms/{handle}.yaml` | Form fields | [Link](https://statamic.dev/forms#blueprint) |
| Submissions | `storage/forms/{form}/*.yaml` | Stored responses | [Link](https://statamic.dev/forms#submissions) |
| Email Template | `resources/views/emails/*.antlers.html` | Notification emails | [Link](https://statamic.dev/forms#email) |
| Form Partial | `resources/views/partials/_form-*.antlers.html` | Form templates | [Link](https://statamic.dev/tags/partial) |

---

## Template Quick Reference

| Task | Syntax |
| --- | --- |
| Render form | `{{ form:contact }}...{{ /form:contact }}` |
| Enable file uploads | `{{ form:contact files="true" }}` |
| Set redirect | `{{ form:contact redirect="/thank-you" }}` |
| Check success | `{{ if success }}...{{ /if }}` |
| Display success message | `{{ success }}` |
| Check for errors | `{{ if errors }}...{{ /if }}` |
| Loop all errors | `{{ errors }}{{ value }}{{ /errors }}` |
| Field-specific error | `{{ error:fieldname }}` |
| Old input value | `{{ old:fieldname }}` |
| Dynamic fields loop | `{{ fields }}...{{ /fields }}` |
| Render field HTML | `{{ field }}` |
| Display submissions | `{{ form:submissions in="handle" }}...{{ /form:submissions }}` |