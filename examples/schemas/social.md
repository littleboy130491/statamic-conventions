schema_name: social
schema_type: global
title: Social Media

fields:
social_links | grid
  platform | select | options: facebook, twitter, instagram, linkedin, youtube, tiktok
  url | text | input_type: url
