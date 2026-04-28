schema_name: posts
schema_type: collection
title: Posts
has_single: true
has_archive: true
route: /blog/{slug}
dated: true
structure: false
mount: blog
taxonomy_relationship: - categories - tags

blueprint: post
blueprint_description: Standard blog post
fields:
title | text | required
featured_image | assets | optional | max_files: 1
excerpt | textarea | optional | character_limit: 300
content | bard | optional | h2, h3, bold, italic, unorderedlist, orderedlist, quote, link, image
author | users | optional | max_items: 1
