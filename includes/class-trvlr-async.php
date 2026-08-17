<?php

/**
 * Async dispatch abstraction.
 *
 * Routes background scheduling through Action Scheduler when available, otherwise
 * WP-Cron. Action Scheduler is not bundled — progressive enhancement only.
 *
 * Hooks (`trvlr_process_sync_batch`, `trvlr_scheduled_sync`) match those registered
 * in class-trvlr.php so callbacks fire regardless of which driver triggered them.
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

class Trvlr_Async
{
	const GROUP        = 'trvlr';
	const BATCH_HOOK   = 'trvlr_process_sync_batch';
	const SYNC_HOOK    = 'trvlr_scheduled_sync';
	const RUNNER_TOKEN = 'trvlr_sync_runner_token';
	const RUNNER_ACTION = 'trvlr_run_sync_batch';

	/**
	 * @return bool
	 */
	public static function is_available(): bool
	{
		return function_exists('as_enqueue_async_action')
			&& function_exists('as_unschedule_all_actions')
			&& function_exists('as_next_scheduled_action');
	}

	/**
	 * @return string 'action_scheduler' | 'wp_cron'
	 */
	public static function driver(): string
	{
		return self::is_available() ? 'action_scheduler' : 'wp_cron';
	}

	/**
	 * Queue the next sync batch. No-op if one is already pending.
	 *
	 * @param string|null $session_id Sync session; late-fired jobs no-op on mismatch.
	 */
	public static function queue_batch(?string $session_id = null): void
	{
		if (self::has_batch()) {
			return;
		}

		$args = self::batch_args($session_id);

		if (self::is_available()) {
			as_enqueue_async_action(self::BATCH_HOOK, $args, self::GROUP);
		} else {
			wp_schedule_single_event(time() + 1, self::BATCH_HOOK, $args);
		}
	}

	/**
	 * Queue a batch and nudge a runner now (dashboard self-heal for stalled runs).
	 *
	 * Always fires the loopback runner + spawn_cron, even when a batch event is
	 * already pending — a pending event that never fires must not block healing.
	 *
	 * @param string|null $session_id Sync session; late-fired jobs no-op on mismatch.
	 */
	public static function queue_batch_now(?string $session_id = null): void
	{
		$args = self::batch_args($session_id);

		if (self::is_available()) {
			if (!self::has_batch()) {
				as_enqueue_async_action(self::BATCH_HOOK, $args, self::GROUP);
			}
			self::loopback_runner($session_id);
			return;
		}

		if (!self::has_batch()) {
			wp_schedule_single_event(time(), self::BATCH_HOOK, $args);
		}
		if (function_exists('spawn_cron')) {
			spawn_cron();
		}
		self::loopback_runner($session_id);
	}

	/**
	 * Fire-and-forget loopback request that runs the next batch immediately,
	 * independent of WP-Cron. Token-gated; no-op when no sync is running.
	 *
	 * @param string|null $session_id
	 */
	public static function loopback_runner(?string $session_id = null): void
	{
		$token = get_option(self::RUNNER_TOKEN, '');
		if (!is_string($token) || $token === '') {
			return;
		}

		$url = add_query_arg(
			array(
				'action'  => self::RUNNER_ACTION,
				'token'   => rawurlencode($token),
				'session' => rawurlencode((string) $session_id),
			),
			admin_url('admin-ajax.php')
		);

		wp_remote_post($url, array(
			'timeout'   => 1,
			'blocking'  => false,
			'sslverify' => false,
			'headers'   => array('Connection' => 'close'),
			'cookies'   => array(),
			'body'      => array(),
		));
	}

	/**
	 * Create (or rotate) the loopback runner token for a sync run.
	 */
	public static function create_runner_token(): string
	{
		$token = wp_generate_password(40, false, false);
		update_option(self::RUNNER_TOKEN, $token, false);
		return $token;
	}

	/**
	 * Remove the runner token (sync finished/cancelled/nuked).
	 */
	public static function delete_runner_token(): void
	{
		delete_option(self::RUNNER_TOKEN);
	}

	/**
	 * @param string $token
	 * @return bool
	 */
	public static function verify_runner_token(string $token): bool
	{
		$stored = get_option(self::RUNNER_TOKEN, '');
		return is_string($stored) && $stored !== '' && hash_equals($stored, $token);
	}

	/**
	 * @return bool
	 */
	public static function has_batch(): bool
	{
		if (self::is_available()) {
			return (bool) as_next_scheduled_action(self::BATCH_HOOK, null, self::GROUP);
		}

		$crons = _get_cron_array();
		if (!is_array($crons)) {
			return false;
		}
		foreach ($crons as $hooks) {
			if (isset($hooks[self::BATCH_HOOK])) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Clear pending batches on both drivers (covers mixed/legacy state).
	 */
	public static function clear_batches(): void
	{
		if (self::is_available()) {
			as_unschedule_all_actions(self::BATCH_HOOK, null, self::GROUP);
		}
		wp_clear_scheduled_hook(self::BATCH_HOOK);
	}

	/**
	 * @param string $frequency hourly|twicedaily|daily|weekly
	 */
	public static function schedule_recurring_sync(string $frequency): void
	{
		self::unschedule_recurring_sync();

		if (self::is_available()) {
			$interval = self::frequency_to_seconds($frequency);
			as_schedule_recurring_action(time() + $interval, $interval, self::SYNC_HOOK, array(), self::GROUP);
		} else {
			wp_schedule_event(time(), $frequency, self::SYNC_HOOK);
		}
	}

	/**
	 * Unschedule recurring sync on both drivers.
	 */
	public static function unschedule_recurring_sync(): void
	{
		if (self::is_available()) {
			as_unschedule_all_actions(self::SYNC_HOOK, array(), self::GROUP);
		}
		$timestamp = wp_next_scheduled(self::SYNC_HOOK);
		if ($timestamp) {
			wp_unschedule_event($timestamp, self::SYNC_HOOK);
		}
	}

	/**
	 * @return int|false
	 */
	public static function next_sync_time()
	{
		if (self::is_available()) {
			$next = as_next_scheduled_action(self::SYNC_HOOK, null, self::GROUP);
			return is_int($next) ? $next : false; // `true` means in-progress
		}
		return wp_next_scheduled(self::SYNC_HOOK);
	}

	/**
	 * Map a WP-Cron-style frequency string to seconds for Action Scheduler.
	 *
	 * @param string $frequency
	 * @return int
	 */
	public static function frequency_to_seconds(string $frequency): int
	{
		switch ($frequency) {
			case 'hourly':
				return HOUR_IN_SECONDS;
			case 'twicedaily':
				return 12 * HOUR_IN_SECONDS;
			case 'weekly':
				return WEEK_IN_SECONDS;
			case 'daily':
			default:
				return DAY_IN_SECONDS;
		}
	}

	/**
	 * @param string|null $session_id
	 * @return array
	 */
	private static function batch_args(?string $session_id): array
	{
		return $session_id !== null && $session_id !== '' ? array($session_id) : array();
	}
}
