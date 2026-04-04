# Templates, styles, and scripts

## `Trvlr_Public` role

The public class enqueues front-end assets, swaps in the single-attraction template, injects theme-driven CSS variables, outputs shared SVG symbols, loads fonts, and handles the **payment confirmation** page (body class, canonical redirect guard, iframe content). Optional **`~dev`** files can replace `get_header()` / `get_footer()` in the single template when present for local template development.

## Single attraction template

For singular `trvlr_attraction` views, `template_include` loads `public/partials/single-trvlr_attraction.php` instead of the theme’s single template. That partial builds the layout from **template tag** functions defined in `includes/trvlr-template-functions.php` (title, duration, sale state, gallery with Splide markup, descriptions, accordion, booking calendar snippet, etc.). Post content (`the_content()`) appears in the flow for any extra editor content.

## Styles

- **Compiled CSS** in `public/css/` (`trvlr-public.css`, `trvlr-cards.css`, `trvlr-single-attraction.css`) is what WordPress enqueues.
- **Source** SCSS lives under `public/src/styles/` and should be compiled to those CSS files when you change styling.
- **Splide** carousel assets ship under `public/dist/` (minified JS + CSS) for image galleries.

The admin settings screen optionally loads the same public card/shell CSS so **theme previews** in the dashboard match the front end.

## Scripts

- **`trvlr-public.js`** — jQuery + Splide: gallery sync (main + vertical nav), back-link behavior, and an in-file **SimpleAccordion** implementation for attraction accordions.
- **`trvlr-bookings.js`** — Standalone booking UI: modal dialog, checkout iframe container, `postMessage` handling, cart/update flows, and delegation for `.trvlr-book-now` / `.trvlr-check-availability` controls. Localized as `trvlrConfig` with base iframe URL and home URL. Not enqueued when **`trvlr_disable_frontend_booking`** is enabled (Connection → TRVLR features); see [feature flags](../reference/feature-flags.md).
- **`simple-accordion.js`** — Duplicate accordion helper exists as a separate file; the active single template path relies on the accordion class embedded in `trvlr-public.js` unless you enqueue the standalone file separately.

## Theme variables

`wp_head` prints a `:root` block from `Trvlr_Theme_Config`: saved options are merged with defaults, then converted to CSS custom properties so cards, grids, and badges track **Theme** settings from the admin app without editing CSS by hand.

## Payment confirmation page

The option-stored **payments** page renders an iframe pointed at the trvlr domain’s payment confirmation URL. A `postMessage` listener handles refresh/navigation back to the home URL when the embedded app signals completion. Canonical redirect is relaxed for that page when query arguments are present so return URLs from the gateway work.

## SVG sprite

`wp_footer` prints a hidden SVG block with symbol definitions (star, clock, arrows, plus/minus) referenced by class-based markup in templates and styles.

## Presentation filters

The public class hooks filters named `trvlr_duration`, `trvlr_start_time`, `trvlr_end_time`, and `trvlr_pricing` to normalize duration strings, format times, and relabel certain price rows for display. Template functions apply these filters where the hook names are used in the pipeline.
