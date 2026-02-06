# Forms (condensed)

## Config
- Path: `resources/forms/{handle}.yaml`
- Required: `title`
- Common keys: `honeypot`, `store`, `email` (array of notifications)

## Blueprint
- Path: `resources/blueprints/forms/{handle}.yaml`
- Use top-level `fields:`

## Templates
- Render: `{{ form:contact }}...{{ /form:contact }}`
- Enable uploads: `files="true"`
- Handle errors: `{{ if errors }}...{{ /if }}`
