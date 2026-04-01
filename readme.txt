=== Trvlr AI Booking System ===
Contributors: pariswelch
Tags: booking, reservations, tours, trvlr, booking system
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 0.1.4
Requires PHP: 7.0
License: MIT
License URI: https://opensource.org/licenses/MIT

WordPress plugin for integrating the trvlr.ai booking platform: synced attractions, a TRVLR settings dashboard, and front-end booking components.

== Description ==

Trvlr AI Booking System connects your WordPress site to **trvlr.ai**. It syncs tours and experiences into the **`trvlr_attraction`** custom post type, lets you adjust copy and media while respecting or overriding remote updates, and provides booking UI (modals, calendars, payment confirmation) you can place with shortcodes and template tags.

**Documentation for maintainers** ships with the plugin in the `docs/` folder (overview, core sync model, admin and public behavior, and optional `docs/reference/` for detailed technical specs).

= Features =

* Sync attractions from trvlr with batched full sync and single-attraction refresh
* Track local edits per field; optional force-sync for specific fields
* Scheduled sync, structured sync logs, optional email notifications
* TRVLR admin app (Getting Started, Connection, Theme, Sync, Logs) backed by the REST API
* Theme tokens (colors, spacing, cards) exposed as CSS variables on the front end
* Default single-attraction template, Splide galleries, booking and checkout iframes
* Shortcodes for attraction fields, booking calendar, and payment confirmation
* Payment confirmation page support for return URLs from trvlr checkout

= Usage =

1. Install and activate the plugin.
2. Open **TRVLR** in the WordPress admin and set **Connection** (Organization ID and API key as required by your account).
3. Run a sync from the **Sync** tab or edit an attraction and use **Sync from TRVLR** in the sidebar when a trvlr ID exists.
4. Use shortcodes or the default single-attraction template to display content; add booking controls with `attraction-id` and classes such as `trvlr-book-now` or `trvlr-check-availability` (see plugin `docs/public/` for behavior).

== Installation ==

1. Upload the `trvlr` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** screen.
3. Go to **TRVLR** in the admin menu and complete connection and sync setup.

The plugin registers the attraction post type, creates log storage, and may create a **Payment Confirmation** page on activation.

== Frequently Asked Questions ==

= How do I get a trvlr.ai account or subdomain? =

Contact trvlr.ai to set up your booking system and obtain Organization ID / API credentials as they apply to your integration.

= What shortcodes are available? =

The plugin registers many shortcodes for titles, descriptions, galleries, pricing, booking calendar, payment confirmation, and more. See `includes/trvlr-shortcodes.php` in the plugin or the `docs/public/` folder for how they relate to templates.

= How do I add a booking calendar? =

Use the booking calendar shortcode on any post or page. Attraction context can be inferred from the current post when appropriate, or you can pass an attraction identifier depending on shortcode attributes (see shortcode definitions).

= How do booking buttons work? =

The front-end booking script listens for elements that include an `attraction-id` attribute and the appropriate classes (for example `trvlr-book-now` for booking). Use the same patterns as the default templates or the plugin documentation.

= Where is developer documentation? =

See the `docs/` directory inside the plugin: `README.md` is the index; `reference/` holds optional detailed specs (e.g. REST payloads) when provided.

== Changelog ==

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

= 0.1.4 =
Maintenance and fixes for attraction card queries, sync tooling, and release metadata.

= 0.0.3 =
Major improvements to admin interface, packaged release files, and booking calendar behavior (automatic attraction ID from context where possible).

= 0.0.2 =
Adds optional disabling of front-end booking assets while keeping shortcodes available for custom implementations.
