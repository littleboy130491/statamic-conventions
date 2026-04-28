schema_name: contact
schema_type: form
title: Contact Form
store: true
honeypot: website
email_to: admin@example.com
email_subject: New contact form submission

fields:
name | text | required
email | text | required | input_type: email
subject | text | optional
submission_message | textarea | required
