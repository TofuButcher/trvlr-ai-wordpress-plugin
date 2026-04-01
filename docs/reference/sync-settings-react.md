# Sync tab (React) — reference

## Scope

The **Sync** tab in **TRVLR → Settings** is implemented in `admin/src/settings-pages/sync-settings.tsx`. It covers statistics, manual sync, schedule controls, custom-edit / force-sync management, and destructive cleanup actions. Data flows through **`TrvlrContext`** and the **`trvlr/v1`** REST API.

## UI areas

| Area | Behavior |
|------|----------|
| Statistics | Totals for attractions, “synced” vs posts with custom edits (from initial payload and/or refresh). |
| Manual sync | Starts a full catalog sync via REST; progress is polled (see `/sync/progress` on the admin class or sync state). |
| Schedule | Enable/disable WP-Cron sync, frequency, display of next run time. |
| Custom edits | Lists posts with `_trvlr_has_custom_edits`, editable force-sync field selections per post. |
| Danger zone | Bulk delete posts and optionally media; confirmations in UI. |

## REST endpoints (typical)

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/trvlr/v1/sync/stats` | Aggregate counts. |
| POST | `/trvlr/v1/sync/manual` | Start manual sync. |
| GET / POST | `/trvlr/v1/sync/schedule` | Read or save schedule. |
| GET | `/trvlr/v1/sync/custom-edits` | Rows for the custom-edits table. |
| POST | `/trvlr/v1/sync/force-sync` | Save per-post force-sync field maps. |
| POST | `/trvlr/v1/sync/clear-force-sync` | Clear force-sync selections. |
| POST | `/trvlr/v1/sync/delete` | Bulk delete; body/query may include `include_media`. |

Full request/response shapes: **`admin-rest-api.md`**.

## Related code

- `admin/src/context/TrvlrContext.jsx` — `triggerManualSync`, `saveScheduleSettings`, `deleteData`, stats refresh.
- `includes/class-trvlr-rest-api.php` — Route registration.
- `core/class-trvlr-sync.php`, `includes/class-trvlr-scheduler.php` — Server-side sync and cron.
