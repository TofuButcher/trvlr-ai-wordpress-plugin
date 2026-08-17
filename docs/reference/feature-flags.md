# Feature flags (connection / integration)

Implementation: `includes/trvlr-feature-flags.php`. Options are stored in the WordPress options table and exposed on **GET/POST** `/trvlr/v1/settings/connection` together with `organisation_id` and `api_key`. Defaults are **false** (all features on). Admin UI: **Tools** page (feature toggles); organisation ID / API key stay on **Connection**.

| Option | When true |
|--------|-----------|
| `trvlr_disable_attraction_post_type` | Do not register `trvlr_attraction` or `trvlr_attraction_tag`. Attraction meta boxes are not loaded. Saving toggles this value triggers a **rewrite flush**. |
| `trvlr_disable_attraction_sync` | No catalog sync, no single-attraction sync, scheduled sync and batch cron no-ops; REST manual sync and schedule enable return errors; AJAX sync handlers reject. If the post type is disabled, sync is **forced off** in storage and the UI locks the sync toggle. |
| `trvlr_disable_frontend_booking` | Do not enqueue `trvlr-bookings.js` or its `trvlrConfig` localization. Splide, `trvlr-public.js`, styles, theme variables, and the payment confirmation page iframe are unchanged. |
| `trvlr_disable_attraction_seo_schema` | Do not output attraction JSON-LD (TouristAttraction / FAQPage) or inject into Rank Math / Yoast. Default is **off** (schema on). |

Effective sync suppression is `trvlr_is_attraction_sync_disabled()`: true when either **post type** or **sync** flag is enabled.

When sync becomes disabled (including via the post-type flag), **scheduled sync is unscheduled** on save.
