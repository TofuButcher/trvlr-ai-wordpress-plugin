# How to Switch to Real API

Currently, the plugin is configured to use **local JSON files** (`api/multiple-attractions.json` and `api/single-attraction.json`) to simulate the TRVLR API response. This is because the remote API endpoint is currently unreachable or under development.

## Steps to Enable Real API

When the API is stable and you are ready to switch from "Mock Mode" to "Production Mode":

1.  **Update API Client (`core/class-trvlr-api.php`)**:
    *   Locate the `get_attractions()` method.
    *   Remove the block that reads from `file_get_contents(...)`.
    *   Uncomment/Enable the `wp_remote_post(...)` block.
    *   Ensure the `$api_url` points to the correct endpoint (currently `https://lc84mznen7.execute-api.ap-southeast-2.amazonaws.com/production/process/webapi_handler/attractions`).

2.  **Update Single Attraction Fetch (`core/class-trvlr-api.php`)**:
    *   Locate the `get_attraction_details( $id )` method.
    *   Remove the block reading `api/single-attraction.json`.
    *   Enable the `wp_remote_post(...)` block passing the `attraction_id`.

3.  **Verify Headers**:
    *   Ensure the API key/Auth headers (if required in the future) are added to the `wp_remote_post` arguments.

4.  **Delete Mock Files (Optional)**:
    *   You can safely remove `api/multiple-attractions.json` and `api/single-attraction.json` once verified.

