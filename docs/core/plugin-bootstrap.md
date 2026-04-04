# Plugin bootstrap and hook wiring

## Entry point

The main plugin file loads the `Trvlr` class from `includes/class-trvlr.php` and calls `run()`, which registers all WordPress actions and filters through a dedicated loader object. Nothing runs outside the normal WordPress request lifecycle; there is no separate bootstrap binary.

## Loader

`Trvlr_Loader` collects actions and filters in arrays and registers them in one pass. This keeps hook registration centralized and makes it obvious which class owns which hook.

## What gets registered where

**Core (always-on)**

- **Custom post type and taxonomy** — Registered on `init` for `trvlr_attraction` and `trvlr_attraction_tag`, unless `trvlr_disable_attraction_post_type` is enabled (see [feature flags](../reference/feature-flags.md)).
- **Cron callbacks** — Daily log cleanup, scheduled full sync, batched sync continuation, and optional weekly email summary are bound to their hook names.
- **Cron schedules filter** — Ensures intervals such as weekly exist for sync scheduling.
- **Edit tracking** — On `save_post`, after normal save processing, attractions are analyzed for divergences from last synced content (see [data model and change detection](data-model-and-change-detection.md)).

**Admin**

- Settings, menus, asset enqueue, REST route registration, meta boxes, CSV export, and multiple AJAX actions for sync, logs, schedules, notifications, and maintenance operations.

**Public**

- Front-end assets, template selection for attractions, global SVG/icons, fonts, theme CSS variables, filters that normalize displayed duration/time/pricing, and payment-confirmation page behavior.

## Activation and deactivation

**Activation** registers the attraction post type (so rewrite rules flush correctly), creates the sync log database table, creates or links the payment confirmation page, schedules log cleanup and optional notification crons, and flushes rewrite rules.

**Deactivation** unschedules maintenance crons tied to logging, sync, and weekly summaries, and flushes rewrite rules. It does not delete posts or options by default (see uninstall handling if present).

## Dependencies between layers

The main class loads `core/class-trvlr-attraction.php` for CPT definitions, `includes` services (logger, scheduler, field map, edit tracker, REST API, etc.), and then `admin` and `public` classes. Sync execution lives in `core/class-trvlr-sync.php` and is pulled in when a sync actually runs, not at initial load, to keep the default request lighter.
