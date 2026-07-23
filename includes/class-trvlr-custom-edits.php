<?php

/**
 * Explicit per-field Custom Edit mode for attractions.
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

class Trvlr_Custom_Edits
{
	const MODEL_OPTION = 'trvlr_custom_edits_model';
	const MODEL_VERSION = 'explicit_v1';
	const NOTICE_OPTION = 'trvlr_custom_edits_notice';

	/**
	 * @param int $post_id
	 * @return string[]
	 */
	public static function get_fields($post_id)
	{
		$stored = get_post_meta($post_id, '_trvlr_edited_fields', true);
		if (!is_array($stored)) {
			return array();
		}

		$fields = array_values(array_unique(array_filter(
			array_map('strval', $stored),
			array('Trvlr_Field_Map', 'is_syncable')
		)));

		if ($fields !== array_values(array_map('strval', $stored))) {
			self::persist_fields($post_id, $fields);
		}

		return $fields;
	}

	/**
	 * @param int    $post_id
	 * @param string $field
	 * @return bool
	 */
	public static function is_custom_edit($post_id, $field)
	{
		return in_array((string) $field, self::get_fields($post_id), true);
	}

	/**
	 * @param int    $post_id
	 * @param string $field
	 * @param bool   $enabled
	 */
	public static function set_custom_edit($post_id, $field, $enabled)
	{
		$field = (string) $field;
		if (!Trvlr_Field_Map::is_syncable($field)) {
			return;
		}

		$fields = self::get_fields($post_id);

		if ($enabled) {
			if (!in_array($field, $fields, true)) {
				$fields[] = $field;
			}
		} else {
			$fields = array_values(array_diff($fields, array($field)));
		}

		self::persist_fields($post_id, $fields);
	}

	/**
	 * @param int   $post_id
	 * @param array $fields_to_clear Empty = clear all
	 */
	public static function clear_fields($post_id, $fields_to_clear = array())
	{
		if (empty($fields_to_clear)) {
			self::persist_fields($post_id, array());
			return;
		}

		$fields = array_values(array_diff(self::get_fields($post_id), array_map('strval', $fields_to_clear)));
		self::persist_fields($post_id, $fields);
	}

	/**
	 * @return int Number of posts cleared
	 */
	public static function clear_all_sitewide()
	{
		$posts = get_posts(array(
			'post_type' => 'trvlr_attraction',
			'meta_key' => '_trvlr_has_custom_edits',
			'meta_value' => '1',
			'fields' => 'ids',
			'posts_per_page' => -1,
			'post_status' => 'any',
		));

		foreach ($posts as $post_id) {
			self::persist_fields($post_id, array());
		}

		return count($posts);
	}

	/**
	 * @param int   $post_id
	 * @param array $fields
	 */
	private static function persist_fields($post_id, $fields)
	{
		$fields = array_values(array_unique(array_map('strval', $fields)));

		if (empty($fields)) {
			delete_post_meta($post_id, '_trvlr_edited_fields');
			delete_post_meta($post_id, '_trvlr_has_custom_edits');
			return;
		}

		update_post_meta($post_id, '_trvlr_edited_fields', $fields);
		update_post_meta($post_id, '_trvlr_has_custom_edits', '1');
	}

	/**
	 * One-time migration from the hash/force-sync model.
	 */
	public static function maybe_migrate()
	{
		if (get_option(self::MODEL_OPTION) === self::MODEL_VERSION) {
			return;
		}

		global $wpdb;

		$posts = get_posts(array(
			'post_type' => 'trvlr_attraction',
			'posts_per_page' => -1,
			'fields' => 'ids',
			'post_status' => 'any',
		));

		foreach ($posts as $post_id) {
			$edited = self::get_fields($post_id);
			if (!empty($edited)) {
				update_post_meta($post_id, '_trvlr_has_custom_edits', '1');
			} else {
				delete_post_meta($post_id, '_trvlr_has_custom_edits');
			}
			delete_post_meta($post_id, '_trvlr_force_sync_fields');
		}

		$wpdb->query(
			"DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_trvlr_sync_hash_%'"
		);
		$wpdb->query(
			"DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_trvlr_force_sync_fields'"
		);

		update_option(self::MODEL_OPTION, self::MODEL_VERSION);
		update_option(self::NOTICE_OPTION, '1');
	}

	/**
	 * Show a dismissible notice after the Custom Edit model migration.
	 */
	public static function maybe_admin_notice()
	{
		if (!is_admin() || !current_user_can('manage_options')) {
			return;
		}
		if (get_option(self::NOTICE_OPTION) !== '1') {
			return;
		}
		if (isset($_GET['trvlr_dismiss_custom_edits_notice'])) {
			check_admin_referer('trvlr_dismiss_custom_edits_notice');
			delete_option(self::NOTICE_OPTION);
			return;
		}

		$dismiss_url = wp_nonce_url(
			add_query_arg('trvlr_dismiss_custom_edits_notice', '1'),
			'trvlr_dismiss_custom_edits_notice'
		);

		echo '<div class="notice notice-info is-dismissible"><p>';
		echo esc_html__(
			'Custom edits are now deliberate. Fields previously protected still are; use Enable Traveloris Sync on a field or the Sync settings tools to restore API control.',
			'trvlr'
		);
		echo ' <a href="' . esc_url($dismiss_url) . '">' . esc_html__('Dismiss', 'trvlr') . '</a>';
		echo '</p></div>';
	}

	/**
	 * Block featured-image meta changes while the field is Synced.
	 */
	public static function filter_thumbnail_meta($check, $object_id, $meta_key)
	{
		if ($meta_key !== '_thumbnail_id') {
			return $check;
		}
		if (get_post_type($object_id) !== 'trvlr_attraction') {
			return $check;
		}
		if (defined('TRVLR_SYNCING') && TRVLR_SYNCING) {
			return $check;
		}
		if (!get_post_meta($object_id, 'trvlr_id', true)) {
			return $check;
		}
		if (self::is_custom_edit($object_id, '_thumbnail_id')) {
			return $check;
		}
		return true;
	}

	/**
	 * Preserve the existing title when post_title is still Synced.
	 */
	public static function filter_insert_post_data($data, $postarr)
	{
		if (empty($postarr['ID']) || empty($data['post_type']) || $data['post_type'] !== 'trvlr_attraction') {
			return $data;
		}
		if (defined('TRVLR_SYNCING') && TRVLR_SYNCING) {
			return $data;
		}
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $data;
		}

		$post_id = (int) $postarr['ID'];
		if (!get_post_meta($post_id, 'trvlr_id', true)) {
			return $data;
		}
		if (!self::is_custom_edit($post_id, 'post_title')) {
			$existing = get_post($post_id);
			if ($existing) {
				$data['post_title'] = $existing->post_title;
			}
		}

		return $data;
	}
}

/**
 * @param int $post_id
 * @return string[]
 */
function trvlr_get_custom_edit_fields($post_id)
{
	return Trvlr_Custom_Edits::get_fields($post_id);
}

/**
 * @param int    $post_id
 * @param string $field
 * @return bool
 */
function trvlr_is_custom_edit($post_id, $field)
{
	return Trvlr_Custom_Edits::is_custom_edit($post_id, $field);
}

/**
 * @param int    $post_id
 * @param string $field
 * @param bool   $enabled
 */
function trvlr_set_custom_edit($post_id, $field, $enabled)
{
	Trvlr_Custom_Edits::set_custom_edit($post_id, $field, $enabled);
}
