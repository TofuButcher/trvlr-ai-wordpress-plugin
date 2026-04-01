# Operations: scheduling, logging, and email

## Scheduled sync

The scheduler registers a recurring **WP-Cron** event when automatic sync is enabled. Supported intervals include hourly, twice daily, daily, and weekly (weekly is registered as a schedule if WordPress does not already provide it). Options record whether sync is enabled and which frequency is active.

When the scheduled hook fires, the sync engine starts a **full catalog sync** the same way as a manual start, including batched processing.

**Operational note:** WP-Cron is driven by site traffic unless a real system cron triggers `wp-cron.php`. Low-traffic sites may see delayed runs.

## Batch continuation

Full sync processes only a few attractions per step, then schedules a **single** cron event to continue. This hook is separate from the recurring “start sync” event; it only advances an in-progress run stored in options.

## Logging

Sync and related events are written to a **custom table** created on activation (and migrated if columns are added in newer versions). Each row has a type, message, optional JSON details, timestamp, user context when applicable, and a **session id** that ties log lines to one sync run.

The logger can also mirror messages to the PHP error log for server-level debugging. A daily cleanup cron trims old entries according to plugin policy.

Admin tools (export, clear, filters) build on this table; specifics of the UI belong in the admin documentation.

## Email notifications

A notifier sends HTML email to a configurable address (defaulting to the site admin email) when enabled. Typical cases include:

- **Sync finished** — Summary counts of created, updated, skipped, and errors.
- **Sync error** — Failures that prevent or disrupt processing for an attraction or the run.

Weekly summary emails are scheduled separately when that feature is enabled. Individual notification types can be toggled in settings.

## Deactivation

Deactivation unschedules the log cleanup, sync, and weekly summary events so no orphaned hooks remain. It does not delete historical log rows.
