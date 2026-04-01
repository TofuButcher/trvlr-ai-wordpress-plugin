# Trvlr plugin documentation

This folder is the canonical documentation for the **Trvlr AI Booking System** WordPress plugin. It describes how the plugin is organized, what it does at a feature level, and how major subsystems behave. Implementation details live in the codebase and inline comments.

## Purpose

The plugin connects a WordPress site to the **trvlr.ai** booking platform. It **syncs attraction (tour/experience) data** from the remote service into WordPress as a dedicated custom post type, preserves **local editorial changes** where configured, and provides **booking-related front-end behavior** (modals, calendars, payment confirmation flow) and **admin tools** (React settings hub, classic attraction editor, sync control, logging).

## Main features (high level)

- **Attraction sync** — Pulls catalog data from the trvlr API, creates or updates `trvlr_attraction` posts, processes images, and records per-field sync hashes for change detection.
- **Selective updates** — Fields the site has intentionally changed can be left untouched on sync; optional **force sync** can override that per field where supported.
- **Scheduled and manual sync** — Full catalog sync runs in **batched** steps via WP-Cron to avoid timeouts; single-attraction sync is available from the attraction editor or targeted tools.
- **Logging** — Structured sync and system events are stored in a custom database table and exposed under **Logs** in the admin app; optional **email notifications** for sync completion and errors.
- **Front-end experience** — Default single-attraction template, card-oriented CSS, Splide-based galleries, theme CSS variables from settings, booking modal/calendar scripts, and shortcodes for embedding pieces anywhere.
- **Admin dashboard** — Tabbed React app (Getting Started, Connection, Theme, Sync, Logs) backed by the **`trvlr/v1`** REST API; classic **meta boxes** on each attraction for detailed edits and per-post sync.
- **Activation setup** — On activation, the plugin registers the attraction post type, creates the sync log table, seeds a **payment confirmation** page when appropriate, and schedules maintenance crons.

## Repository map

Paths are relative to the plugin root (`wp-content/plugins/trvlr/`).

| Path | What you will find |
|------|---------------------|
| `trvlr.php` | Plugin bootstrap: constants, activation/deactivation hooks, update checker, and entry to the main `Trvlr` class. |
| `includes/` | Shared infrastructure: hook loader, REST API (`Trvlr_REST_API`), scheduler, logger, notifier, field map, data transforms, edit tracker, theme config, shortcodes and template helpers, activator/deactivator. |
| `core/` | Domain logic tightly tied to attractions: sync engine (`Trvlr_Sync`), attraction CPT registration (`Trvlr_Attraction`). |
| `admin/` | Top-level TRVLR menu; **`partials/`** shell for the React mount; **`src/`** admin app source; **`build/`** compiled bundle; **`styles/`** / **`css`** admin skin; **`meta-fields.php`** and **`class-trvlr-meta-repeater.php`** for the attraction editor; **`class-trvlr-admin.php`** for enqueue, settings registration, AJAX, and extra REST routes. |
| `public/` | **`class-trvlr-public.php`** (assets, template swap, payment page, filters); **`partials/`** single-attraction template; **`css/`** compiled styles; **`src/styles/`** SCSS sources; **`js/`** public and booking scripts; **`dist/`** Splide vendor files. |
| `media/` | Static assets shipped with the plugin (e.g. header branding SVG). |
| `plugin-update-checker/` | Third-party library for GitHub-hosted plugin updates. |
| `docs/` | Maintainer documentation: architecture guides and optional **`reference/`** specs (REST payloads, migrations, deep config). |
| `~api`, `~dev` | Local development-only assets; not part of a distributable build. |

## Detailed documentation layout

| Section | Description |
|---------|--------------|
| [Core](core/README.md) | Bootstrap, sync, data model, cron/logging/notifications. |
| [Public (front end)](public/README.md) | Templates, styles, scripts, booking behavior, shortcodes. |
| [Admin](admin/README.md) | React dashboard, meta boxes, REST overview. |
| [Reference (specs)](reference/README.md) | Technical supplements: endpoint tables, theme config detail, API migration notes—see folder README for naming and migration tips. |

Narrative overview: **`docs/admin/rest-api.md`**. Detailed request/response reference: **`docs/reference/admin-rest-api.md`**.
