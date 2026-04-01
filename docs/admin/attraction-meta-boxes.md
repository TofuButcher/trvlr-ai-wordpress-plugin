# Attraction editor (meta boxes)

## Entry point

`Trvlr_Admin::init_meta_boxes()` loads **`admin/meta-fields.php`**, which registers meta boxes and save handlers for the `trvlr_attraction` post type.

## Meta boxes

1. **TRVLR Sync (sidebar)** — Shows the remote **trvlr ID** when present, warns when **`_trvlr_has_custom_edits`** is set with a count of skipped fields, and provides **Sync from TRVLR** which posts to the `trvlr_sync_single` AJAX action with a dedicated nonce. Success reloads the edit screen.

2. **Attraction Details (normal, core priority)** — Consolidated fields: read-only trvlr ID, multiple **editors** for description, short description, inclusions, highlights, sale checkbox and description, **pricing** and **locations** repeaters, a **media gallery** backed by the WordPress media modal (attachment IDs stored in `trvlr_media`), duration, start/end time inputs, and additional info editor.

3. **Content** — The main post editor is removed from default placement and re-added as its own meta box lower in the stack so the structured “Attraction Details” box sits directly under the title.

The native **Custom Fields** meta box is removed for this post type to avoid raw meta clutter.

## Repeaters

`Trvlr_Meta_Repeater` renders titled sections with add/remove rows and text inputs driven by a field definition array. Instances are created in **`trvlr_get_repeater_instances()`** for **pricing** (type, price, sale price) and **locations** (type, address, lat, lng). Each repeater verifies its own nonce on save and stores a sanitized array in the corresponding meta key.

## Saving

`trvlr_save_details_meta` runs on `save_post`: verifies the details nonce, sanitizes text and editor fields, normalizes the on-sale flag, updates the gallery only when `trvlr_media` appears in `POST` (empty array clears the gallery), then delegates to each repeater’s **`save()`** method.

## Relationship to sync

Fields here mirror the **field map** used by sync and edit tracking. Edits saved through this screen participate in **hash comparison** and **`_trvlr_edited_fields`** after the next `save_post` pass through the edit tracker.
