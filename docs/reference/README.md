# Reference and technical specifications

This folder holds **detailed** material: field lists, REST payloads, theme token wiring, and React admin behavior. Narrative overviews live in `docs/core/`, `docs/admin/`, and `docs/public/`.

## Naming

Use **kebab-case** filenames only.

## Contents

| File | Description |
|------|-------------|
| [attraction-post-type.md](attraction-post-type.md) | CPT, taxonomy, meta keys, internal sync/edit flags. |
| [theme-config.md](theme-config.md) | PHP theme config, CSS variables, how to add tokens. |
| [admin-rest-api.md](admin-rest-api.md) | `trvlr/v1` endpoints, methods, example bodies. |
| [sync-settings-react.md](sync-settings-react.md) | Sync tab responsibilities and REST map. |
| [react-context-provider.md](react-context-provider.md) | `TrvlrProvider`, `trvlrInitialData`, `useTrvlr()`. |
| [feature-flags.md](feature-flags.md) | `trvlr_disable_attraction_post_type`, sync, and front-end booking options. |

## Dropped material

- **Migration / changelog write-ups** (old `api-migration-changes.md`) were not ported; use git history.
- **Obsolete development outline** (`development-outline.md`) was superseded by `docs/README.md`.

## Optional archive

If you need to keep one-off snapshots, add `docs/reference/archive/` and link them here sparingly.
