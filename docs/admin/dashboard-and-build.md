# Dashboard and build

## Menu and shell

`Trvlr_Admin` registers a top-level **TRVLR** menu (`trvlr_settings`) for users with `manage_options`. The rendered page is minimal PHP: **`partials/trvlr-settings-main.php`** outputs a header partial, an empty **`#trvlr-settings-root`** mount node, and a footer partial. All interactive settings UI is the **React** application mounted on that root.

## Asset pipeline

- **Compiled bundle** — `admin/build/trvlr-admin-root.jsx.js` (plus `.asset.php` dependency manifest), CSS from `build/trvlr-admin-root.jsx.css`, and RTL CSS. If the build folder is missing, an admin notice explains that a release build is required.
- **Source** — `admin/src/` holds the entry (`trvlr-admin-root.jsx`), **context** (`TrvlrProvider`), **settings pages** (Getting Started, Connection, Theme, Sync, Logs), **forms**, and shared components. TypeScript and JSX mix is used depending on the file.
- **Global styles** — `admin/styles/trvlr-admin.scss` feeds `admin/css/trvlr-admin.css` (compile step is outside WordPress).
- **Legacy JS** — `admin/js/trvlr-admin.js` loads on admin pages with a generic `trvlr_admin_vars` nonce for older or non-React interactions.

On the TRVLR settings screen only, the script enqueue also pulls **`@wordpress/components`** styles, localizes **`wpApiSettings`** for `apiFetch`, and passes **`trvlrInitialData`** (see below). For card/theme previews, **public** `trvlr-public.css` and `trvlr-cards.css` are enqueued so previews match the front end.

## React application structure

The root component wraps children in **`TrvlrProvider`**, which holds settings/sync state and implements REST calls (theme, connection, notifications, sync, logs, setup). **`MainSettings`** defines a **tab strip**: Getting Started, Connection, Theme, Sync, Logs. The active tab syncs to the URL query string (`?tab=…`) so links and refreshes preserve context.

Subpages cover onboarding copy, organisation/API connection, **theme tokens** (colors, spacing, badges) with live-oriented preview, **manual and scheduled sync**, custom-edit tooling, log viewers, and destructive/data operations where implemented.

## Connection tab: TRVLR features

The **Connection** tab includes **Organisation ID** and a **TRVLR features** block (subtitle: “Turn off what you don’t need”) with three toggles backed by options `trvlr_disable_attraction_post_type`, `trvlr_disable_attraction_sync`, and `trvlr_disable_frontend_booking`. When the post type is disabled, the sync toggle is forced on and disabled in the UI, and the server enforces no syncing. Details: [feature flags](../reference/feature-flags.md).

## Initial payload

`get_initial_data()` bundles theme settings, connection snapshot, notification preferences, sync statistics (including counts of posts with custom edits), scheduler state, payment page status, REST nonces, and admin AJAX nonce into **`trvlrInitialData`** so the first paint can avoid redundant round trips. The provider may still refetch via REST after mount.

## Registration and AJAX

Classic **`register_setting`** calls expose options to the Options API and REST where `show_in_rest` is configured. Heavy operations (legacy paths) may still use **AJAX** actions registered on `Trvlr_Admin`; the React app favors **`/trvlr/v1/...`** endpoints from `Trvlr_REST_API` for sync, logs, setup, and settings.

## Optional dev class

The main plugin class may load a dev-specific admin extension from **`~dev`** when present; that is for local development only.

## SVG helper

`output_admin_svg_icons()` exists on the admin class for sprite markup used in previews; ensure it is hooked if you rely on those symbols in new markup.
