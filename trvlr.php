<?php

/**
 * Plugin Name: Trvlr AI Booking System
 * Description: Wordpress plugin for integrating the trvlr.ai booking system.
 * Version: 0.0.2
 * Author: Paris Welch
 * Text Domain: trvlr
 */

if (!defined('ABSPATH')) {
   exit;
}

define('TRVLR_VERSION', '0.0.2');
define('TRVLR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TRVLR_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once TRVLR_PLUGIN_DIR . 'admin/settings.php';
require_once TRVLR_PLUGIN_DIR . 'booking-system/bookings.php';

function trvlr_enqueue_scripts()
{
   $base_domain = get_option('trvlr_base_domain', '');
   $frontend_enabled = get_option('trvlr_enable_frontend', true);

   if (empty($base_domain) || !$frontend_enabled) {
      return;
   }

   wp_enqueue_script(
      'trvlr-bookings',
      TRVLR_PLUGIN_URL . 'booking-system/bookings.js',
      array(),
      TRVLR_VERSION,
      true
   );

   wp_localize_script('trvlr-bookings', 'trvlrConfig', array(
      'baseIframeUrl' => $base_domain,
      'homeUrl' => home_url()
   ));

   wp_enqueue_style(
      'trvlr-modal-styles',
      TRVLR_PLUGIN_URL . 'booking-system/modal-styles.css',
      array(),
      TRVLR_VERSION
   );
}
add_action('wp_enqueue_scripts', 'trvlr_enqueue_scripts');

/* Update Checker */
require TRVLR_PLUGIN_DIR . 'plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
   'https://github.com/TofuButcher/trvlr-ai-wordpress-plugin/',
   __FILE__,
   'trvlr'
);

$myUpdateChecker->setBranch('main');

// Optional: For private repos, add authentication
// $myUpdateChecker->setAuthentication('your-github-token-here');