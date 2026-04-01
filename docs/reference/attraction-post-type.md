# Attraction post type (reference)

## Post type

| | |
|--|--|
| Post type | `trvlr_attraction` |
| Public archive | Yes (`has_archive`) |
| REST | Enabled |
| URL slug | WordPress default (derived from title unless customized) |

## Taxonomy

| Taxonomy | Attached to | Role |
|----------|-------------|------|
| `trvlr_attraction_tag` | `trvlr_attraction` | Tag-like; API `attraction_type` values are applied as terms during sync. |

Core **category** and **post_tag** are also registered on the post type for optional theme use.

## Identity and sync metadata

| Meta key | Purpose |
|----------|---------|
| `trvlr_id` | Remote primary key; stable link to trvlr catalog. |
| `trvlr_pk` | Stored PK from API payload when present. |
| `trvlr_raw_data` | JSON snapshot of last fetched API payload (diagnostics / future use). |

## Content fields (trackable for sync and edit detection)

Aligned with `Trvlr_Field_Map` and the attraction editor. Types reflect how values are hashed, not always the DB storage shape.

| Field | Notes |
|-------|--------|
| `post_title` | Synced title; maps from API `title`. |
| `trvlr_description` | Rich text; API `description`. |
| `trvlr_short_description` | Rich text; API `short_description`. |
| `trvlr_inclusions` | Rich text / list markup; API `inclusions`. |
| `trvlr_highlights` | Rich text / list markup; API `highlights`. |
| `trvlr_pricing` | Array of rows (type, price, sale price, etc.); API `pricing`. |
| `trvlr_locations` | Array of rows (type, address, lat, lng); derived from API. |
| `trvlr_media` | Gallery attachment IDs. |
| `trvlr_duration` | Often `d-h-m` segments (e.g. `0-5-15`); formatted on output. |
| `trvlr_start_time` | Time string. |
| `trvlr_end_time` | Time string. |
| `trvlr_additional_info` | Rich text; API `additional_info`. |
| `trvlr_is_on_sale` | Boolean flag. |
| `trvlr_sale_description` | Short text. |
| `_thumbnail_id` | Featured image (first gallery image or API hero when synced). |

## Internal meta (not shown as ordinary content)

| Meta | Purpose |
|------|---------|
| `_trvlr_sync_hash_{field}` | Per-field hash after a successful sync write. |
| `_trvlr_edited_fields` | List of field names diverging from last sync. |
| `_trvlr_has_custom_edits` | Flag when any tracked field differs. |
| `_trvlr_force_sync_fields` | Fields to accept from API on next sync despite edits. |
| `_trvlr_list_image_cache` | Optional cache when list thumbnail must be preserved. |

## Editor vs template

- Structured fields live in the **Attraction Details** meta box; the main **Content** editor is a separate box for free-form post body.
- Template tags and shortcodes read the `trvlr_*` meta keys and post title/content as documented in `docs/public/`.
