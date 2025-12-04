<?php

// Temporary Test Function
function trvlr_run_api_test() {
    // Only run if specific query param is present
    // Visit: https://your-site.com/?trvlr_test_api=1
    if ( ! isset( $_GET['trvlr_test_api'] ) ) {
        return;
    }

    // Define Log Function
    function trvlr_test_log($message, $data = null) {
        $timestamp = date('Y-m-d H:i:s');
        $log_message = $message;
        if ($data) {
            $log_message .= " " . print_r($data, true);
        }
        $file_entry = "[" . $timestamp . "] " . $log_message . "\n";
        file_put_contents(plugin_dir_path(__FILE__) . 'api-test.log', $file_entry, FILE_APPEND);
        error_log("[TRVLR API TEST] " . $log_message);
    }

    trvlr_test_log("--- Starting Sync (Full Mock Mode) ---");

    require_once plugin_dir_path( __FILE__ ) . 'core/class-trvlr-sync.php';
    $syncer = new Trvlr_Sync();
    
    // This now triggers the logic to loop through all items in multiple-attractions.json
    // and merge them with details from single-attraction.json
    $syncer->sync_all(); 

    trvlr_test_log("--- Test Complete ---");
    die('Test complete (Full Mock Mode). Check api-test.log and Attractions list. You should see ~30 attractions populated.');
}
add_action('init', 'trvlr_run_api_test');
