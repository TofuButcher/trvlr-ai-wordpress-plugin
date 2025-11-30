# Trvlr Plugin Development Outline

## 1. File Structure Map
This structure moves towards an Object-Oriented approach (similar to the WordPress Plugin Boilerplate) to maintain scalability as the sync logic and API integrations grow.

```text
trvlr/
├── trvlr.php                        # Main plugin file (Entry point)
├── README.md                        # Documentation
├── uninstall.php                    # Cleanup on deletion
├── includes/                        # Core plugin logic
│   ├── class-trvlr-loader.php       # Orchestrates hooks (Action/Filter registry)
│   ├── class-trvlr-i18n.php         # Internationalization
│   ├── class-trvlr-activator.php    # Activation: Create DB tables, Register CPT (flush rewrite rules)
│   └── class-trvlr-deactivator.php  # Deactivation logic
├── admin/                           # Admin-specific functionality
│   ├── class-trvlr-admin.php        # Main Admin logic (Settings, Menu pages)
│   ├── css/                         # Admin styles
│   ├── js/                          # Admin scripts (Sync progress UI)
│   └── partials/                    # HTML templates for admin screens
│       ├── trvlr-admin-display.php  # Main settings & sync dashboard
│       └── trvlr-admin-debug.php    # Logs & manual tools
├── public/                          # Frontend functionality
│   ├── class-trvlr-public.php       # Frontend hooks (Shortcodes, Template loading)
│   ├── css/                         # Frontend styles
│   ├── js/                          # Frontend scripts (Booking modal logic)
│   └── partials/                    # Frontend templates
│       ├── content-single-attraction.php # Default single tour template
│       └── booking-modal.php        # Booking iframe container
└── core/                            # Business Logic & Data Handling
    ├── class-trvlr-api.php          # API Client (Auth, Fetching data)
    ├── class-trvlr-sync.php         # Sync Engine (Diffing, Updating, Image processing)
    ├── class-trvlr-attraction.php   # Model for 'trvlr_attraction' CPT (Getters/Setters/Helpers)
    └── class-trvlr-logger.php       # Custom logging handler
```

## 2. Development Plan

### Phase 1: Foundation & Data Structure (Backend)
**Goal:** Establish the plugin structure and the custom data container (`trvlr_attraction` CPT) to hold the API data.

1.  **Refactor Entry Point**: Update `trvlr.php` to instantiate the core `Trvlr` class (which loads dependencies).
2.  **CPT Registration**:
    *   Create `includes/class-trvlr-activator.php` and `core/class-trvlr-attraction.php`.
    *   Register `trvlr_attraction` Post Type.
    *   Register associated Taxonomies (e.g., `trvlr_attraction_type`, `trvlr_destination`).
    *   *Note:* Ensure `supports` includes `title`, `editor` (for description), `thumbnail` (featured image), and `custom-fields`.
3.  **DB / Options Setup**:
    *   Define options for API credentials (`trvlr_api_key`, `trvlr_business_id`).
    *   Create a custom table or option for Sync Logs (simple array storage or dedicated table if logs are extensive).

### Phase 2: Admin & API Connection
**Goal:** Allow user to connect to TRVLR AI and see the connection status.

1.  **Admin Dashboard**:
    *   Create `admin/class-trvlr-admin.php`.
    *   Build the Settings Page: Input fields for API Base URL, Organization ID (from API docs).
    *   **Feature**: "Delete All Data" button.
        *   *Logic*: Loop through all `trvlr_attraction` posts -> `wp_delete_post( $id, true )`.
        *   Clear all associated media (optional/configurable, to avoid orphan images).
        *   Clear sync logs.
2.  **API Client**:
    *   Create `core/class-trvlr-api.php`.
    *   Implement `get_attractions()`: Fetches the list (pagination support).
    *   Implement `get_attraction_details($id)`: Fetches single item details.
    *   Add Basic Auth headers handling (placeholder for now).

### Phase 3: The Sync Engine (Core Logic)
**Goal:** Accurately import data and handle updates intelligently ("Smart Sync").

1.  **Sync Manager (`core/class-trvlr-sync.php`)**:
    *   **Import Logic**: Iterate through API results -> Create new `trvlr_attraction` posts.
    *   **Mapping**: Map API fields (`title`, `description`, `images`) to WP fields (`post_title`, `post_content`, `_thumbnail_id`).
    *   **Smart Diffing**:
        *   Store a "Source Hash" or "Last Synced Value" for key fields in post meta (e.g., `_trvlr_sync_hash_description`).
        *   *Logic*: If `post_content` differs from `_trvlr_sync_hash_description` -> User has manually edited. **Skip update** (or flag conflict).
        *   If `post_content` == `_trvlr_sync_hash_description` -> Safe to overwrite with new API data.
    *   **Image Handling**:
        *   Check if image URL exists in Media Library (store URL in meta to prevent dupe downloads).
        *   Sideload image -> Set as Featured Image.
2.  **Manual Sync**:
    *   Add "Sync Now" button to Admin Dashboard.
    *   Add AJAX handler to run sync batch (avoid timeouts).
    *   Display progress bar.

### Phase 4: Frontend Display
**Goal:** Display the data on the site.

1.  **Template Injection**:
    *   Filter `single_template` to use `public/partials/content-single-attraction.php` for `trvlr_attraction` posts.
2.  **Shortcodes**:
    *   `[trvlr_booking_btn]` (Existing logic from your current `bookings.js`).
    *   `[trvlr_attraction_list]` (Grid view of tours).
3.  **Booking Integration**:
    *   Ensure the existing JS/Iframe logic works with the new CPT structure (getting ID from `get_the_ID()` or meta).

