1) Fetch current config

Use ac_get_listscreen() or ac_get_columns() to read the existing configuration for the attractions list table. The docs describe ac_get_listscreen() as retrieving a single table configuration and ac_get_columns() as retrieving the list of columns and settings.

php
<?php
// Only run if Admin Columns Pro is available.
if ( function_exists( 'ac_get_listscreen' ) ) {
    $listscreen = ac_get_listscreen( 'post', 'attractions' );

    if ( $listscreen ) {
        $columns = $listscreen->get_columns();

        // Inspect or store the config however you want.
        // For example, dump to error log during development:
        error_log( print_r( $columns, true ) );
    }
}

If you prefer the more direct helper, the docs also expose ac_get_columns() for column retrieval.

php
<?php
if ( function_exists( 'ac_get_columns' ) ) {
    $columns = ac_get_columns( 'post', 'attractions' );
    error_log( print_r( $columns, true ) );
}

2) Apply saved config

Admin Columns Pro supports loading column settings from PHP/local storage, and the docs state that generated PHP settings can be replaced with Local Storage so settings are loaded from a directory on disk . That means you can keep the exported config in your plugin and load it when AC is present.

php
<?php
add_action( 'plugins_loaded', function () {
    if ( ! function_exists( 'ac_load_columns' ) && ! function_exists( 'ac_register_columns' ) ) {
        return;
    }

    $config = require plugin_dir_path( __FILE__ ) . 'admin-columns/attractions-columns.php';

    if ( function_exists( 'ac_load_columns' ) ) {
        ac_load_columns( $config );
    } elseif ( function_exists( 'ac_register_columns' ) ) {
        ac_register_columns( $config );
    }
});

And your exported config file could look like this:

php
<?php
return array(
    // Paste the exported Admin Columns PHP config here.
);