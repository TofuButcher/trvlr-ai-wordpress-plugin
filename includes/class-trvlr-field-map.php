<?php

/**
 * Central field mapping and configuration for TRVLR attractions
 * Single source of truth for both sync and edit tracking
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

class Trvlr_Field_Map
{
	/**
	 * Get all trackable fields with their configurations
	 * 
	 * @return array Field configurations
	 */
	public static function get_trackable_fields()
	{
		return array(
			'post_title' => array(
				'label' => 'Title',
				'type' => 'string',
				'sync_from' => 'title',
			),
			'trvlr_description' => array(
				'label' => 'Description',
				'type' => 'string',
				'sync_from' => 'description',
			),
			'trvlr_short_description' => array(
				'label' => 'Short Description',
				'type' => 'string',
				'sync_from' => 'short_description',
			),
			'trvlr_inclusions' => array(
				'label' => 'Inclusions',
				'type' => 'string',
				'sync_from' => 'inclusions',
			),
			'trvlr_highlights' => array(
				'label' => 'Highlights',
				'type' => 'string',
				'sync_from' => 'highlights',
			),
			'trvlr_pricing' => array(
				'label' => 'Pricing',
				'type' => 'array',
				'sync_from' => 'pricing',
			),
			'trvlr_locations' => array(
				'label' => 'Locations',
				'type' => 'array',
				'sync_from' => null,
			),
			'trvlr_media' => array(
				'label' => 'Gallery Images',
				'type' => 'array',
				'sync_from' => null,
			),
			'trvlr_duration' => array(
				'label' => 'Duration',
				'type' => 'string',
				'sync_from' => 'duration',
			),
			'trvlr_start_time' => array(
				'label' => 'Start Time',
				'type' => 'string',
				'sync_from' => 'start_time',
			),
			'trvlr_end_time' => array(
				'label' => 'End Time',
				'type' => 'string',
				'sync_from' => 'end_time',
			),
			'trvlr_additional_info' => array(
				'label' => 'Additional Info',
				'type' => 'string',
				'sync_from' => 'additional_info',
			),
			'trvlr_is_on_sale' => array(
				'label' => 'Is On Sale',
				'type' => 'boolean',
				'sync_from' => null,
			),
			'trvlr_sale_description' => array(
				'label' => 'Sale Description',
				'type' => 'string',
				'sync_from' => null,
			),
			'_thumbnail_id' => array(
				'label' => 'Featured Image',
				'type' => 'string',
				'sync_from' => null,
			),
		);
	}

	/**
	 * Get human-readable label for a field
	 * 
	 * @param string $field_name Field name
	 * @return string Label
	 */
	public static function get_field_label($field_name)
	{
		$fields = self::get_trackable_fields();
		return isset($fields[$field_name]['label']) ? $fields[$field_name]['label'] : $field_name;
	}

	/**
	 * Get field type
	 * 
	 * @param string $field_name Field name
	 * @return string Type (string, array, boolean)
	 */
	public static function get_field_type($field_name)
	{
		$fields = self::get_trackable_fields();
		return isset($fields[$field_name]['type']) ? $fields[$field_name]['type'] : 'string';
	}

	/**
	 * Generate hash for a field value
	 * Handles different data types consistently
	 * 
	 * @param mixed $value Field value
	 * @param string $field_name Field name (for type detection)
	 * @return string MD5 hash
	 */
	public static function hash_field_value($value, $field_name)
	{
		$type = self::get_field_type($field_name);

		switch ($type) {
			case 'array':
				// Sort arrays to ensure consistent ordering
				if (is_array($value)) {
					// Deep sort for nested arrays
					$value = self::normalize_array($value);
					return md5(json_encode($value));
				}
				return md5('');

			case 'boolean':
				// Normalize boolean values
				return md5($value ? '1' : '0');

			case 'string':
			default:
				// Normalize line endings to \n (WordPress may save as \r\n)
				$value = is_string($value) ? trim($value) : (string) $value;
				$value = str_replace(array("\r\n", "\r"), "\n", $value);
				return md5($value);
		}
	}

	/**
	 * Normalize array for consistent hashing
	 * 
	 * @param array $array Array to normalize
	 * @return array Normalized array
	 */
	private static function normalize_array($array)
	{
		if (!is_array($array)) {
			return $array;
		}

		// Remove empty values
		$array = array_filter($array, function ($value) {
			if (is_array($value)) {
				return !empty($value);
			}
			return $value !== '' && $value !== null;
		});

		// Sort by keys for associative arrays
		if (self::is_assoc($array)) {
			ksort($array);
		}

		// Recursively normalize nested arrays and convert numeric strings to integers
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$array[$key] = self::normalize_array($value);
			} elseif (is_numeric($value)) {
				// Normalize numeric strings to integers (e.g., "31" -> 31)
				// This ensures consistency between sync (strings) and manual saves (integers)
				$array[$key] = intval($value);
			}
		}

		return $array;
	}

	/**
	 * Check if array is associative
	 * 
	 * @param array $array Array to check
	 * @return bool
	 */
	private static function is_assoc($array)
	{
		if (array() === $array) return false;
		return array_keys($array) !== range(0, count($array) - 1);
	}

	/**
	 * Get current value of a field from a post
	 * 
	 * @param int $post_id Post ID
	 * @param string $field_name Field name
	 * @return mixed Field value
	 */
	public static function get_field_value($post_id, $field_name)
	{
		if ($field_name === 'post_title') {
			return get_the_title($post_id);
		}

		if ($field_name === '_thumbnail_id') {
			return get_post_thumbnail_id($post_id);
		}

		return get_post_meta($post_id, $field_name, true);
	}

	/**
	 * Get all field names
	 * 
	 * @return array Field names
	 */
	public static function get_field_names()
	{
		return array_keys(self::get_trackable_fields());
	}

	/**
	 * Get all field labels indexed by field name
	 * 
	 * @return array ['field_name' => 'Label']
	 */
	public static function get_field_labels()
	{
		$fields = self::get_trackable_fields();
		$labels = array();
		foreach ($fields as $name => $config) {
			$labels[$name] = $config['label'];
		}
		return $labels;
	}
}
