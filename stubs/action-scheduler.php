<?php
/**
 * Action Scheduler function stubs for static analysis (Intelephense / PHPStan).
 * Not loaded at runtime — AS ships with WooCommerce / the action-scheduler plugin.
 *
 * @package Trvlr
 */

if (!function_exists('as_enqueue_async_action')) {
	/**
	 * @param string $hook
	 * @param array  $args
	 * @param string $group
	 * @param bool   $unique
	 * @param int    $priority
	 * @return int
	 */
	function as_enqueue_async_action($hook, $args = array(), $group = '', $unique = false, $priority = 10)
	{
		return 0;
	}
}

if (!function_exists('as_schedule_recurring_action')) {
	/**
	 * @param int    $timestamp
	 * @param int    $interval_in_seconds
	 * @param string $hook
	 * @param array  $args
	 * @param string $group
	 * @param bool   $unique
	 * @param int    $priority
	 * @return int
	 */
	function as_schedule_recurring_action($timestamp, $interval_in_seconds, $hook, $args = array(), $group = '', $unique = false, $priority = 10)
	{
		return 0;
	}
}

if (!function_exists('as_unschedule_all_actions')) {
	/**
	 * @param string $hook
	 * @param array|null $args
	 * @param string $group
	 * @return void
	 */
	function as_unschedule_all_actions($hook, $args = null, $group = '')
	{
	}
}

if (!function_exists('as_next_scheduled_action')) {
	/**
	 * @param string $hook
	 * @param array|null $args
	 * @param string $group
	 * @return int|bool
	 */
	function as_next_scheduled_action($hook, $args = null, $group = '')
	{
		return false;
	}
}
