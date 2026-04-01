# Trvlr React context (`TrvlrProvider`)

## Purpose

`admin/src/context/TrvlrContext.jsx` centralizes settings, sync summaries, and system flags for the TRVLR settings app. **`window.trvlrInitialData`** (from `Trvlr_Admin::get_initial_data()` + `wp_localize_script`) seeds the first render so tabs can paint without waiting on redundant GETs for small payloads.

## Initial payload (conceptual)

The PHP array typically includes:

- **`settings.theme`** — Merged theme option object.
- **`settings.connection`** — `organisation_id` (and related keys as stored).
- **`settings.notifications`** — Email and notification toggles.
- **`themeConfig`** / processed config — Structure from `Trvlr_Theme_Config` for the Theme tab.
- **`sync.stats`** — Totals and custom-edit counts.
- **`sync.schedule`** — Enabled flag, frequency, next run timestamp string.
- **`system.payment_page`** — Exists, URL, ID.
- **`nonce`** — Admin AJAX nonce; **`restNonce`** / **`restRoot`** are also set via `wpApiSettings` for `apiFetch`.

Large lists (logs, full custom-edit tables) may still be loaded lazily via REST when a tab opens.

## Consumer pattern

Components call **`useTrvlr()`** for:

- Theme: `themeSettings`, `saveThemeSettings`, `themeConfig`, `processedThemeConfig`
- Connection / notifications: `connectionSettings`, `saveConnectionSettings`, etc.
- Sync: `syncStats`, `triggerManualSync`, `saveScheduleSettings`, `refreshSyncStats`, `deleteData`
- System: `systemStatus`, `createPaymentPage`, `testApiConnection`
- UI: `saving`, `refreshing`

Writes use **`apiFetch`** against `/trvlr/v1/...` routes registered in `includes/class-trvlr-rest-api.php`.

## When to extend

1. Add fields to **`get_initial_data()`** when every tab needs them immediately.
2. Add state and mutators to the provider when multiple components share the same data.
3. Prefer lazy **`apiFetch`** for heavy or rare data (e.g. log streams).

## Debugging

In the browser console on the TRVLR settings screen: `window.trvlrInitialData` and React DevTools for context value.
