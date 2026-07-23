<?php

/**
 * Email notifications for TRVLR sync events.
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

class Trvlr_Notifier
{
	/**
	 * @param string $subject
	 * @param string $message
	 * @param array  $headers
	 * @return bool
	 */
	private static function send_email($subject, $message, $headers = array())
	{
		$admin_email = get_option('trvlr_notification_email', get_option('admin_email'));
		
		if (empty($admin_email)) {
			return false;
		}

		$default_headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
		);

		$headers = array_merge($default_headers, $headers);

		return wp_mail($admin_email, $subject, $message, $headers);
	}

	/**
	 * @param string $error_message
	 * @param array  $context
	 */
	public static function notify_sync_error($error_message, $context = array())
	{
		if (!self::is_notification_enabled('sync_errors')) {
			return;
		}

		$subject = '[TRVLR] Sync Error - ' . get_bloginfo('name');
		
		$message = '<h2>TRVLR Sync Error</h2>';
		$message .= '<p><strong>Error:</strong> ' . esc_html($error_message) . '</p>';
		$message .= '<p><strong>Time:</strong> ' . current_time('Y-m-d H:i:s') . '</p>';
		
		if (!empty($context)) {
			$message .= '<h3>Context:</h3>';
			$message .= '<pre>' . esc_html(print_r($context, true)) . '</pre>';
		}
		
		$message .= '<p><a href="' . admin_url('admin.php?page=trvlr-settings&tab=logs') . '">View Full Logs</a></p>';

		self::send_email($subject, $message);
		
		Trvlr_Logger::log('notification', 'Error notification sent', array(
			'error' => $error_message,
			'email' => get_option('trvlr_notification_email', get_option('admin_email'))
		));
	}

	/**
	 * @param int $created
	 * @param int $updated
	 * @param int $skipped
	 * @param int $errors
	 */
	public static function notify_sync_complete($created, $updated, $skipped, $errors = 0)
	{
		if (!self::is_notification_enabled('sync_complete')) {
			return;
		}

		// Skip no-op syncs (no changes and no errors).
		if ($created === 0 && $updated === 0 && $errors === 0) {
			return;
		}

		$subject = '[TRVLR] Sync Complete - ' . get_bloginfo('name');
		
		$message = '<h2>TRVLR Sync Completed</h2>';
		$message .= '<p><strong>Time:</strong> ' . current_time('Y-m-d H:i:s') . '</p>';
		$message .= '<h3>Summary:</h3>';
		$message .= '<ul>';
		$message .= '<li><strong>Created:</strong> ' . $created . '</li>';
		$message .= '<li><strong>Updated:</strong> ' . $updated . '</li>';
		$message .= '<li><strong>Skipped:</strong> ' . $skipped . '</li>';
		if ($errors > 0) {
			$message .= '<li><strong style="color: red;">Errors:</strong> ' . $errors . '</li>';
		}
		$message .= '</ul>';
		
		$message .= '<p><a href="' . admin_url('admin.php?page=trvlr-settings&tab=sync') . '">View Sync Stats</a></p>';

		self::send_email($subject, $message);
	}

	public static function send_weekly_summary()
	{
		if (!self::is_notification_enabled('weekly_summary')) {
			return;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'trvlr_sync_logs';
		$week_ago = date('Y-m-d H:i:s', strtotime('-7 days'));

		$stats = $wpdb->get_results($wpdb->prepare(
			"SELECT log_type, COUNT(*) as count FROM {$table_name} 
			WHERE created_at >= %s 
			GROUP BY log_type",
			$week_ago
		));

		$subject = '[TRVLR] Weekly Summary - ' . get_bloginfo('name');
		
		$message = '<h2>TRVLR Weekly Summary</h2>';
		$message .= '<p><strong>Period:</strong> ' . date('Y-m-d', strtotime('-7 days')) . ' to ' . date('Y-m-d') . '</p>';
		
		if (empty($stats)) {
			$message .= '<p>No sync activity this week.</p>';
		} else {
			$message .= '<h3>Activity:</h3>';
			$message .= '<ul>';
			foreach ($stats as $stat) {
				$message .= '<li><strong>' . ucfirst(str_replace('_', ' ', $stat->log_type)) . ':</strong> ' . $stat->count . '</li>';
			}
			$message .= '</ul>';
		}

		$total = wp_count_posts('trvlr_attraction')->publish;
		$with_edits = get_posts(array(
			'post_type' => 'trvlr_attraction',
			'posts_per_page' => -1,
			'fields' => 'ids',
			'meta_query' => array(
				array(
					'key' => '_trvlr_has_custom_edits',
					'value' => '1'
				)
			)
		));

		$message .= '<h3>Current Status:</h3>';
		$message .= '<ul>';
		$message .= '<li><strong>Total Attractions:</strong> ' . $total . '</li>';
		$message .= '<li><strong>With Custom Edits:</strong> ' . count($with_edits) . '</li>';
		$message .= '<li><strong>Synced (No Edits):</strong> ' . ($total - count($with_edits)) . '</li>';
		$message .= '</ul>';
		
		$message .= '<p><a href="' . admin_url('admin.php?page=trvlr-settings&tab=sync') . '">View Sync Settings</a></p>';

		self::send_email($subject, $message);
		
		Trvlr_Logger::log('notification', 'Weekly summary sent');
	}

	/**
	 * @param string $type
	 * @return bool
	 */
	private static function is_notification_enabled($type)
	{
		$enabled_notifications = get_option('trvlr_enabled_notifications', array());
		return in_array($type, $enabled_notifications);
	}

	/**
	 * @return array
	 */
	public static function get_notification_types()
	{
		return array(
			'sync_errors' => __('Sync Errors', 'trvlr'),
			'sync_complete' => __('Sync Completion (when changes occur)', 'trvlr'),
			'weekly_summary' => __('Weekly Summary Report', 'trvlr')
		);
	}

	public static function schedule_weekly_summary()
	{
		if (!wp_next_scheduled('trvlr_weekly_summary')) {
			// Intentionally Monday 9:00 local time.
			$next_monday = strtotime('next monday 9:00');
			wp_schedule_event($next_monday, 'weekly', 'trvlr_weekly_summary');
		}
	}

	public static function unschedule_weekly_summary()
	{
		$timestamp = wp_next_scheduled('trvlr_weekly_summary');
		if ($timestamp) {
			wp_unschedule_event($timestamp, 'trvlr_weekly_summary');
		}
	}
}
