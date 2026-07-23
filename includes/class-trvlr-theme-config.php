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
				'description' => 'Global color scheme for TRVLR components',
				'cols-3' => array(
					'fields' => array(
						'primaryColor' => array(
							'label' => 'Primary Color',
							'type' => 'color',
							'default' => 'hsl(245, 90%, 50%)',
							'cssVar' => '--trvlr-primary-color',
						),
						'primaryActiveColor' => array(
							'label' => 'Primary Hover Color',
							'type' => 'color',
							'default' => 'hsl(245, 100%, 40%)',
							'cssVar' => '--trvlr-primary-active-color',
						),
						'accentColor' => array(
							'label' => 'Accent Color',
							'type' => 'color',
							'default' => 'hsl(57, 100%, 50%)',
							'cssVar' => '--trvlr-accent-color',
						),
						'headingColor' => array(
							'label' => 'Heading Color',
							'type' => 'color',
							'default' => 'hsl(0, 0%, 0%)',
							'cssVar' => '--trvlr-heading-color',
						),
						'textMutedColor' => array(
							'label' => 'Text Muted',
							'type' => 'color',
							'default' => 'hsl(0, 0%, 40%)',
							'cssVar' => '--trvlr-text-muted-color',
						),
					),
				),
			),
			'attractionCards' => array(
				'label' => 'Attraction Cards',
				'description' => 'Styling for attraction cards',
				'fields' => array(
					'cardBackground' => array(
						'label' => 'Card Background',
						'type' => 'color',
						'default' => 'transparent',
						'cssVar' => '--trvlr-card-background',
					),
					'cardPadding' => array(
						'label' => 'Card Padding',
						'type' => 'range',
						'default' => 4,
						'min' => 0,
						'max' => 40,
						'step' => 2,
						'unit' => 'px',
						'cssVar' => '--trvlr-card-padding',
					),
					'cardBorderRadius' => array(
						'label' => 'Card Border Radius',
						'type' => 'range',
						'default' => 8,
						'min' => 0,
						'max' => 30,
						'step' => 2,
						'unit' => 'px',
						'cssVar' => '--trvlr-card-border-radius',
					),
					'cardImageBorderRadius' => array(
						'label' => 'Image Border Radius',
						'type' => 'range',
						'default' => 8,
						'min' => 0,
						'max' => 30,
						'step' => 2,
						'unit' => 'px',
						'cssVar' => '--trvlr-card-image-border-radius',
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
		return array_merge(
			self::get_defaults(),
			is_array($user_settings) ? $user_settings : array()
		);
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
				$value = isset($settings[$key]) ? $settings[$key] : $field['default'];
				$unit = isset($field['unit']) ? $field['unit'] : '';
				$css .= $field['cssVar'] . ': ' . $value . $unit . '; ';
			}
		}

		$defaults = self::get_defaults();

		if (isset($settings['primaryColor']) || isset($defaults['primaryColor'])) {
			$primary_color = isset($settings['primaryColor']) ? $settings['primaryColor'] : $defaults['primaryColor'];
			if (self::is_usable_color($primary_color)) {
				$css .= '--trvlr-text-on-primary-color: ' . self::get_text_on_color($primary_color) . '; ';
			}
		}

		if (isset($settings['accentColor']) || isset($defaults['accentColor'])) {
			$accent_color = isset($settings['accentColor']) ? $settings['accentColor'] : $defaults['accentColor'];
			if (self::is_usable_color($accent_color)) {
				$css .= '--trvlr-text-on-accent-color: ' . self::get_text_on_color($accent_color) . '; ';
			}
		}

		return $css;
	}

	/**
	 * Pick black or white text for best contrast on a background color.
	 *
	 * @param string $color CSS color value (hsl, rgb, hex).
	 * @return string hsl(0, 0%, 100%) or hsl(0, 0%, 0%)
	 */
	public static function get_text_on_color($color)
	{
		error_log('get_text_on_color: ' . $color);
		$rgb = self::parse_color_to_rgb($color);
		error_log('rgb: ' . print_r($rgb, true));
		if ($rgb === null) {
			return 'hsl(0, 0%, 0%)';
		}

		$background_luminance = self::relative_luminance($rgb[0], $rgb[1], $rgb[2]);
		$white_contrast = self::contrast_ratio($background_luminance, 1);
		$black_contrast = self::contrast_ratio($background_luminance, 0);
		error_log('background_luminance: ' . $background_luminance);
		error_log('white_contrast: ' . $white_contrast);
		error_log('black_contrast: ' . $black_contrast);

		return $white_contrast >= $black_contrast ? 'hsl(0, 0%, 100%)' : 'hsl(0, 0%, 0%)';
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

	private static function is_usable_color($color)
	{
		if (!is_string($color)) {
			return false;
		}

		$color = trim(strtolower($color));

		return $color !== '' && $color !== 'transparent';
	}

	private static function parse_color_to_rgb($color)
	{
		if (!self::is_usable_color($color)) {
			return null;
		}

		$color = trim(strtolower($color));

		if ($color[0] === '#') {
			return self::hex_to_rgb($color);
		}

		if (preg_match('#^hsla?\(\s*([-\d.]+)(?:deg)?\s*[,/\s]\s*([-\d.]+%?)\s*[,/\s]\s*([-\d.]+%?)(?:\s*[,/\s]\s*([-\d.]+%?))?\s*\)$#i', $color, $matches)) {
			$h = (float) $matches[1];
			$s = self::normalize_percentage($matches[2]);
			$l = self::normalize_percentage($matches[3]);

			return self::hsl_to_rgb($h, $s, $l);
		}

		if (preg_match('#^rgba?\(\s*([-\d.]+%?)\s*[,/\s]\s*([-\d.]+%?)\s*[,/\s]\s*([-\d.]+%?)(?:\s*[,/\s]\s*([-\d.]+%?))?\s*\)$#i', $color, $matches)) {
			return array(
				self::normalize_rgb_channel($matches[1]),
				self::normalize_rgb_channel($matches[2]),
				self::normalize_rgb_channel($matches[3]),
			);
		}

		return null;
	}

	private static function hex_to_rgb($hex)
	{
		$hex = ltrim($hex, '#');

		if (strlen($hex) === 3) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		if (strlen($hex) !== 6 || !ctype_xdigit($hex)) {
			return null;
		}

		return array(
			hexdec(substr($hex, 0, 2)),
			hexdec(substr($hex, 2, 2)),
			hexdec(substr($hex, 4, 2)),
		);
	}

	private static function normalize_percentage($value)
	{
		$value = trim((string) $value);

		if (substr($value, -1) === '%') {
			return (float) substr($value, 0, -1);
		}

		return (float) $value;
	}

	private static function normalize_rgb_channel($value)
	{
		$value = trim((string) $value);

		if (substr($value, -1) === '%') {
			return (int) round((float) substr($value, 0, -1) * 255 / 100);
		}

		return (int) round((float) $value);
	}

	private static function hsl_to_rgb($h, $s, $l)
	{
		$h = fmod((float) $h, 360);
		if ($h < 0) {
			$h += 360;
		}

		$s = max(0, min(100, (float) $s)) / 100;
		$l = max(0, min(100, (float) $l)) / 100;

		$c = (1 - abs(2 * $l - 1)) * $s;
		$x = $c * (1 - abs(fmod($h / 60, 2) - 1));
		$m = $l - $c / 2;

		if ($h < 60) {
			$r = $c;
			$g = $x;
			$b = 0;
		} elseif ($h < 120) {
			$r = $x;
			$g = $c;
			$b = 0;
		} elseif ($h < 180) {
			$r = 0;
			$g = $c;
			$b = $x;
		} elseif ($h < 240) {
			$r = 0;
			$g = $x;
			$b = $c;
		} elseif ($h < 300) {
			$r = $x;
			$g = 0;
			$b = $c;
		} else {
			$r = $c;
			$g = 0;
			$b = $x;
		}

		return array(
			(int) round(($r + $m) * 255),
			(int) round(($g + $m) * 255),
			(int) round(($b + $m) * 255),
		);
	}

	private static function relative_luminance($red, $green, $blue)
	{
		$channels = array($red, $green, $blue);
		$linear = array();

		foreach ($channels as $channel) {
			$value = max(0, min(255, (int) $channel)) / 255;
			$linear[] = $value <= 0.03928 ? $value / 12.92 : pow(($value + 0.055) / 1.055, 2.4);
		}

		return 0.2126 * $linear[0] + 0.7152 * $linear[1] + 0.0722 * $linear[2];
	}

	private static function contrast_ratio($luminance_a, $luminance_b)
	{
		$lighter = max($luminance_a, $luminance_b);
		$darker = min($luminance_a, $luminance_b);

		return ($lighter + 0.05) / ($darker + 0.05);
	}
}
