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
				sync_session_id varchar(50),
				created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
				user_id bigint(20) DEFAULT 0 NOT NULL,
				PRIMARY KEY (id),
				KEY sync_session_id (sync_session_id)
			) $charset_collate;";
			
			dbDelta($sql);
		} else {
			// Table exists, check if sync_session_id column exists
			$column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'sync_session_id'");
			if (empty($column_exists)) {
				// Add the column
				$wpdb->query("ALTER TABLE $table_name ADD COLUMN sync_session_id varchar(50) AFTER details, ADD KEY sync_session_id (sync_session_id)");
			}
		}
	}

	/**
	 * Log a sync event
	 * 
	 * @param string $type Log type (sync_start, sync_complete, attraction_created, etc.)
	 * @param string $message Human-readable message
	 * @param array $details Additional details to store as JSON
	 * @param string|null $session_id Optional session ID for grouping related logs
	 */
	public static function log($type, $message, $details = array(), $session_id = null)
	{
		// Ensure table exists before logging
		self::ensure_table_exists();
		
		global $wpdb;
		$table_name = self::get_table_name();

		// If no session ID provided, try to get current session from global
		if ($session_id === null) {
			$session_id = isset($GLOBALS['trvlr_current_sync_session']) ? $GLOBALS['trvlr_current_sync_session'] : null;
		}

		$wpdb->insert(
			$table_name,
			array(
				'log_type' => $type,
				'message' => $message,
				'details' => json_encode($details),
				'sync_session_id' => $session_id,
				'created_at' => current_time('mysql'),
				'user_id' => get_current_user_id()
			),
			array('%s', '%s', '%s', '%s', '%s', '%d')
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
	 * Get logs grouped by sync session
	 * 
	 * @param int $limit Number of sync sessions to retrieve
	 * @return array Array of sync sessions with their logs
	 */
	public static function get_grouped_logs($limit = 50)
	{
		// Ensure table exists before querying
		self::ensure_table_exists();
		
		global $wpdb;
		$table_name = self::get_table_name();

		// Get distinct sync sessions (ordered by most recent)
		$sessions = $wpdb->get_results($wpdb->prepare(
			"SELECT DISTINCT sync_session_id, MIN(created_at) as started_at, MAX(created_at) as completed_at
			FROM {$table_name}
			WHERE sync_session_id IS NOT NULL
			GROUP BY sync_session_id
			ORDER BY started_at DESC
			LIMIT %d",
			$limit
		));

		$grouped = array();

		foreach ($sessions as $session) {
			// Get all logs for this session
			$logs = $wpdb->get_results($wpdb->prepare(
				"SELECT * FROM {$table_name}
				WHERE sync_session_id = %s
				ORDER BY created_at ASC",
				$session->sync_session_id
			));

			// Calculate session summary
			$created_count = 0;
			$updated_count = 0;
			$skipped_count = 0;
			$error_count = 0;
			$status = 'completed';

			foreach ($logs as $log) {
				switch ($log->log_type) {
					case 'attraction_created':
						$created_count++;
						break;
					case 'attraction_updated':
						$updated_count++;
						break;
					case 'attraction_skipped':
					case 'no_updates':
						$skipped_count++;
						break;
					case 'error':
						$error_count++;
						$status = 'error';
						break;
				}
			}

			$grouped[] = array(
				'session_id' => $session->sync_session_id,
				'started_at' => $session->started_at,
				'completed_at' => $session->completed_at,
				'status' => $status,
				'summary' => array(
					'created' => $created_count,
					'updated' => $updated_count,
					'skipped' => $skipped_count,
					'errors' => $error_count,
					'total' => count($logs)
				),
				'logs' => $logs
			);
		}

		// Also get logs without a session ID (legacy or standalone logs)
		$standalone_logs = $wpdb->get_results($wpdb->prepare(
			"SELECT * FROM {$table_name}
			WHERE sync_session_id IS NULL
			ORDER BY created_at DESC
			LIMIT %d",
			$limit
		));

		if (!empty($standalone_logs)) {
			$grouped[] = array(
				'session_id' => null,
				'started_at' => null,
				'completed_at' => null,
				'status' => 'standalone',
				'summary' => array(
					'created' => 0,
					'updated' => 0,
					'skipped' => 0,
					'errors' => 0,
					'total' => count($standalone_logs)
				),
				'logs' => $standalone_logs
			);
		}

		return $grouped;
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

