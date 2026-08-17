<?php

/**
 * Theme settings config (PHP). See docs/reference/theme-config.md for the React side.
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

class Trvlr_Theme_Config
{
	/**
	 * Get all theme configuration
	 * @return array
	 *
	 * [ 'Top Level Group' => [
	 * 	'label' => 'Label',
	 * 	'description' => 'Description',
	 * 	'fields' => [
	 * 		'Key' => [
	 * 			'label' => 'Label',
	 * 			'type' => 'Type',
	 * 			'default' => 'Default',
	 * 			'cssVar' => 'CSS Var',
	 * 		]
	 * 	]
	 * 	'cols-2' => [
	 * 		'label' => 'Optional Label',
	 * 		'description' => 'Optional Description',
	 * 		'fields' => [...],
	 * 		]
	 * ] ]
	 * --- cols-X keys can be used to wrap fields within with eg. div.trvlr-cols-2.
	 * --- Available keys are 'cols-2', 'cols-3', 'cols-4'.
	 */
	public static function get_config()
	{
		return array(
			'colors' => array(
				'label' => 'Colors',
				'description' => 'Core colors for Traveloris components. Hover, foreground, and shade scales are derived automatically.',
				'cols-4' => array(
					'fields' => array(
						'primaryColor' => array(
							'label' => 'Primary',
							'type' => 'color',
							'default' => 'hsl(165, 100%, 39%)',
							'cssVar' => '--trvlr-color-primary',
						),
						'secondaryColor' => array(
							'label' => 'Secondary',
							'type' => 'color',
							'default' => 'hsl(329, 66%, 75%)',
							'cssVar' => '--trvlr-color-secondary',
						),
						'accentColor' => array(
							'label' => 'Accent',
							'type' => 'color',
							'default' => 'hsl(157, 100%, 49%)',
							'cssVar' => '--trvlr-color-accent',
						),
						'alertColor' => array(
							'label' => 'Alert',
							'type' => 'color',
							'default' => 'hsl(0, 90%, 65%)',
							'cssVar' => '--trvlr-color-alert',
						),
					),
				),
			),
		);
	}

	/**
	 * @return array
	 */
	public static function get_defaults()
	{
		$config = self::get_config();
		$defaults = array();

		foreach ($config as $group) {
			$fields = self::extract_fields_from_group($group);
			foreach ($fields as $key => $field) {
				$defaults[$key] = $field['default'];
			}
		}

		return $defaults;
	}

	/**
	 * @param array|mixed $user_settings
	 * @return array
	 */
	public static function merge_with_defaults($user_settings)
	{
		$user_settings = self::migrate_legacy_setting_keys(
			is_array($user_settings) ? $user_settings : array()
		);

		return array_merge(
			self::get_defaults(),
			$user_settings
		);
	}

	/**
	 * Map retired setting keys onto current ones.
	 *
	 * @param array $settings
	 * @return array
	 */
	public static function migrate_legacy_setting_keys($settings)
	{
		if (!is_array($settings)) {
			return array();
		}

		if (isset($settings['importantColor']) && !isset($settings['alertColor'])) {
			$settings['alertColor'] = $settings['importantColor'];
		}
		unset($settings['importantColor']);

		return $settings;
	}

	/**
	 * Field definitions keyed by setting key.
	 *
	 * @return array<string, array>
	 */
	public static function get_fields_map()
	{
		$map = array();
		foreach (self::get_all_fields() as $field) {
			if (!isset($field['key'])) {
				continue;
			}
			$map[$field['key']] = $field;
		}
		return $map;
	}

	/**
	 * Keep only known theme keys whose values differ from defaults.
	 *
	 * @param array $settings
	 * @return array
	 */
	public static function filter_to_stored_settings($settings)
	{
		if (!is_array($settings)) {
			return array();
		}

		$settings = self::migrate_legacy_setting_keys($settings);
		$defaults = self::get_defaults();
		$stored = array();

		foreach ($defaults as $key => $default) {
			if (!array_key_exists($key, $settings)) {
				continue;
			}
			$value = $settings[$key];
			if (self::values_equal($value, $default)) {
				continue;
			}
			$stored[$key] = $value;
		}

		return $stored;
	}

	/**
	 * Persist theme option keys sparsely (non-defaults only) and optional presentation theme.
	 *
	 * @param array $settings
	 * @return array Merged settings for API responses
	 */
	public static function save_settings($settings)
	{
		if (!is_array($settings)) {
			$settings = array();
		}

		if (class_exists('Trvlr_Template_Registry') && array_key_exists('presentationTheme', $settings)) {
			$pt = sanitize_key((string) $settings['presentationTheme']);
			if ($pt !== '') {
				Trvlr_Template_Registry::set_active_presentation_theme($pt);
			}
		}

		$theme_only = self::filter_to_stored_settings($settings);
		update_option('trvlr_theme_settings', $theme_only);

		return self::get_merged_settings_for_response();
	}

	/**
	 * @return array
	 */
	public static function get_merged_settings_for_response()
	{
		$stored = get_option('trvlr_theme_settings', array());
		$merged = self::merge_with_defaults(is_array($stored) ? $stored : array());

		if (class_exists('Trvlr_Template_Registry')) {
			$merged['presentationTheme'] = Trvlr_Template_Registry::get_active_presentation_theme_slug();
			$merged['cardTemplate'] = Trvlr_Template_Registry::get_active_card_slug();
			$merged['attractionPageTemplate'] = Trvlr_Template_Registry::get_active_single_slug();
		}

		return $merged;
	}

	/**
	 * Export payload: stored custom values + presentation theme.
	 *
	 * @return array
	 */
	public static function build_export_payload()
	{
		$stored = get_option('trvlr_theme_settings', array());
		$settings = self::filter_to_stored_settings(is_array($stored) ? $stored : array());

		if (class_exists('Trvlr_Template_Registry')) {
			$settings['presentationTheme'] = Trvlr_Template_Registry::get_active_presentation_theme_slug();
		}

		return array(
			'type' => 'trvlr-theme-settings',
			'version' => 1,
			'pluginVersion' => defined('TRVLR_VERSION') ? TRVLR_VERSION : '',
			'exportedAt' => gmdate('c'),
			'settings' => $settings,
		);
	}

	/**
	 * Normalize import JSON into a flat settings map.
	 *
	 * @param mixed $payload
	 * @return array|WP_Error
	 */
	public static function normalize_import_payload($payload)
	{
		if (!is_array($payload)) {
			return new WP_Error('invalid_import', __('Import file must be a JSON object.', 'trvlr'), array('status' => 400));
		}

		if (isset($payload['settings']) && is_array($payload['settings'])) {
			if (isset($payload['type']) && $payload['type'] !== 'trvlr-theme-settings') {
				return new WP_Error('invalid_import', __('This file is not a Traveloris theme settings export.', 'trvlr'), array('status' => 400));
			}
			return $payload['settings'];
		}

		$reserved = array('type', 'version', 'pluginVersion', 'exportedAt');
		$settings = $payload;
		foreach ($reserved as $key) {
			unset($settings[$key]);
		}

		if (!is_array($settings) || empty($settings)) {
			return new WP_Error('invalid_import', __('No settings found in import file.', 'trvlr'), array('status' => 400));
		}

		return $settings;
	}

	/**
	 * Validate import settings against theme config field types.
	 *
	 * @param array $settings
	 * @return array{valid: array, invalid: array<int, array{key: string, reason: string}>}
	 */
	public static function validate_import_settings($settings)
	{
		$fields = self::get_fields_map();
		$valid = array();
		$invalid = array();

		if (!is_array($settings)) {
			return array('valid' => $valid, 'invalid' => array(
				array('key' => '(root)', 'reason' => __('Settings must be an object.', 'trvlr')),
			));
		}

		$settings = self::migrate_legacy_setting_keys($settings);

		foreach ($settings as $key => $value) {
			$key = (string) $key;

			if ($key === 'presentationTheme') {
				$slug = sanitize_key(is_scalar($value) ? (string) $value : '');
				if (
					$slug !== ''
					&& class_exists('Trvlr_Template_Registry')
					&& isset(Trvlr_Template_Registry::get_presentation_themes()[$slug])
				) {
					$valid[$key] = $slug;
				} else {
					$invalid[] = array(
						'key' => $key,
						'reason' => __('Unknown or invalid presentation theme.', 'trvlr'),
					);
				}
				continue;
			}

			if ($key === 'cardTemplate' || $key === 'attractionPageTemplate') {
				continue;
			}

			if (!isset($fields[$key])) {
				$invalid[] = array(
					'key' => $key,
					'reason' => __('Unknown setting (not in current theme config).', 'trvlr'),
				);
				continue;
			}

			$checked = self::validate_field_value($fields[$key], $value);
			if (is_wp_error($checked)) {
				$invalid[] = array(
					'key' => $key,
					'reason' => $checked->get_error_message(),
				);
				continue;
			}

			$valid[$key] = $checked;
		}

		return array(
			'valid' => $valid,
			'invalid' => $invalid,
		);
	}

	/**
	 * Replace stored theme settings with validated import values.
	 * Keys not present in $valid are reset to defaults (omitted from option).
	 * If presentationTheme is omitted, resets to the default presentation theme.
	 *
	 * @param array $valid
	 * @return array
	 */
	public static function apply_import($valid)
	{
		if (!is_array($valid)) {
			$valid = array();
		}

		$theme_values = $valid;
		unset($theme_values['presentationTheme']);
		$theme_only = self::filter_to_stored_settings($theme_values);
		update_option('trvlr_theme_settings', $theme_only);

		if (class_exists('Trvlr_Template_Registry')) {
			if (isset($valid['presentationTheme'])) {
				Trvlr_Template_Registry::set_active_presentation_theme($valid['presentationTheme']);
			} else {
				Trvlr_Template_Registry::set_active_presentation_theme(
					Trvlr_Template_Registry::get_default_presentation_theme_slug()
				);
			}
		}

		return self::get_merged_settings_for_response();
	}

	/**
	 * @param array $field
	 * @param mixed $value
	 * @return mixed|WP_Error
	 */
	private static function validate_field_value($field, $value)
	{
		$type = isset($field['type']) ? $field['type'] : 'text';

		switch ($type) {
			case 'color':
				if (!is_string($value)) {
					return new WP_Error('invalid_type', __('Expected a color string.', 'trvlr'));
				}
				$value = trim($value);
				if ($value === '') {
					return new WP_Error('invalid_type', __('Color value cannot be empty.', 'trvlr'));
				}
				if (!self::is_plausible_css_color($value)) {
					return new WP_Error('invalid_type', __('Value does not look like a valid CSS color.', 'trvlr'));
				}
				return $value;

			case 'range':
				if (is_string($value) && is_numeric($value)) {
					$value = strpos($value, '.') !== false ? (float) $value : (int) $value;
				}
				if (!is_int($value) && !is_float($value)) {
					return new WP_Error('invalid_type', __('Expected a number.', 'trvlr'));
				}
				if (isset($field['min']) && $value < $field['min']) {
					return new WP_Error('invalid_type', sprintf(__('Value is below minimum (%s).', 'trvlr'), $field['min']));
				}
				if (isset($field['max']) && $value > $field['max']) {
					return new WP_Error('invalid_type', sprintf(__('Value is above maximum (%s).', 'trvlr'), $field['max']));
				}
				return $value;

			case 'select':
				if (!is_scalar($value)) {
					return new WP_Error('invalid_type', __('Expected a select option value.', 'trvlr'));
				}
				if (!empty($field['options']) && is_array($field['options'])) {
					$allowed = array();
					foreach ($field['options'] as $option) {
						if (is_array($option) && array_key_exists('value', $option)) {
							$allowed[] = $option['value'];
						} else {
							$allowed[] = $option;
						}
					}
					if (!in_array($value, $allowed, false)) {
						return new WP_Error('invalid_type', __('Value is not a valid option.', 'trvlr'));
					}
				}
				return $value;

			case 'text':
			default:
				if (!is_string($value) && !is_numeric($value)) {
					return new WP_Error('invalid_type', __('Expected a string.', 'trvlr'));
				}
				return is_string($value) ? $value : (string) $value;
		}
	}

	/**
	 * @param mixed $value
	 * @return bool
	 */
	private static function is_plausible_css_color($value)
	{
		$value = trim((string) $value);
		if ($value === '') {
			return false;
		}
		if (stripos($value, 'var(') === 0) {
			return true;
		}
		if ($value[0] === '#') {
			return (bool) preg_match('/^#([0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $value);
		}
		if (preg_match('/^(rgb|rgba|hsl|hsla|oklch|oklab|color)\(/i', $value)) {
			return true;
		}
		return (bool) preg_match('/^[a-zA-Z]+$/', $value);
	}

	/**
	 * @param mixed $a
	 * @param mixed $b
	 * @return bool
	 */
	private static function values_equal($a, $b)
	{
		if (is_numeric($a) && is_numeric($b)) {
			return (float) $a === (float) $b;
		}
		return $a === $b;
	}

	/**
	 * @param array $settings
	 * @return string
	 */
	public static function generate_css_variables($settings)
	{
		$config = self::get_config();
		$css = '';

		foreach ($config as $group) {
			$fields = self::extract_fields_from_group($group);
			foreach ($fields as $key => $field) {
				if (!isset($field['cssVar'])) {
					continue;
				}

				$value = isset($settings[$key]) ? $settings[$key] : $field['default'];
				if (!is_string($value) && !is_numeric($value)) {
					continue;
				}

				$value = trim((string) $value);
				if ($value === '') {
					continue;
				}

				$unit = isset($field['unit']) ? $field['unit'] : '';
				$css .= $field['cssVar'] . ': ' . $value . $unit . '; ';
			}
		}

		if (class_exists('Trvlr_Icons')) {
			$css .= Trvlr_Icons::css_mask_variables(array('check-circle'));
		}

		return $css;
	}

	/**
	 * @return array
	 */
	public static function get_all_fields()
	{
		$config = self::get_config();
		$fields = array();

		foreach ($config as $group) {
			$group_fields = self::extract_fields_from_group($group);
			foreach ($group_fields as $key => $field) {
				$field['key'] = $key;
				$fields[] = $field;
			}
		}

		return $fields;
	}

	/**
	 * Flatten fields from a group, including cols-X wrappers.
	 *
	 * @param array $group
	 * @return array
	 */
	private static function extract_fields_from_group($group)
	{
		$all_fields = array();

		if (isset($group['fields'])) {
			$all_fields = array_merge($all_fields, $group['fields']);
		}

		foreach ($group as $key => $value) {
			if (strpos($key, 'cols-') === 0 && isset($value['fields'])) {
				$all_fields = array_merge($all_fields, $value['fields']);
			}
		}

		return $all_fields;
	}
}
