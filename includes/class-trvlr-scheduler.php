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

	/**
	 * @param string|null $session_id Optional session from queued action args.
	 */
	public static function run_sync_batch($session_id = null)
	{
		if (function_exists('trvlr_is_attraction_sync_disabled') && trvlr_is_attraction_sync_disabled()) {
			return;
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'core/class-trvlr-sync.php';
		$sync = new Trvlr_Sync();
		$sync->process_batch(null, is_string($session_id) ? $session_id : null);
	}

	/**
	 * Loopback runner endpoint (admin-ajax, token-gated, works logged-out).
	 * Lets batches chain without WP-Cron on low-resource hosts.
	 */
	public static function ajax_run_sync_batch()
	{
		$token = isset($_REQUEST['token']) ? sanitize_text_field(wp_unslash($_REQUEST['token'])) : '';
		$session = isset($_REQUEST['session']) ? sanitize_text_field(wp_unslash($_REQUEST['session'])) : '';

		if ($token === '' || !Trvlr_Async::verify_runner_token($token)) {
			status_header(403);
			wp_die('', '', array('response' => 403));
		}

		ignore_user_abort(true);

		// Release the HTTP connection early so the caller never blocks on us.
		if (function_exists('fastcgi_finish_request')) {
			status_header(200);
			echo 'ok';
			fastcgi_finish_request();
		}

		self::run_sync_batch($session !== '' ? $session : null);

		wp_die('ok');
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
