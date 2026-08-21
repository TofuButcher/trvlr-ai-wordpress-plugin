=== Traveloris Wordpress Manager ===
Contributors: pariswelch
Tags: booking, reservations, tours, trvlr, traveloris, booking system
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 0.2.01
Requires PHP: 7.0
License: MIT
License URI: https://opensource.org/licenses/MIT

WordPress plugin for integrating the Traveloris booking platform: synced attractions, a Traveloris settings dashboard, and front-end booking components.

== Description ==

Traveloris Wordpress Manager connects your WordPress site to **Traveloris**. It syncs tours and experiences into the **`trvlr_attraction`** custom post type, lets you adjust copy and media while respecting or overriding remote updates, and provides booking UI (modals, calendars, payment confirmation) you can place with shortcodes and template tags.

**Documentation for maintainers** ships with the plugin in the `docs/` folder (overview, core sync model, admin and public behavior, and optional `docs/reference/` for detailed technical specs).

= Features =

* Sync attractions from Traveloris with batched full sync and single-attraction refresh
* Track local edits per field (Synced / Custom Edit); Traveloris is source of truth for synced fields
* Scheduled sync, structured sync logs, optional email notifications
* Traveloris admin app (Getting Started, Connection, Theme, Sync, Logs, Tools) backed by the REST API
* Theme tokens (colors, spacing, cards) exposed as CSS variables on the front end
* Default single-attraction template, Splide galleries, booking and checkout iframes
* Shortcodes for attraction fields, booking calendar, and payment confirmation
* Payment confirmation page support for return URLs from Traveloris checkout

= Usage =

1. Install and activate the plugin.
2. Open **Traveloris** in the WordPress admin and set **Connection** (Organization ID and API key as required by your account).
3. Run a sync from the **Sync** tab or edit an attraction and use **Sync from Traveloris** in the sidebar when a Traveloris ID exists.
4. Use shortcodes or the default single-attraction template to display content; add booking controls with `attraction-id` and classes such as `trvlr-book-now` or `trvlr-check-availability` (see plugin `docs/public/` for behavior).

== Installation ==

1. Upload the `trvlr` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** screen.
3. Go to **Traveloris** in the admin menu and complete connection and sync setup.

The plugin registers the attraction post type, creates log storage, and may create a **Payment Confirmation** page on activation.

== Frequently Asked Questions ==

= How do I get a Traveloris account or subdomain? =

Contact Traveloris (traveloris.com) to set up your booking system and obtain Organization ID / API credentials as they apply to your integration.

= What shortcodes are available? =

The plugin registers many shortcodes for titles, descriptions, galleries, pricing, booking calendar, payment confirmation, and more. See `includes/trvlr-shortcodes.php` in the plugin or the `docs/public/` folder for how they relate to templates.

= How do I add a booking calendar? =

Use the booking calendar shortcode on any post or page. Attraction context can be inferred from the current post when appropriate, or you can pass an attraction identifier depending on shortcode attributes (see shortcode definitions).

= How do booking buttons work? =

The front-end booking script listens for elements that include an `attraction-id` attribute and the appropriate classes (for example `trvlr-book-now` for booking). Use the same patterns as the default templates or the plugin documentation.

= Where is developer documentation? =

See the `docs/` directory inside the plugin: `README.md` is the index; `reference/` holds optional detailed specs (e.g. REST payloads) when provided.

== Changelog ==

= 0.2.01 =
Hotfix for 0.2.0.
* Check availability modal uses `group_id` when `attraction-group-id` is set (same as Book now)
* Fix MutationObserver timing issue when public JS loads in the head
* Single layout tweaks (sidebar/calendar width, gallery height)

= 0.2.0 =
Admin
* Admin menu and plugin chrome rebranded to Traveloris (logo SVGs, dashboard header)
* New Tools page: feature toggles (CPT, sync, frontend booking, SEO schema) plus theme settings export/import
* Live attraction card preview on the Theme page (REST preview-card)
* Connection and sync copy updated for Traveloris as the catalog source

Sync & data
* Unified field map (`class-trvlr-field-map.php`) driving sync, Custom Edit, and meta UI
* Custom edits are explicit per field (Synced / Custom Edit); hash auto-detect (`Trvlr_Edit_Tracker`) removed
* One-time `explicit_v1` migration for existing `_trvlr_edited_fields`
* FAQs and important-information fields on attractions; term FAQs on taxonomies
* List `seo_metadata` stored as post meta; JSON-LD (TouristAttraction + FAQPage) on singles by default

Frontend / themes
* Shared template helpers: `trvlr_section`, gallery `layout` (`nav-bottom` / `nav-right` / `nav-right-2col`), `trvlr_faqs` accordion vs list
* Presentation themes enqueue `themes/variant-N.css` from `public/dist/css/` — no per-theme JS
* Icon system (`icons/` + `class-trvlr-icons.php`)
* Semantic color tokens; `importantColor` → `alertColor` (legacy CSS vars aliased)
* Compiled public assets moved: `public/css` + `public/js` → `public/dist/css` + `public/dist/js`

Tooling
* Webpack replaced by Vite (`npm run dev`, `npm run build`, `npm run dist`)
* Dual zips: production `trvlr-wordpress-manager.zip` and `dev-trvlr-wordpress-manager.zip` (`~dev` only in the latter)

Dev-only (dev zip)
* `?trvlr_test=true` data debug (unchanged)
* `?trvlr_components=true` component library
* `?trvlr_theme_colors=true` theme color page

= 0.1.91 =
* Version bump to force update - Refer to 0.1.9

= 0.1.9 =
* New template and style options
* Group titles now have dedicated source rather than primary attraction title
* New fields for simple location, suitable ages and cancellation policy

= 0.1.8 =
* Improvements to syncing functionality to reliably show feedback for current sync state even if user didn't start it
* Sync now supports use of Action Scheduler where available to improve reliability on sites with low traffic. AS not bundled so falls back to WP-Cron if not found.
* All booking elements now automatically use Group ID when found so group type attractions now work out of the box

= 0.1.7 =
* Added support for 'group' type attractions
* Improvements to query manager script to enable building of better custom ajax filters / sort elements
* Updated debugging endpoint "?trvlr_test=true" to use latest traveloris domain API endpoint

= 0.1.6 =
* Updated API + booking iframe URLs to use the new subdomain ( traveloris.com )
* New system for switching attraction card + page templates & styles

= 0.1.5 =
* More comprehensive documentation and updated feature outlines
* Added controls to disable front-end booking, syncing, and the trvlr_attraction post type
* Fixed `trvlr_payment_confirmation` shortcode for embedding the payment confirmation iframe
* Content of Automatically created Payment confirmation page set to the `trvlr_payment_confirmation` shortcode

= 0.1.4 =
* Stable tag and version metadata updated for release
* Attraction cards shortcode and query: separate WordPress vs TRVLR tag filters, optional categories, safer `tax_query` wrapping for single-clause cases
* Debug tooling, data transform behavior, and bulk force-sync workflow improvements

= 0.1.3 =
* Extracted attraction data transforms into `Trvlr_Data_Transform` (shared normalization and list/pricing helpers)
* Improved data-transform testing route for inspecting API-shaped data
* Post type labels use a clear **TRVLR** prefix on `trvlr_attraction` to avoid clashing with other “attraction” post types
* Ensured compiled React admin assets are included in the distributable build
* Version bump and stable-tag alignment for 0.1.3

= 0.1.2 =
* Further plugin directory / update-checker naming fixes for reliable self-updates from GitHub
* Version number alignment with tagged releases

= 0.1.1 =
* Renamed a filter hook for consistent plugin directory naming (update checker / paths)

= 0.1.0 =
* Full sync refactored to **batched processing** with WP-Cron continuations to avoid timeouts on large catalogs and modest hosts
* **trvlr_attraction** custom post type with rich meta, sync engine, change tracking, and dedicated TRVLR admin area (connection, sync, logs)
* **React-based TRVLR settings** app (single mount), WordPress components, theme tab with live card preview
* Theme configuration driven from PHP with CSS variables on the front end; merged theme config for one source of truth between server and UI
* **REST API** (`trvlr/v1`) for settings, sync, logs, and setup operations used by the dashboard
* Connected sync to the **live trvlr API**; field mapping and data transforms aligned with production payloads
* Core front end: booking scripts and styles, attraction **card grid** shortcode, **single attraction** template, Splide gallery support, many field shortcodes
* Mobile responsiveness for single attraction and card grids; container-query grid for cards in non-full-width areas
* Per-attraction **Sync from TRVLR** on the editor, **live sync progress** for full sync, clearer logging
* Optional `~dev`-style local overrides for template testing without shipping dev files
* SCSS build pipeline for public and admin assets; ongoing admin UI and instruction content updates
* Removed redundant connection/API key UI where organisation-based auth is sufficient

= 0.0.3 =
* Added packaged plugin files, `readme.txt`, branding asset, and expanded `trvlr.php` settings (including frontend visibility and tour post types)
* Meta fields for attraction ID on supported post types; optional automatic **payment confirmation** page creation
* Admin styles and setup-oriented copy on the settings screen
* Changed “Enable Frontend” to **Disable Frontend Elements** (inverted default)
* Booking calendar shortcode can resolve attraction ID from the current post when attributes are omitted
* Tour post types configuration for registering attraction ID fields where needed
* Refactored front-end booking script to a class-based structure; improved modal handling, validation, and booking calendar shortcode defaults
* Documentation and development outline updates

= 0.0.2 =
* **Enable / disable front-end** booking assets from settings (conditional enqueue of JS/CSS when enabled and configured)
* Booking modals and scripts respect the toggle; shortcodes remain available for custom layouts
* Admin settings presentation improvements

= 0.0.1 =
* Aligned plugin version and description with GitHub releases
* **Plugin Update Checker** integration for updates from the GitHub repository inside WordPress
* Initial public booking integration: modal flow, payment confirmation page, booking calendar shortcode, and base admin settings

== Upgrade Notice ==

= 0.2.01 =
Hotfix for 0.2.0: grouped check-availability, list-trim JS crash, and per-site CSS so existing branded sites keep their look.

= 0.2.0 =
Custom edits are now explicit (Synced vs Custom Edit); Traveloris is the catalog source of truth. Maintainers: webpack is gone — copy vite.local.example.json to vite.local.json and use npm run dev. Child themes that hardcode public/css/ or public/js/ must switch to public/dist/. JSON-LD schema is on by default (disable under Tools). Gallery helpers now take layout (nav-bottom / nav-right / nav-right-2col) instead of theme-specific args.

= 0.1.4 =
Maintenance and fixes for attraction card queries, sync tooling, and release metadata.

= 0.0.3 =
Major improvements to admin interface, packaged release files, and booking calendar behavior (automatic attraction ID from context where possible).

= 0.0.2 =
Adds optional disabling of front-end booking assets while keeping shortcodes available for custom implementations.