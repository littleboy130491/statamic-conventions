schema_name: categories
schema_type: taxonomy
title: Categories
route: /category/{slug}
collections: - posts

blueprint: categories
fields:
title | text | required
description | textarea | optional
