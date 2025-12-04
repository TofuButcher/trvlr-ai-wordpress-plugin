<?php

/**
 * Email notification system for TRVLR sync events
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

class Trvlr_Notifier
{
	/**
	 * Send email notification
	 * 
	 * @param string $subject Email subject
	 * @param string $message Email body
	 * @param array  $headers Optional headers
	 * @return bool Success/failure
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
	 * Send sync error notification
	 * 
	 * @param string $error_message Error details
	 * @param array  $context Additional context
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
		
		// Log the notification
		Trvlr_Logger::log('notification', 'Error notification sent', array(
			'error' => $error_message,
			'email' => get_option('trvlr_notification_email', get_option('admin_email'))
		));
	}

	/**
	 * Send sync completion notification
	 * 
	 * @param int $created   Number created
	 * @param int $updated   Number updated
	 * @param int $skipped   Number skipped
	 * @param int $errors    Number of errors
	 */
	public static function notify_sync_complete($created, $updated, $skipped, $errors = 0)
	{
		if (!self::is_notification_enabled('sync_complete')) {
			return;
		}

		// Only send if there were changes or errors
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

	/**
	 * Send weekly summary email
	 */
	public static function send_weekly_summary()
	{
		if (!self::is_notification_enabled('weekly_summary')) {
			return;
		}

		// Get stats from the past week
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

		// Get current attraction stats
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
	 * Check if notification type is enabled
	 * 
	 * @param string $type Notification type
	 * @return bool
	 */
	private static function is_notification_enabled($type)
	{
		$enabled_notifications = get_option('trvlr_enabled_notifications', array());
		return in_array($type, $enabled_notifications);
	}

	/**
	 * Get available notification types
	 * 
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

	/**
	 * Schedule weekly summary
	 */
	public static function schedule_weekly_summary()
	{
		if (!wp_next_scheduled('trvlr_weekly_summary')) {
			// Schedule for Monday at 9am
			$next_monday = strtotime('next monday 9:00');
			wp_schedule_event($next_monday, 'weekly', 'trvlr_weekly_summary');
		}
	}

	/**
	 * Unschedule weekly summary
	 */
	public static function unschedule_weekly_summary()
	{
		$timestamp = wp_next_scheduled('trvlr_weekly_summary');
		if ($timestamp) {
			wp_unschedule_event($timestamp, 'trvlr_weekly_summary');
		}
	}
}

