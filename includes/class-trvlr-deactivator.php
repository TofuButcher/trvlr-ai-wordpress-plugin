<?php

/**
 * Fired during plugin deactivation
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */
class Trvlr_Deactivator
{

	/**
	 * Fired during plugin deactivation.
	 *
	 * This class defines all code necessary to run during the plugin's deactivation.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		// Unschedule cron jobs
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-logger.php';
		Trvlr_Logger::unschedule_cleanup();

		// Unschedule sync cron
		$timestamp = wp_next_scheduled('trvlr_scheduled_sync');
		if ($timestamp) {
			wp_unschedule_event($timestamp, 'trvlr_scheduled_sync');
		}

		// Unschedule notifications
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-notifier.php';
		Trvlr_Notifier::unschedule_weekly_summary();

		flush_rewrite_rules();
	}
}
