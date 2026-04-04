# Booking and integrations

## Booking modal and checkout

When **`trvlr_disable_frontend_booking`** is off (default), front-end booking is driven by **`trvlr-bookings.js`** (class `TrvlrBookingSystem`). On init it validates configuration (`baseIframeUrl` from localized `trvlrConfig`), ensures the **dialog** and **checkout iframe** wrappers exist in the DOM (injecting them if missing), wires click handlers for elements with `attraction-id` and classes such as **`trvlr-book-now`** and **`trvlr-check-availability`**, loads iframe URLs under the trvlr base domain, and coordinates cart state and messaging with the embedded apps.

If **Disable frontend booking** is enabled in Connection → TRVLR features, that script is not loaded; shortcodes and templates may still output static markup or custom iframes. See [feature flags](../reference/feature-flags.md).

The PHP method `Trvlr_Public::inject_booking_modal()` mirrors the same HTML structure but is **not** registered on a hook in the default loader; the JavaScript path is the one that runs on typical requests.

## Template tags and shortcodes

Reusable PHP functions in `includes/trvlr-template-functions.php` output HTML for attraction fields (gallery, pricing, calendar embed, etc.). `includes/trvlr-shortcodes.php` exposes the same building blocks as shortcodes (e.g. `trvlr_title`, `trvlr_gallery`, `trvlr_booking_calendar`, `trvlr_payment_confirmation`) so content can be composed in the block editor or classic content areas without editing the single template.

## Calendar and payment shortcodes

The booking calendar and payment confirmation shortcodes render iframes or containers that point at the configured trvlr host, consistent with the booking script’s base URL.

## Admin preview

The TRVLR settings page loads selected **public CSS** so connection/theme tabs can show card or layout previews that align with the live site’s look.
