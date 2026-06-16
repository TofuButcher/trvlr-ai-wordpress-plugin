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

		// Unschedule recurring sync + any pending batch events from an
		// interrupted run, across both schedulers (Action Scheduler / WP-Cron).
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-async.php';
		Trvlr_Async::unschedule_recurring_sync();
		Trvlr_Async::clear_batches();
		delete_transient('trvlr_sync_batch_lock');

		// Unschedule notifications
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-notifier.php';
		Trvlr_Notifier::unschedule_weekly_summary();

		flush_rewrite_rules();
	}
}
