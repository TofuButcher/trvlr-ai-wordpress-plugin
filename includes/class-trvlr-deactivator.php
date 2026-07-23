<?php

/**
 * Plugin deactivation: clear cron/AS schedules and rewrite rules.
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */
class Trvlr_Deactivator
{

	/**
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-logger.php';
		Trvlr_Logger::unschedule_cleanup();

		// Clear recurring sync + pending batches on both AS and WP-Cron (mixed/legacy state).
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-async.php';
		Trvlr_Async::unschedule_recurring_sync();
		Trvlr_Async::clear_batches();
		delete_transient('trvlr_sync_batch_lock');

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-notifier.php';
		Trvlr_Notifier::unschedule_weekly_summary();

		flush_rewrite_rules();
	}
}
