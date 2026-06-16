<?php

/**
 * Async dispatch abstraction.
 *
 * Routes background scheduling through Action Scheduler when it is available on
 * the site, and transparently falls back to native WP-Cron when it is not. This
 * keeps the plugin working everywhere while giving sites that ship Action
 * Scheduler (e.g. WooCommerce, or a future bundled copy) far more reliable,
 * self-propagating batch processing that does not depend on per-batch traffic.
 *
 * Action Scheduler is NOT bundled by this plugin; this is a progressive
 * enhancement that activates only if `as_*()` functions are present.
 *
 * The action hooks used here (`trvlr_process_sync_batch`, `trvlr_scheduled_sync`)
 * are the same hooks registered in class-trvlr.php, so the existing callbacks
 * fire regardless of which scheduler triggered them.
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

class Trvlr_Async
{
	const GROUP       = 'trvlr';
	const BATCH_HOOK  = 'trvlr_process_sync_batch';
	const SYNC_HOOK   = 'trvlr_scheduled_sync';

	/**
	 * Is Action Scheduler usable on this site?
	 */
	public static function is_available(): bool
	{
		return function_exists('as_enqueue_async_action')
			&& function_exists('as_unschedule_all_actions')
			&& function_exists('as_next_scheduled_action');
	}

	/**
	 * Which scheduler is in use ('action_scheduler' | 'wp_cron'). For diagnostics.
	 */
	public static function driver(): string
	{
		return self::is_available() ? 'action_scheduler' : 'wp_cron';
	}

	// ----------------------------------------------------------------
	// Sync batch chaining (run ASAP, one after another)
	// ----------------------------------------------------------------

	/**
	 * Queue the next sync batch. No-op if one is already pending.
	 */
	public static function queue_batch(): void
	{
		if (self::has_batch()) {
			return;
		}

		if (self::is_available()) {
			// Async actions run as soon as possible and Action Scheduler
			// dispatches its own loopback runner, so the chain self-propagates
			// without waiting for the next visitor.
			as_enqueue_async_action(self::BATCH_HOOK, array(), self::GROUP);
		} else {
			wp_schedule_single_event(time() + 1, self::BATCH_HOOK);
		}
	}

	/**
	 * Queue a batch and actively nudge a runner now. Used by the dashboard
	 * self-heal path to recover a stalled run.
	 */
	public static function queue_batch_now(): void
	{
		if (self::is_available()) {
			if (!self::has_batch()) {
				as_enqueue_async_action(self::BATCH_HOOK, array(), self::GROUP);
			}
			// Action Scheduler kicks its async runner on enqueue; nothing else needed.
			return;
		}

		if (!wp_next_scheduled(self::BATCH_HOOK)) {
			wp_schedule_single_event(time(), self::BATCH_HOOK);
		}
		if (function_exists('spawn_cron')) {
			spawn_cron();
		}
	}

	/**
	 * Is a sync batch currently scheduled/pending?
	 */
	public static function has_batch(): bool
	{
		if (self::is_available()) {
			return (bool) as_next_scheduled_action(self::BATCH_HOOK, null, self::GROUP);
		}
		return (bool) wp_next_scheduled(self::BATCH_HOOK);
	}

	/**
	 * Remove all pending sync batches (both drivers, to cover mixed/legacy state).
	 */
	public static function clear_batches(): void
	{
		if (self::is_available()) {
			as_unschedule_all_actions(self::BATCH_HOOK, array(), self::GROUP);
		}
		while (($timestamp = wp_next_scheduled(self::BATCH_HOOK))) {
			wp_unschedule_event($timestamp, self::BATCH_HOOK);
		}
	}

	// ----------------------------------------------------------------
	// Recurring automatic sync
	// ----------------------------------------------------------------

	/**
	 * Schedule the recurring automatic sync.
	 *
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
	 * Remove the recurring automatic sync (both drivers).
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
	 * Next scheduled automatic sync timestamp, or false.
	 *
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
}
