<?php

/**
 * Scheduled sync functionality for TRVLR
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

class Trvlr_Scheduler
{
	/**
	 * Schedule automatic sync
	 * 
	 * @param string $frequency Recurrence: hourly, twicedaily, daily, weekly
	 */
	public static function schedule_sync($frequency = 'daily')
	{
		// Unschedule existing first
		self::unschedule_sync();

		// Validate frequency
		$valid_frequencies = array('hourly', 'twicedaily', 'daily', 'weekly');
		if (!in_array($frequency, $valid_frequencies)) {
			$frequency = 'daily';
		}

		// Schedule new event
		wp_schedule_event(time(), $frequency, 'trvlr_scheduled_sync');
		update_option('trvlr_sync_frequency', $frequency);
		update_option('trvlr_sync_enabled', '1');

		Trvlr_Logger::log('system', "Scheduled sync enabled: {$frequency}");
	}

	/**
	 * Unschedule automatic sync
	 */
	public static function unschedule_sync()
	{
		$timestamp = wp_next_scheduled('trvlr_scheduled_sync');
		if ($timestamp) {
			wp_unschedule_event($timestamp, 'trvlr_scheduled_sync');
		}
		update_option('trvlr_sync_enabled', '0');
	}

	/**
	 * Check if scheduled sync is enabled
	 * 
	 * @return bool
	 */
	public static function is_sync_enabled()
	{
		return get_option('trvlr_sync_enabled', '0') === '1';
	}

	/**
	 * Get current sync frequency
	 * 
	 * @return string
	 */
	public static function get_sync_frequency()
	{
		return get_option('trvlr_sync_frequency', 'daily');
	}

	/**
	 * Get next scheduled sync time
	 * 
	 * @return int|false Timestamp or false
	 */
	public static function get_next_sync_time()
	{
		return wp_next_scheduled('trvlr_scheduled_sync');
	}

	/**
	 * Run scheduled sync (callback for WP-Cron)
	 */
	public static function run_scheduled_sync()
	{
		if (!self::is_sync_enabled()) {
			return;
		}

		Trvlr_Logger::log('sync_start', 'Scheduled sync started (automated)');

		require_once plugin_dir_path(dirname(__FILE__)) . 'core/class-trvlr-sync.php';
		$sync = new Trvlr_Sync();
		$sync->sync_all();
	}

	/**
	 * Add custom cron schedules
	 * 
	 * @param array $schedules Existing schedules
	 * @return array Modified schedules
	 */
	public static function add_cron_schedules($schedules)
	{
		if (!isset($schedules['weekly'])) {
			$schedules['weekly'] = array(
				'interval' => 604800, // 7 days in seconds
				'display'  => __('Once Weekly', 'trvlr')
			);
		}
		return $schedules;
	}
}

