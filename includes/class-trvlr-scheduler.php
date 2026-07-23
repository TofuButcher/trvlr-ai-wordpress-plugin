<?php

/**
 * Scheduled attraction sync (via Trvlr_Async driver).
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

if (!class_exists('Trvlr_Async')) {
	require_once plugin_dir_path(__FILE__) . 'class-trvlr-async.php';
}

class Trvlr_Scheduler
{
	/**
	 * @param string $frequency hourly|twicedaily|daily|weekly
	 */
	public static function schedule_sync($frequency = 'daily')
	{
		self::unschedule_sync();

		$valid_frequencies = array('hourly', 'twicedaily', 'daily', 'weekly');
		if (!in_array($frequency, $valid_frequencies)) {
			$frequency = 'daily';
		}

		Trvlr_Async::schedule_recurring_sync($frequency);
		update_option('trvlr_sync_frequency', $frequency);
		update_option('trvlr_sync_enabled', '1');

		Trvlr_Logger::log('system', "Scheduled sync enabled: {$frequency} (" . Trvlr_Async::driver() . ")");
	}

	public static function unschedule_sync()
	{
		Trvlr_Async::unschedule_recurring_sync();
		update_option('trvlr_sync_enabled', '0');
	}

	/**
	 * @return bool
	 */
	public static function is_sync_enabled()
	{
		return get_option('trvlr_sync_enabled', '0') === '1';
	}

	/**
	 * @return string
	 */
	public static function get_sync_frequency()
	{
		return get_option('trvlr_sync_frequency', 'daily');
	}

	/**
	 * @return int|false
	 */
	public static function get_next_sync_time()
	{
		return Trvlr_Async::next_sync_time();
	}

	public static function run_scheduled_sync()
	{
		if (function_exists('trvlr_is_attraction_sync_disabled') && trvlr_is_attraction_sync_disabled()) {
			return;
		}

		if (!self::is_sync_enabled()) {
			return;
		}

		Trvlr_Logger::log('sync_start', 'Scheduled sync started (automated)');

		require_once plugin_dir_path(dirname(__FILE__)) . 'core/class-trvlr-sync.php';
		$sync = new Trvlr_Sync();
		$sync->start_sync();
	}

	public static function run_sync_batch()
	{
		if (function_exists('trvlr_is_attraction_sync_disabled') && trvlr_is_attraction_sync_disabled()) {
			return;
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'core/class-trvlr-sync.php';
		$sync = new Trvlr_Sync();
		$sync->process_batch();
	}

	/**
	 * @param array $schedules
	 * @return array
	 */
	public static function add_cron_schedules($schedules)
	{
		if (!isset($schedules['weekly'])) {
			$schedules['weekly'] = array(
				'interval' => 604800, // 7 days
				'display'  => __('Once Weekly', 'trvlr')
			);
		}
		return $schedules;
	}
}
