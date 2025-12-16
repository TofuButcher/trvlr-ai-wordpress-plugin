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
				'description' => 'Global color scheme for attractions',
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
					'textMutedColor' => array(
						'label' => 'Text Muted',
						'type' => 'color',
						'default' => 'hsl(0, 0%, 40%)',
						'cssVar' => '--trvlr-text-muted-color',
					),
					'headingColor' => array(
						'label' => 'Heading Color',
						'type' => 'color',
						'default' => 'hsl(0, 0%, 0%)',
						'cssVar' => '--trvlr-heading-color',
					),
				),
			),
			'typography' => array(
				'label' => 'Typography',
				'description' => 'Font and text styling',
				'fields' => array(
					'headingLetterSpacing' => array(
						'label' => 'Heading Letter Spacing',
						'type' => 'range',
						'default' => -0.04,
						'min' => -0.1,
						'max' => 0.1,
						'step' => 0.01,
						'unit' => 'em',
						'cssVar' => '--trvlr-heading-letter-spacing',
					),
				),
			),
			'attractionCards' => array(
				'label' => 'Attraction Cards',
				'description' => 'Styling for attraction card grid and individual cards',
				'fields' => array(
					'attractionGridGap' => array(
						'label' => 'Grid Column Gap',
						'type' => 'range',
						'default' => 40,
						'min' => 0,
						'max' => 100,
						'step' => 4,
						'unit' => 'px',
						'cssVar' => '--attraction-grid-gap',
					),
					'attractionGridRowGap' => array(
						'label' => 'Grid Row Gap',
						'type' => 'range',
						'default' => 80,
						'min' => 0,
						'max' => 200,
						'step' => 4,
						'unit' => 'px',
						'cssVar' => '--attraction-grid-row-gap',
					),
					'cardBackground' => array(
						'label' => 'Card Background',
						'type' => 'color',
						'default' => 'transparent',
						'cssVar' => '--attraction-card-background',
					),
					'cardPadding' => array(
						'label' => 'Card Padding',
						'type' => 'range',
						'default' => 4,
						'min' => 0,
						'max' => 40,
						'step' => 2,
						'unit' => 'px',
						'cssVar' => '--attraction-card-padding',
					),
					'cardBorderRadius' => array(
						'label' => 'Card Border Radius',
						'type' => 'range',
						'default' => 8,
						'min' => 0,
						'max' => 30,
						'step' => 2,
						'unit' => 'px',
						'cssVar' => '--attraction-card-border-radius',
					),
					'cardImageBorderRadius' => array(
						'label' => 'Image Border Radius',
						'type' => 'range',
						'default' => 8,
						'min' => 0,
						'max' => 30,
						'step' => 2,
						'unit' => 'px',
						'cssVar' => '--attraction-card-image-border-radius',
					),
				),
			),
			'badges' => array(
				'label' => 'Popular Badge',
				'description' => 'Styling for the "Popular" badge on cards',
				'fields' => array(
					'popularBadgeColor' => array(
						'label' => 'Badge Text Color',
						'type' => 'color',
						'default' => '#fff',
						'cssVar' => '--attraction-card-popular-badge-color',
					),
					'popularBadgeBackground' => array(
						'label' => 'Badge Background',
						'type' => 'color',
						'default' => '#000',
						'cssVar' => '--attraction-card-popular-badge-background',
					),
					'popularBadgeFontSize' => array(
						'label' => 'Badge Font Size',
						'type' => 'range',
						'default' => 16,
						'min' => 10,
						'max' => 24,
						'step' => 1,
						'unit' => 'px',
						'cssVar' => '--attraction-card-popular-badge-font-size',
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
