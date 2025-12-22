<?php

/**
 * Theme Configuration
 * 
 * Single source of truth for theme settings (PHP version).
 * Mirrors the TypeScript config in admin/src/config/themeConfig.ts
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

class Trvlr_Theme_Config
{
	/**
	 * Get all theme configuration
	 */
	public static function get_config()
	{
		return array(
			'colors' => array(
				'label' => 'Colors',
				'description' => 'Global color scheme for TRVLR components',
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
	 * Get default values for all theme settings
	 */
	public static function get_defaults()
	{
		$config = self::get_config();
		$defaults = array();

		foreach ($config as $group) {
			foreach ($group['fields'] as $key => $field) {
				$defaults[$key] = $field['default'];
			}
		}

		return $defaults;
	}

	/**
	 * Merge user settings with defaults
	 */
	public static function merge_with_defaults($user_settings)
	{
		return array_merge(
			self::get_defaults(),
			is_array($user_settings) ? $user_settings : array()
		);
	}

	/**
	 * Generate CSS variables string from settings
	 */
	public static function generate_css_variables($settings)
	{
		$config = self::get_config();
		$css = '';

		foreach ($config as $group) {
			foreach ($group['fields'] as $key => $field) {
				$value = isset($settings[$key]) ? $settings[$key] : $field['default'];
				$unit = isset($field['unit']) ? $field['unit'] : '';
				$css .= $field['cssVar'] . ': ' . $value . $unit . '; ';
			}
		}

		return $css;
	}

	/**
	 * Get all fields as flat array
	 */
	public static function get_all_fields()
	{
		$config = self::get_config();
		$fields = array();

		foreach ($config as $group) {
			foreach ($group['fields'] as $key => $field) {
				$field['key'] = $key;
				$fields[] = $field;
			}
		}

		return $fields;
	}
}
