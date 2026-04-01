# REST API (dashboard)

## Primary controller

**`includes/class-trvlr-rest-api.php`** registers the **`trvlr/v1`** namespace used by the React settings app via `apiFetch`. Routes are grouped conceptually into:

- **Settings** — Theme, connection, and notification options (`/settings/theme`, `/settings/connection`, `/settings/notifications`).
- **Sync** — Manual runs, schedule, progress, statistics, custom-edit listings, force-sync/clear operations, and bulk delete helpers under `/sync/...`.
- **Logs** — Querying grouped or flat logs, clearing old or all entries, CSV export.
- **Setup** — Payment page creation, connection test, status flags.

All routes use a **`manage_options`** (or equivalent) permission callback unless the implementation defines stricter checks.

## Admin-registered routes

**`Trvlr_Admin::register_theme_rest_routes()`** also registers some **`trvlr/v1`** endpoints (for example theme and connection GET/POST and sync progress). The React code in `admin/src` consistently calls the **`Trvlr_REST_API`** paths listed above (`/settings/...`, `/sync/progress`, etc.). If you add new client features, prefer extending **`Trvlr_REST_API`** to keep a single route table, and avoid duplicating the same URL with two handlers.

## Related documentation

For request/response shapes and query parameters, see **`docs/reference/admin-rest-api.md`**. The narrative pages under **`docs/`** stay behavioral rather than duplicating every payload.
