# REST API reference (`trvlr/v1`)

## General

| | |
|--|--|
| Base URL | `/wp-json/trvlr/v1` |
| Authentication | Cookie auth + `X-WP-Nonce` header (`wp_rest` nonce); user must have `manage_options`. |
| Implementation | `includes/class-trvlr-rest-api.php` |

Some routes are also registered on `Trvlr_Admin` (e.g. alternate paths for theme/progress). The React app uses the paths below.

---

## Settings

### Theme

```
GET  /settings/theme
POST /settings/theme
```

**POST body:** JSON object of theme keys (colors, card spacing, badge options, etc.) as stored in `trvlr_theme_settings`. See `Trvlr_Theme_Config` and `docs/reference/theme-config.md`.

### Connection

```
GET  /settings/connection
POST /settings/connection
```

**POST body example:**

```json
{
  "organisation_id": "your-subdomain",
  "api_key": ""
}
```

### Notifications

```
GET  /settings/notifications
POST /settings/notifications
```

**POST body example:**

```json
{
  "email": "user@example.com",
  "notify_errors": true,
  "notify_complete": false,
  "notify_weekly": false
}
```

---

## Sync

### Statistics

```
GET /sync/stats
```

**Response example:**

```json
{
  "total_attractions": 42,
  "synced_count": 35,
  "custom_edit_count": 7
}
```

### Manual sync

```
POST /sync/manual
```

Starts a batched full sync (see `Trvlr_Sync::start_sync`). Response depends on implementation (success flag and message).

### Schedule

```
GET  /sync/schedule
POST /sync/schedule
```

**POST body example:**

```json
{
  "enabled": true,
  "frequency": "daily"
}
```

`frequency`: `hourly`, `twicedaily`, `daily`, or `weekly`.

### Custom edits

```
GET /sync/custom-edits
```

Returns an array of attractions with manual edits (titles, edit links, modified date, edited field keys, force-sync selections, human-readable labels).

### Force sync

```
POST /sync/force-sync
```

**POST body example:**

```json
{
  "force_sync_fields": {
    "123": ["post_title", "trvlr_description"],
    "456": ["trvlr_media"]
  }
}
```

### Clear force sync

```
POST /sync/clear-force-sync
```

### Delete data

```
POST /sync/delete
```

Register `include_media` as a boolean (body or JSON depending on client). Destructive: removes attraction posts and optionally attachments.

---

## Logs

### List

```
GET /logs
```

| Query param | Type | Default | Description |
|-------------|------|---------|-------------|
| `limit` | int | 50 | Max rows. |
| `grouped` | bool | true | Group by sync session when supported. |
| `type` | string | — | Filter by `log_type`. |

### Clear old

```
POST /logs/clear-old
```

Typically removes entries older than the retention policy (see logger implementation).

### Clear all

```
POST /logs/clear-all
```

### Export

```
GET /logs/export
```

| Query param | Description |
|-------------|-------------|
| `limit` | Optional cap. |
| `type` | Log type filter. |
| `date_from`, `date_to` | `YYYY-MM-DD` range. |

Returns CSV payload (or JSON wrapping CSV) for download.

---

## Setup / status

### Status

```
GET /setup/status
```

Payment page presence, API test metadata, etc.

### Payment page

```
POST /setup/payment-page
```

Creates or links the payment confirmation page when missing.

### Test connection

```
POST /setup/test-connection
```

Behavior depends on current implementation in the REST class.

---

## Sync progress (admin class)

Progress polling for the dashboard may use `Trvlr_Admin::get_sync_progress_rest` at:

```
GET /trvlr/v1/sync/progress
```

(Registered on the admin class; returns transients and option state for in-progress runs.)

---

## Testing

From the browser console on an admin page (with REST nonce):

```js
wp.apiFetch({ path: '/trvlr/v1/sync/stats' }).then(console.log);
```

Or HTTP clients: `GET https://example.com/wp-json/trvlr/v1/sync/stats` with `X-WP-Nonce` and authenticated cookies.

---

## AJAX

Legacy **`admin-ajax.php`** actions remain for the classic attraction editor (e.g. single-post sync) and some tools. Prefer REST for new TRVLR settings UI work.
