# Data model and change detection

## Attraction post type

**Post type:** `trvlr_attraction`

Tours and experiences synced from trvlr are stored as this CPT. It supports title, editor, featured image, excerpt, and custom fields, is public and REST-enabled, and uses a dedicated non-hierarchical taxonomy **`trvlr_attraction_tag`** (plus core categories/tags where configured). API-side “types” can be mapped onto attraction tags during sync.

Core identity from the remote system is stored in post meta (e.g. trvlr ID and raw API payload for debugging or future use).

Field names and meta keys in table form: [Attraction post type (reference)](../reference/attraction-post-type.md).

## Field map

`Trvlr_Field_Map` is the **single catalog** of which fields participate in:

- Sync comparison and updates  
- Per-field **hash** storage after a successful sync  
- Edit tracking on save  

Each entry has a type (string, array, boolean), a human label, and optional linkage to API property names where that mapping is direct. Examples include title, rich text areas (description, inclusions, highlights), pricing and locations arrays, duration and times, sale flags, media-related fields, and featured image.

Keeping this list in one place avoids drift between “what sync writes” and “what counts as an edit.”

## Data transforms

`Trvlr_Data_Transform` normalizes API strings into forms suitable for WordPress: cleaning HTML for the block/classic editor, normalizing titles, turning structured list or JSON-like API payloads into editor-friendly markup, and building repeatable row structures for pricing and locations. Transforms run **before** hashing where applicable so API and stored forms compare consistently.

## Hashing

For each trackable field, the plugin stores a **sync hash** after a successful sync that applied that field. Hashes are computed with awareness of field type:

- Strings are trimmed and line endings normalized; title and rich-text fields follow extra normalization so trivial HTML or entity differences do not cause false positives.
- Arrays are normalized (key order, numeric coercion where defined, pruning empty entries) before hashing.
- Booleans map to stable string forms.

This allows a compact “did the stored value diverge from what we last synced?” check without storing full duplicate copies of large HTML fields for comparison only.

## Edit tracking on save

When an attraction post is saved in the admin (not autosaves or revisions), the edit tracker walks all trackable fields that already have a sync hash. It compares the **current** hash to the **stored sync hash**:

- If they differ, the field is listed as **custom-edited** and a site-wide flag indicates the post has customizations.
- If they match, that field is removed from the custom-edit list.

So “custom edit” means **local content no longer matches what was last successfully synced** for that field, not merely “user opened the post.”

## Sync vs edits

During sync, for existing posts:

- If a field is already marked as custom-edited, incoming API data for that field is typically **omitted** so local work is preserved.
- If the sync logic detects a **conflict** (remote data changed but the editor also changed the field since last sync), the field can be skipped and the edit list updated so the UI reflects reality.
- **Force sync** meta can list fields that should accept API data on the next sync even when they would otherwise be treated as protected; after application, those overrides are cleared.

New posts have no prior hashes for some fields until after the first successful write; the sync path handles that case so initial population is complete.

## Raw payload

The full API payload for an attraction may be stored in meta for diagnostics or downstream use. The field map and hashes govern **user-facing** fields; the raw blob is not what drives edit detection for mapped fields.
