schema_name: pages
schema_type: collection
title: Pages
has_single: true
has_archive: false
route: /{parent_uri}/{slug}
dated: false
structure: true
structure_max_depth: 3

blueprint: default
blueprint_description: Simple content page
fields:
title | text | required
content | bard | optional | h2, h3, bold, italic, unorderedlist, orderedlist, quote, link, image

---

blueprint: home
blueprint_description: Homepage with hero and latest posts
fields:
title | text | required
hero_heading | text | optional
hero_lead | textarea | optional
hero_image | assets | optional | max_files: 1
hero_cta_text | text | optional
hero_cta_link | link | optional | collections: pages

---

blueprint: about
blueprint_description: About page with content and team
fields:
title | text | required
subtitle | text | optional
featured_image | assets | optional | max_files: 1
content | bard | optional | h2, h3, bold, italic, unorderedlist, orderedlist, quote, link, image

---

blueprint: contact
blueprint_description: Contact page with info and form
fields:
title | text | required
subtitle | text | optional
content | bard | optional | h2, h3, bold, italic, link
email | text | optional | input_type: email
phone | text | optional
address | textarea | optional
