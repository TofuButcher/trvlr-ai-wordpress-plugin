<?php

/**
 * Plugin Name: Trvlr AI Booking System
 * Description: Wordpress plugin for integrating the trvlr.ai booking system.
 * Version: 0.0.3
 * Author: Paris Welch
 * Text Domain: trvlr
 */

if (! defined('ABSPATH')) {
	exit;
}

// Define Constants
define('TRVLR_VERSION', '0.0.3');
define('TRVLR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TRVLR_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-trvlr-activator.php
 */
function activate_trvlr()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-trvlr-activator.php';
	Trvlr_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-trvlr-deactivator.php
 */
function deactivate_trvlr()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-trvlr-deactivator.php';
	Trvlr_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_trvlr');
register_deactivation_hook(__FILE__, 'deactivate_trvlr');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-trvlr.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
function run_trvlr()
{

	$plugin = new Trvlr();
	$plugin->run();
}
run_trvlr();

/* Update Checker */
require_once TRVLR_PLUGIN_DIR . 'plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/TofuButcher/trvlr-ai-wordpress-plugin/',
	__FILE__,
	'trvlr'
);

$myUpdateChecker->setBranch('main');

// Temp test files
if (file_exists(TRVLR_PLUGIN_DIR . 'test-api.php')) {
	require_once TRVLR_PLUGIN_DIR . 'test-api.php';
}

// Data transform testing (access with ?testing=true)
add_action('init', function () {
	if (isset($_GET['test']) && $_GET['test'] == 'true') {
		require_once TRVLR_PLUGIN_DIR . 'core/data-transform-testing.php';
	}

	if (isset($_GET['temp']) && $_GET['temp'] == 'true') {
		// Fetch all attractions, loop through the trvlr_price repeater field and get the price type for each price row.
		// Die() and output to screen a set of all price types used on attractions.

		$attractions = get_posts(array(
			'post_type' => 'trvlr_attraction',
			'posts_per_page' => -1,
			'post_status' => 'publish'
		));

		$price_types = array();

		foreach ($attractions as $attraction) {
			$prices = get_post_meta($attraction->ID, 'trvlr_pricing', true);
			$pricing = is_array($prices) ? $prices : array();

			foreach ($pricing as $price) {
				if (isset($price['type']) && !empty($price['type'])) {
					$price_types[] = $price['type'];
				}
			}
		}

		$unique_price_types = array_unique($price_types);
		sort($unique_price_types);

		echo '<h2>Price Types Found on Attractions:</h2>';
		echo '<ul>';
		foreach ($unique_price_types as $type) {
			echo '<li>' . esc_html($type) . '</li>';
		}
		echo '</ul>';

		echo '<h3>Total attractions: ' . count($attractions) . '</h3>';
		echo '<h3>Total unique price types: ' . count($unique_price_types) . '</h3>';
		echo '<h3>Total price entries: ' . count($price_types) . '</h3>';

		die();
	}
});
