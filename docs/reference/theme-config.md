# Theme configuration (reference)

## Overview

Theme tokens (colors, card layout, badges, grid gaps) are defined in PHP, exposed to the admin app and the front end, and rendered as **CSS custom properties** on `:root` so public CSS can theme cards and headings without hard-coding values.

## Source of truth

| Layer | Location | Role |
|-------|------------|------|
| PHP | `includes/class-trvlr-theme-config.php` | `get_config()` structure: groups, labels, field types, defaults, `cssVar` names. Defaults merging, CSS string generation, and localization for React. |
| React | `admin/src/settings-pages/theme-settings.tsx` | Theme tab UI: reads `themeConfig` / `processedThemeConfig` from `TrvlrContext`, renders `ThemeField` and `AttractionCardPreview`. |
| Components | `admin/src/components/theme-field.tsx`, `theme-preview.tsx` | Field controls and live card preview. |

Historical notes referred to a separate `themeConfig.ts`; the shipping plugin uses **`Trvlr_Theme_Config::get_config()`** as the canonical definition. If you add a TS file later, keep it in strict parity with PHP.

## Adding a variable

1. Add the field to **`Trvlr_Theme_Config::get_config()`** with `label`, `type` (`color`, `range`, `text`, …), `default`, optional `min` / `max` / `step` / `unit`, and `cssVar` (e.g. `--trvlr-primary-color`).
2. Register the option key in WordPress if needed (see `register_setting` for `trvlr_theme_settings` in `class-trvlr-admin.php` REST schema).
3. Use the variable in public CSS: `color: var(--trvlr-heading-color);`
4. Rebuild admin assets if you change React-only code: `npm run build` in `admin/`.

## Data flow

1. **`get_initial_data()`** includes merged theme settings and the processed config for the React mount.
2. User changes are saved via **`POST /wp-json/trvlr/v1/settings/theme`** (see `admin-rest-api.md`).
3. **`Trvlr_Public::output_theme_css_variables()`** prints `:root { … }` on the front end from merged settings.

## Typical CSS variables

Exact keys follow `get_config()`. Examples include:

- Colors: `--trvlr-primary-color`, `--trvlr-primary-active-color`, `--trvlr-accent-color`, `--trvlr-heading-color`, `--trvlr-text-muted-color`
- Cards: `--trvlr-card-background`, `--trvlr-card-padding`, `--trvlr-card-border-radius`, `--trvlr-card-image-border-radius`
- Layout: grid gap and row gap, heading letter spacing
- Badges: popular badge color, background, font size

Inspect `class-trvlr-theme-config.php` for the authoritative list.

## Utilities (PHP)

- `Trvlr_Theme_Config::merge_with_defaults( $user_settings )`
- `Trvlr_Theme_Config::generate_css_variables( $settings )`

React helpers (`getThemeDefaults`, `mergeWithDefaults`, `getAllFieldsFromConfig`) live on the context side and must stay consistent with the PHP shape.
