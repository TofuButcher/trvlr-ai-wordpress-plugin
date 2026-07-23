<?php

/**
 * Unified attraction field registry for sync, Custom Edit, and meta UI.
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

class Trvlr_Field_Map
{
	/**
	 * @return array Field catalog keyed by field id
	 */
	public static function get_fields()
	{
		return array(
			'post_title' => array(
				'label' => 'Title',
				'data_type' => 'string',
				'sync' => array('source' => 'title'),
				'ui' => false,
			),
			'trvlr_description' => array(
				'label' => 'Description',
				'data_type' => 'string',
				'sync' => array('from' => 'description', 'transform' => 'editor'),
				'ui' => array('control' => 'richtext', 'span' => 6, 'rows' => 10),
			),
			'trvlr_short_description' => array(
				'label' => 'Short Description',
				'data_type' => 'string',
				'sync' => array('from' => 'short_description', 'transform' => 'editor'),
				'ui' => array('control' => 'richtext', 'span' => 6, 'rows' => 3, 'teeny' => true),
			),
			'trvlr_inclusions' => array(
				'label' => 'Inclusions',
				'data_type' => 'string',
				'sync' => array('from' => 'inclusions', 'transform' => 'list'),
				'ui' => array('control' => 'richtext', 'span' => 12, 'rows' => 5),
			),
			'trvlr_highlights' => array(
				'label' => 'Highlights',
				'data_type' => 'string',
				'sync' => array('from' => 'highlights', 'transform' => 'list'),
				'ui' => array('control' => 'richtext', 'span' => 12, 'rows' => 5),
			),
			'trvlr_is_on_sale' => array(
				'label' => 'Is On Sale',
				'data_type' => 'boolean',
				'sync' => false,
				'ui' => array('control' => 'checkbox', 'span' => 6),
			),
			'trvlr_sale_description' => array(
				'label' => 'Sale Description',
				'data_type' => 'string',
				'sync' => false,
				'ui' => array('control' => 'text', 'span' => 6),
			),
			'trvlr_pricing' => array(
				'label' => 'Attraction Pricing',
				'data_type' => 'array',
				'sync' => array('source' => 'pricing'),
				'ui' => array(
					'control' => 'repeater',
					'span' => 12,
					'repeater' => array(
						array('id' => 'type', 'label' => 'Price Type'),
						array('id' => 'price', 'label' => 'Price'),
						array('id' => 'sale_price', 'label' => 'Sale Price'),
					),
				),
			),
			'trvlr_locations' => array(
				'label' => 'Locations',
				'data_type' => 'array',
				'sync' => array('source' => 'locations'),
				'ui' => array(
					'control' => 'repeater',
					'span' => 12,
					'repeater' => array(
						array('id' => 'type', 'label' => 'Type (Start/End)'),
						array('id' => 'address', 'label' => 'Address'),
						array('id' => 'lat', 'label' => 'Latitude'),
						array('id' => 'lng', 'label' => 'Longitude'),
					),
				),
			),
			'trvlr_media' => array(
				'label' => 'Media Gallery',
				'data_type' => 'array',
				'sync' => array('source' => 'images'),
				'ui' => array('control' => 'gallery', 'span' => 12),
			),
			'trvlr_duration' => array(
				'label' => 'Duration',
				'data_type' => 'string',
				'sync' => array('from' => 'duration', 'transform' => 'text'),
				'ui' => array('control' => 'text', 'span' => 4),
			),
			'trvlr_start_time' => array(
				'label' => 'Start Time',
				'data_type' => 'string',
				'sync' => array('from' => 'start_time', 'transform' => 'text'),
				'ui' => array('control' => 'time', 'span' => 4),
			),
			'trvlr_end_time' => array(
				'label' => 'End Time',
				'data_type' => 'string',
				'sync' => array('from' => 'end_time', 'transform' => 'text'),
				'ui' => array('control' => 'time', 'span' => 4),
			),
			'trvlr_additional_info' => array(
				'label' => 'Additional Info',
				'data_type' => 'string',
				'sync' => array('from' => 'additional_info', 'transform' => 'editor'),
				'ui' => array('control' => 'richtext', 'span' => 12, 'rows' => 5),
			),
			'trvlr_simple_location' => array(
				'label' => 'Simple Location',
				'data_type' => 'string',
				'sync' => false,
				'ui' => array(
					'control' => 'text',
					'span' => 4,
					'description' => 'Short location name shown in the summary bar (e.g. "Cairns")',
				),
			),
			'trvlr_suitable_ages' => array(
				'label' => 'Suitable Ages',
				'data_type' => 'string',
				'sync' => false,
				'ui' => array(
					'control' => 'text',
					'span' => 4,
					'description' => 'Age suitability text shown in the summary bar (e.g. "All ages")',
				),
			),
			'trvlr_cancellation_policy' => array(
				'label' => 'Cancellation Policy',
				'data_type' => 'string',
				'sync' => false,
				'ui' => array(
					'control' => 'text',
					'span' => 4,
					'description' => 'Cancellation policy text shown in the summary bar (e.g. "Free cancellation")',
				),
			),
			'trvlr_faqs' => array(
				'label' => 'FAQs',
				'data_type' => 'array',
				'sync' => false,
				'ui' => array(
					'control' => 'repeater',
					'span' => 12,
					'repeater' => array(
						array('id' => 'question', 'label' => 'Question'),
						array('id' => 'answer', 'label' => 'Answer', 'type' => 'textarea'),
					),
				),
			),
			'trvlr_important_information' => array(
				'label' => 'Important Information',
				'data_type' => 'string',
				'sync' => false,
				'ui' => array('control' => 'text', 'span' => 12),
			),
			'_thumbnail_id' => array(
				'label' => 'Featured Image',
				'data_type' => 'string',
				'sync' => array('source' => 'thumbnail'),
				'ui' => false,
			),
		);
	}

	/**
	 * @deprecated Use get_fields()
	 */
	public static function get_trackable_fields()
	{
		return self::get_fields();
	}

	/**
	 * @param string $field_name
	 * @return array|null
	 */
	public static function get_field($field_name)
	{
		$fields = self::get_fields();
		return isset($fields[$field_name]) ? $fields[$field_name] : null;
	}

	/**
	 * @param string $field_name
	 * @return bool
	 */
	public static function is_syncable($field_name)
	{
		$field = self::get_field($field_name);
		return $field && !empty($field['sync']);
	}

	/**
	 * @return array
	 */
	public static function get_syncable_fields()
	{
		$out = array();
		foreach (self::get_fields() as $name => $config) {
			if (!empty($config['sync'])) {
				$out[$name] = $config;
			}
		}
		return $out;
	}

	/**
	 * @return string[]
	 */
	public static function get_syncable_field_names()
	{
		return array_keys(self::get_syncable_fields());
	}

	/**
	 * @return array
	 */
	public static function get_meta_ui_fields()
	{
		$out = array();
		foreach (self::get_fields() as $name => $config) {
			if (!empty($config['ui']) && is_array($config['ui'])) {
				$out[$name] = $config;
			}
		}
		return $out;
	}

	/**
	 * @param string $field_name
	 * @return string
	 */
	public static function get_field_label($field_name)
	{
		$field = self::get_field($field_name);
		return $field && isset($field['label']) ? $field['label'] : $field_name;
	}

	/**
	 * @param string $field_name
	 * @return string
	 */
	public static function get_field_type($field_name)
	{
		$field = self::get_field($field_name);
		if ($field && isset($field['data_type'])) {
			return $field['data_type'];
		}
		if ($field && isset($field['type'])) {
			return $field['type'];
		}
		return 'string';
	}

	/**
	 * @param mixed  $value
	 * @param string $field_name
	 * @return string MD5 hash
	 */
	public static function hash_field_value($value, $field_name)
	{
		$type = self::get_field_type($field_name);

		switch ($type) {
			case 'array':
				if ($value === null || $value === false || $value === '') {
					$value = array();
				}
				if (is_array($value)) {
					$value = self::normalize_array($value);
					return md5(json_encode($value));
				}
				return md5('');

			case 'boolean':
				return md5($value ? '1' : '0');

			case 'string':
			default:
				$value = is_string($value) ? trim($value) : (string) $value;
				$value = str_replace(array("\r\n", "\r"), "\n", $value);
				if ($field_name === 'post_title') {
					$value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
				}
				if (self::is_rich_text_meta_field($field_name)) {
					$value = self::normalize_editor_html_for_hash($value);
				}
				return md5($value);
		}
	}

	/**
	 * @param string $field_name
	 * @return bool
	 */
	public static function is_rich_text_meta_field($field_name)
	{
		$field = self::get_field($field_name);
		if (!$field || empty($field['ui']['control'])) {
			return false;
		}
		return $field['ui']['control'] === 'richtext';
	}

	private static function normalize_editor_html_for_hash($html)
	{
		if ($html === '') {
			return '';
		}
		$html = Trvlr_Data_Transform::normalize_text_for_editor_storage($html);
		$html = preg_replace('/\s+data-[a-zA-Z0-9_-]+="[^"]*"/', '', $html);
		$html = preg_replace("/\s+data-[a-zA-Z0-9_-]+='[^']*'/", '', $html);
		$html = preg_replace('#<p>\s*</p>#i', '', $html);
		$html = preg_replace('/>\s+</', '><', $html);
		foreach (array('li', 'p', 'ul', 'ol', 'div') as $tag) {
			$q = preg_quote($tag, '/');
			$html = preg_replace('/<' . $q . '([^>]*)>\s+/iu', '<' . $tag . '$1>', $html);
			$html = preg_replace('/\s+<\/' . $q . '>/iu', '</' . $tag . '>', $html);
		}
		$html = preg_replace('/\s+/u', ' ', $html);
		return trim($html);
	}

	private static function normalize_array($array)
	{
		if (!is_array($array)) {
			return $array;
		}

		$array = array_filter($array, function ($value) {
			if (is_array($value)) {
				return !empty($value);
			}
			return $value !== null;
		});

		if (self::is_assoc($array)) {
			ksort($array);
		}

		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$array[$key] = self::normalize_array($value);
			} elseif (($key === 'lat' || $key === 'lng') && $value !== '' && $value !== null && is_numeric($value)) {
				$array[$key] = number_format((float) $value, 6, '.', '');
			} elseif (is_numeric($value)) {
				$array[$key] = intval($value);
			}
		}

		return $array;
	}

	private static function is_assoc($array)
	{
		if (array() === $array) {
			return false;
		}
		return array_keys($array) !== range(0, count($array) - 1);
	}

	/**
	 * @param int    $post_id
	 * @param string $field_name
	 * @return mixed
	 */
	public static function get_field_value($post_id, $field_name)
	{
		if ($field_name === 'post_title') {
			return get_the_title($post_id);
		}

		if ($field_name === '_thumbnail_id') {
			return get_post_thumbnail_id($post_id);
		}

		$meta = get_post_meta($post_id, $field_name, true);
		if (self::get_field_type($field_name) === 'array' && !is_array($meta)) {
			return array();
		}
		return $meta;
	}

	/**
	 * @return string[]
	 */
	public static function get_field_names()
	{
		return array_keys(self::get_fields());
	}

	/**
	 * @return array<string,string>
	 */
	public static function get_field_labels()
	{
		$labels = array();
		foreach (self::get_fields() as $name => $config) {
			$labels[$name] = $config['label'];
		}
		return $labels;
	}

	/**
	 * @param array $data API attraction payload
	 * @return array meta_input entries from sync.from fields
	 */
	public static function build_sync_meta_from_api($data)
	{
		$meta = array();

		foreach (self::get_syncable_fields() as $field_name => $config) {
			$sync = $config['sync'];
			if (empty($sync['from'])) {
				continue;
			}

			$key = $sync['from'];
			$raw = isset($data[$key]) ? $data[$key] : null;
			$transform = isset($sync['transform']) ? $sync['transform'] : 'text';

			switch ($transform) {
				case 'editor':
					$meta[$field_name] = $raw !== null && $raw !== ''
						? Trvlr_Data_Transform::prepare_for_wp_editor($raw)
						: '';
					break;
				case 'list':
					$meta[$field_name] = !empty($raw)
						? Trvlr_Data_Transform::transform_list_field($raw)
						: '';
					break;
				case 'text':
				default:
					$meta[$field_name] = $raw !== null && $raw !== ''
						? sanitize_text_field($raw)
						: '';
					break;
			}
		}

		return $meta;
	}
}
