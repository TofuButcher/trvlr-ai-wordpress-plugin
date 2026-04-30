# Templates, styles, and scripts

## `Trvlr_Public` role

The public class enqueues front-end assets, swaps in the single-attraction template, injects theme-driven CSS variables, outputs shared SVG symbols, loads fonts, and handles the **payment confirmation** page (body class, canonical redirect guard, iframe content). Optional **`~dev`** files can replace `get_header()` / `get_footer()` in the single template when present for local template development.

## Single attraction template

For singular `trvlr_attraction` views, `template_include` loads `public/partials/single-trvlr_attraction.php` instead of the theme’s single template. That partial handles header/footer (or `~dev` replacements) and includes the resolved layout file from **`public/templates/single-attraction/`**. The layout uses **template tag** functions defined in `includes/trvlr-template-functions.php` (title, duration, sale state, gallery with Splide markup, descriptions, accordion, booking calendar snippet, etc.). Post content (`the_content()`) appears in the flow for any extra editor content.

## Card and single PHP templates (`Trvlr_Template_Registry`)

Built-in card markup and single-attraction layouts live under:

- `public/templates/cards/` — one file per card template; slugs (e.g. `card-1`) map to files such as `card-template-1.php`.
- `public/templates/single-attraction/` — one file per single layout (e.g. `single-template-1.php`).

`includes/class-trvlr-template-registry.php` registers card and single templates, runs the `trvlr_register_templates` action so more can be added from outside the plugin, and **presentation themes** bundle a card + single + their theme CSS. The active presentation theme is stored in the option `trvlr_presentation_theme` (e.g. `theme-1`, `theme-2`); the registry maps each to a card slug and a single-attraction slug, syncs the legacy options `trvlr_card_template` and `trvlr_single_attraction_template` to match, and can migrate from older per-field values when a matching pair is found. Use `trvlr_register_presentation_themes` to register additional paired themes after registering the underlying card/single files. The Theme admin screen only exposes the presentation choice; the REST payload includes `presentationTheme` plus derived `cardTemplate` and `attractionPageTemplate` for debugging or integrations. `trvlr_card()` loads the card file; the single shell loads the single file. Root nodes expose `data-trvlr-card-template` / `data-trvlr-single-template` (and BEM-style `--template-{slug}` classes) for scoped styling.

- **Per-template theme CSS** (card and single each use `themes-{slug}.css` for their respective slugs, e.g. `themes-card-1.css` and `themes-page-1.css` from `public/src/styles/themes/`), enqueued for the **active** card and single slugs when those files exist.

## Styles

- **Compiled CSS** in `public/css/` (`trvlr-public.css`, `trvlr-cards.css`, `trvlr-single-attraction.css`) is what WordPress enqueues. For the **active card and single slugs** (from the selected presentation theme), `Trvlr_Public` also enqueues each matching **`themes-{slug}.css`** when that file exists (built from `public/src/styles/themes/` via webpack), after the base card / single CSS.
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
