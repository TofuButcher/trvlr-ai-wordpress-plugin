# Trvlr AI Booking System (WordPress plugin)

WordPress integration for **[trvlr.ai](https://trvlr.ai)**: sync tours and experiences into a custom post type, manage them from the block/classic editor and a React settings hub, and embed the trvlr booking flow (modals, calendars, payment confirmation) on the front end.

## Documentation

 Maintainer docs live in **[`docs/`](docs/README.md)**—architecture by area (`core/`, `admin/`, `public/`) plus **`docs/reference/`** for technical specs (REST payloads, theme config detail, migration notes). Start at [`docs/README.md`](docs/README.md).

## Requirements

- WordPress 5.0+
- PHP 7.0+ (see plugin header / `readme.txt`)
- Built admin UI requires compiled assets under `admin/build/` (see Development)

## Installation

1. Copy the `trvlr` folder into `wp-content/plugins/`.
2. Activate **Trvlr AI Booking System** under Plugins.
3. Open **TRVLR** in the admin menu and complete **Getting Started** / **Connection** (Organization ID and API credentials as required by your trvlr account).

Activation registers the `trvlr_attraction` post type, creates sync log storage, and can create a **Payment Confirmation** page used by the booking return flow.

## Configuration (high level)

| Area | Purpose |
|------|--------|
| **Connection** | Organisation ID and API key so sync and iframes target the correct trvlr tenant. |
| **Theme** | Colors, spacing, and card variables (surfaced as CSS custom properties on the front end). |
| **Sync** | Manual full sync, schedule, per-attraction sync from the editor, custom-edit and force-sync tooling. |
| **Logs** | Sync and system log entries with export/clear options. |

## Front end

- **Single attraction** — Default template under `public/partials/`; theme CSS variables from **Theme** settings.
- **Booking script** — Listens for elements with `attraction-id` and classes such as **`trvlr-book-now`** and **`trvlr-check-availability`** (see `public/js/trvlr-bookings.js`).
- **Shortcodes** — Many attraction fields are available as shortcodes (see `includes/trvlr-shortcodes.php`), including booking calendar and payment confirmation.

## Features

- Sync attractions from trvlr with batched runs and conflict detection against local edits  
- Custom post type `trvlr_attraction` with meta boxes, repeaters (pricing, locations), and media gallery  
- REST API (`trvlr/v1`) for the admin React app: settings, sync, logs, setup  
- Scheduled sync, structured logging, optional email notifications  
- Splide-based galleries, payment confirmation iframe page, booking modal/checkout integration  

## Development

- Admin UI is built from `admin/src/` (WordPress scripts, React/WordPress Element). Run your usual `npm` build to produce `admin/build/`.
- Public SCSS sources: `public/src/styles/` → compiled CSS in `public/css/`.

## License

MIT (see `readme.txt`).

## Version

See `trvlr.php` / `readme.txt` for the current stable version.
