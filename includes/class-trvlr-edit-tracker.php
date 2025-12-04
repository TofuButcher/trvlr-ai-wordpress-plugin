<?php

/**
 * Track manual edits to attractions in real-time
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

class Trvlr_Edit_Tracker
{
	/**
	 * Track when an attraction is saved
	 */
	public static function track_attraction_save($post_id, $post, $update)
	{
		// Only track updates, not new posts
		if (!$update) {
			return;
		}

		// Only for attractions
		if ($post->post_type !== 'trvlr_attraction') {
			return;
		}

		// Ignore autosaves and revisions
		if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
			return;
		}

		// Get current edited fields
		$edited_fields = get_post_meta($post_id, '_trvlr_edited_fields', true);
		if (!is_array($edited_fields)) {
			$edited_fields = array();
		}

		// Only debug log for trvlr_id 5220
		$trvlr_id = get_post_meta($post_id, 'trvlr_id', true);
		$is_debug_post = ($trvlr_id == '5220');

		if ($is_debug_post) {
			error_log("===== TRVLR EDIT TRACKER START [{$post_id}] =====");
			error_log("Post: " . get_the_title($post_id));
		}

		// Check all trackable fields using centralized field map
		$trackable_fields = Trvlr_Field_Map::get_field_names();

		foreach ($trackable_fields as $field_name) {
			// Get synced hash
			$synced_hash = get_post_meta($post_id, "_trvlr_sync_hash_{$field_name}", true);

			// Skip if no synced hash exists (field never synced)
			if (!$synced_hash) {
				if ($is_debug_post) {
					error_log("  [{$field_name}] SKIP - No synced hash");
				}
				continue;
			}

			// Get current value
			$current_value = Trvlr_Field_Map::get_field_value($post_id, $field_name);

			// FULL DEBUG for rich text fields (only for debug post)
			if ($is_debug_post && in_array($field_name, ['trvlr_description', 'trvlr_short_description', 'trvlr_additional_info', 'trvlr_inclusions', 'trvlr_highlights'])) {
				error_log("TRVLR TRACKER [{$post_id}] {$field_name} - FULL RAW VALUE (ON SAVE):");
				error_log("  Length: " . strlen($current_value));
				error_log("  First 200 chars: " . substr($current_value, 0, 200));
				error_log("  Last 200 chars: " . substr($current_value, -200));
				error_log("  Serialized: " . substr(serialize($current_value), 0, 300));
			}

			// Generate hash using centralized method
			$current_hash = Trvlr_Field_Map::hash_field_value($current_value, $field_name);

			// Debug: Log every field comparison (only for debug post)
			if ($is_debug_post) {
				error_log("TRVLR TRACKER [{$post_id}] {$field_name}:");

				// Handle array values for logging
				$log_value = $current_value;
				if (is_array($log_value)) {
					$log_value = json_encode($log_value);
				} elseif (is_string($log_value) && strlen($log_value) > 50) {
					$log_value = substr($log_value, 0, 50) . '...';
				}

				error_log("  - Current Value: " . $log_value);
				error_log("  - Current Hash:  " . $current_hash);
				error_log("  - Synced Hash:   " . ($synced_hash ?: 'NONE'));
			}

			// Compare hashes
			if ($current_hash !== $synced_hash) {
				if ($is_debug_post) {
					error_log("  - RESULT: CHANGED! " . ($synced_hash ? "Hash mismatch" : "No synced hash"));
				}

				// Value changed - mark as edited
				if (!in_array($field_name, $edited_fields)) {
					$edited_fields[] = $field_name;
				}
			} else {
				if ($is_debug_post) {
					error_log("  - RESULT: MATCH");
				}

				// Value matches sync - remove from edited if present
				$edited_fields = array_diff($edited_fields, array($field_name));
			}
		}

		// Update meta
		if (!empty($edited_fields)) {
			if ($is_debug_post) {
				error_log("RESULT: " . count($edited_fields) . " fields marked as edited: " . implode(', ', $edited_fields));
			}
			update_post_meta($post_id, '_trvlr_edited_fields', array_values($edited_fields));
			update_post_meta($post_id, '_trvlr_has_custom_edits', '1');
		} else {
			if ($is_debug_post) {
				error_log("RESULT: No edited fields - clearing flags");
			}
			delete_post_meta($post_id, '_trvlr_edited_fields');
			delete_post_meta($post_id, '_trvlr_has_custom_edits');
		}

		if ($is_debug_post) {
			error_log("===== TRVLR EDIT TRACKER END [{$post_id}] =====");
		}
	}
}
