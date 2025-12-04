<?php

/**
 * Logging functionality for TRVLR sync operations
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

class Trvlr_Logger
{
	/**
	 * Get the table name for logs
	 */
	private static function get_table_name()
	{
		global $wpdb;
		return $wpdb->prefix . 'trvlr_sync_logs';
	}

	/**
	 * Check if log table exists and create if missing
	 */
	public static function ensure_table_exists()
	{
		global $wpdb;
		$table_name = self::get_table_name();
		
		// Check if table exists
		$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		
		if ($table_exists != $table_name) {
			// Table doesn't exist, create it
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				log_type varchar(50) NOT NULL,
				message text NOT NULL,
				details longtext,
				created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
				user_id bigint(20) DEFAULT 0 NOT NULL,
				PRIMARY KEY (id)
			) $charset_collate;";
			
			dbDelta($sql);
		}
	}

	/**
	 * Log a sync event
	 * 
	 * @param string $type Log type (sync_start, sync_complete, attraction_created, etc.)
	 * @param string $message Human-readable message
	 * @param array $details Additional details to store as JSON
	 */
	public static function log($type, $message, $details = array())
	{
		// Ensure table exists before logging
		self::ensure_table_exists();
		
		global $wpdb;
		$table_name = self::get_table_name();

		$wpdb->insert(
			$table_name,
			array(
				'log_type' => $type,
				'message' => $message,
				'details' => json_encode($details),
				'created_at' => current_time('mysql'),
				'user_id' => get_current_user_id()
			),
			array('%s', '%s', '%s', '%s', '%d')
		);

		// Also log to PHP error log for debugging
		error_log("TRVLR [{$type}]: {$message}");
	}

	/**
	 * Get logs from the database
	 * 
	 * @param int $limit Number of logs to retrieve
	 * @param string|null $type Filter by log type
	 * @return array Array of log objects
	 */
	public static function get_logs($limit = 50, $type = null)
	{
		// Ensure table exists before querying
		self::ensure_table_exists();
		
		global $wpdb;
		$table_name = self::get_table_name();

		$sql = "SELECT * FROM {$table_name}";

		if ($type) {
			$sql .= $wpdb->prepare(" WHERE log_type = %s", $type);
		}

		$sql .= " ORDER BY created_at DESC LIMIT %d";

		return $wpdb->get_results($wpdb->prepare($sql, $limit));
	}

	/**
	 * Clear old logs
	 * 
	 * @param int $days Delete logs older than this many days
	 * @return int Number of deleted rows
	 */
	public static function clear_old_logs($days = 30)
	{
		global $wpdb;
		$table_name = self::get_table_name();

		$date_threshold = date('Y-m-d H:i:s', strtotime("-{$days} days"));

		return $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$table_name} WHERE created_at < %s",
				$date_threshold
			)
		);
	}

	/**
	 * Clear all logs
	 * 
	 * @return int Number of deleted rows
	 */
	public static function clear_all_logs()
	{
		global $wpdb;
		$table_name = self::get_table_name();

		return $wpdb->query("TRUNCATE TABLE {$table_name}");
	}

	/**
	 * Get log statistics
	 * 
	 * @return array Counts by log type
	 */
	public static function get_stats()
	{
		global $wpdb;
		$table_name = self::get_table_name();

		$results = $wpdb->get_results(
			"SELECT log_type, COUNT(*) as count FROM {$table_name} GROUP BY log_type"
		);

		$stats = array();
		foreach ($results as $row) {
			$stats[$row->log_type] = $row->count;
		}

		return $stats;
	}

	/**
	 * Schedule daily log cleanup
	 */
	public static function schedule_cleanup()
	{
		if (! wp_next_scheduled('trvlr_daily_log_cleanup')) {
			wp_schedule_event(time(), 'daily', 'trvlr_daily_log_cleanup');
		}
	}

	/**
	 * Unschedule log cleanup
	 */
	public static function unschedule_cleanup()
	{
		$timestamp = wp_next_scheduled('trvlr_daily_log_cleanup');
		if ($timestamp) {
			wp_unschedule_event($timestamp, 'trvlr_daily_log_cleanup');
		}
	}

	/**
	 * Daily cleanup callback
	 */
	public static function run_daily_cleanup()
	{
		$deleted = self::clear_old_logs(30);
		self::log('system', 'Auto-cleanup: Deleted ' . $deleted . ' old log entries');
	}

	/**
	 * Export logs as CSV
	 * 
	 * @param int    $limit     Number of logs to export (default: all)
	 * @param string $type      Filter by log type (optional)
	 * @param string $date_from Filter by start date (optional)
	 * @param string $date_to   Filter by end date (optional)
	 * @return string CSV content
	 */
	public static function export_to_csv($limit = null, $type = null, $date_from = null, $date_to = null)
	{
		global $wpdb;
		$table_name = self::get_table_name();

		// Build query
		$sql = "SELECT * FROM {$table_name} WHERE 1=1";
		$params = array();

		if ($type) {
			$sql .= " AND log_type = %s";
			$params[] = $type;
		}

		if ($date_from) {
			$sql .= " AND created_at >= %s";
			$params[] = $date_from . ' 00:00:00';
		}

		if ($date_to) {
			$sql .= " AND created_at <= %s";
			$params[] = $date_to . ' 23:59:59';
		}

		$sql .= " ORDER BY created_at DESC";

		if ($limit) {
			$sql .= " LIMIT %d";
			$params[] = $limit;
		}

		if (!empty($params)) {
			$logs = $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A);
		} else {
			$logs = $wpdb->get_results($sql, ARRAY_A);
		}

		// Generate CSV
		$csv = array();
		
		// Headers
		$csv[] = array('ID', 'Type', 'Message', 'Details', 'Created At', 'User ID');

		// Data rows
		foreach ($logs as $log) {
			$csv[] = array(
				$log['id'],
				$log['log_type'],
				$log['message'],
				$log['details'], // JSON string
				$log['created_at'],
				$log['user_id']
			);
		}

		// Convert to CSV string
		$output = '';
		foreach ($csv as $row) {
			$output .= implode(',', array_map(array(__CLASS__, 'escape_csv_value'), $row)) . "\n";
		}

		return $output;
	}

	/**
	 * Escape CSV value
	 * 
	 * @param string $value Value to escape
	 * @return string Escaped value
	 */
	private static function escape_csv_value($value)
	{
		if (strpos($value, ',') !== false || strpos($value, '"') !== false || strpos($value, "\n") !== false) {
			$value = '"' . str_replace('"', '""', $value) . '"';
		}
		return $value;
	}

	/**
	 * Generate CSV filename
	 * 
	 * @param string $type Optional log type
	 * @return string Filename
	 */
	public static function get_csv_filename($type = null)
	{
		$filename = 'trvlr-logs';
		if ($type) {
			$filename .= '-' . $type;
		}
		$filename .= '-' . date('Y-m-d-His');
		$filename .= '.csv';
		return $filename;
	}
}

