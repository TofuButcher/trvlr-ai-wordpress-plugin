<?php

/**
 * Static data transformation utilities for TRVLR attraction fields
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

class Trvlr_Data_Transform
{
	public static function normalize_text_for_editor_storage($text)
	{
		if (!is_string($text) || $text === '') {
			return '';
		}

		$t = str_replace(array("\r\n", "\r"), "\n", $text);
		$t = html_entity_decode($t, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$t = html_entity_decode($t, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$t = str_replace(
			array(
				"\xc2\xa0",
				"\xe2\x80\xaf",
				"\xe2\x80\x8b",
				"\xef\xbb\xbf",
				"\xc2\xad",
			),
			array(' ', ' ', '', '', ''),
			$t
		);
		$t = preg_replace('/&nbsp;/i', ' ', $t);
		$t = preg_replace('/&#0*160;/i', ' ', $t);
		$t = preg_replace('/&#x0*a0;/i', ' ', $t);
		$t = str_replace(
			array(
				"\xe2\x80\x99",
				"\xe2\x80\x98",
				"\xe2\x80\x9c",
				"\xe2\x80\x9d",
				"\xe2\x80\x93",
				"\xe2\x80\x94",
			),
			array("'", "'", '"', '"', '-', '-'),
			$t
		);
		$t = preg_replace('/\x{00A0}/u', ' ', $t);
		$t = preg_replace('/\x{202F}/u', ' ', $t);

		return $t;
	}

	public static function normalize_post_title_for_sync($title)
	{
		if (!is_string($title) || $title === '') {
			return '';
		}
		$title = sanitize_text_field($title);
		$title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		return trim($title);
	}

	public static function prepare_for_wp_editor($content)
	{
		if (empty($content)) {
			return '';
		}

		$content = wp_kses_post($content);
		$content = str_replace(array("\r\n", "\r"), "\n", $content);
		$content = preg_replace('#\s*<p[^>]*>\s*#i', '', $content);
		$content = preg_replace('#\s*</p>\s*#i', "\n\n", $content);
		$content = preg_replace('#<br\s*/?>#i', "\n", $content);
		$content = preg_replace('#</?div[^>]*>#i', '', $content);
		$content = preg_replace("/\n{3,}/", "\n\n", $content);

		$lines = explode("\n", $content);
		$lines = array_map('trim', $lines);
		$content = implode("\n", $lines);

		$content = self::normalize_text_for_editor_storage($content);

		return trim($content);
	}

	public static function maybe_parse_json_list($content)
	{
		if (!is_string($content) || empty($content)) {
			return $content;
		}

		$trimmed = trim($content);
		if ($trimmed[0] !== '{' && $trimmed[0] !== '[') {
			return $content;
		}

		$decoded = json_decode($trimmed, true);
		if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
			return $content;
		}

		$items = array();

		if (isset($decoded['items']) && is_array($decoded['items'])) {
			foreach ($decoded['items'] as $item) {
				if (isset($item['content']) && $item['content'] !== '') {
					$items[] = $item['content'];
				}
			}
		} elseif (isset($decoded['highlights']) && is_array($decoded['highlights'])) {
			foreach ($decoded['highlights'] as $item) {
				if (is_string($item) && $item !== '') {
					$items[] = $item;
				}
			}
		} elseif (array_values($decoded) === $decoded) {
			foreach ($decoded as $item) {
				if (is_string($item) && $item !== '') {
					$items[] = $item;
				} elseif (is_array($item) && isset($item['content']) && $item['content'] !== '') {
					$items[] = $item['content'];
				}
			}
		}

		if (empty($items)) {
			return $content;
		}

		$html = '<ul>';
		foreach ($items as $item) {
			$html .= '<li>' . esc_html($item) . '</li>';
		}
		$html .= '</ul>';

		return $html;
	}

	public static function transform_list_field($content)
	{
		return self::prepare_for_wp_editor(self::maybe_parse_json_list($content));
	}

	public static function build_pricing_rows_from_api($pricing)
	{
		$rows = array();
		if (!empty($pricing) && is_array($pricing)) {
			foreach ($pricing as $p) {
				$rows[] = array(
					'type' => isset($p['pricing_type']) ? sanitize_text_field($p['pricing_type']) : '',
					'price' => isset($p['max_price']) ? sanitize_text_field($p['max_price']) : '',
					'sale_price' => '',
				);
			}
		}
		return $rows;
	}

	public static function build_location_rows_from_api($data)
	{
		if (!is_array($data)) {
			return array();
		}
		$location_rows = array();
		if (!empty($data['location_start'])) {
			$loc_start = is_string($data['location_start']) ? json_decode($data['location_start'], true) : $data['location_start'];
			if (is_array($loc_start) && !empty($loc_start[0])) {
				$l = $loc_start[0];
				$location_rows[] = array(
					'type' => 'Start',
					'address' => isset($l['building']) ? $l['building'] . ', ' . (isset($l['city']) ? $l['city'] : '') : '',
					'lat' => isset($l['latitude']) ? $l['latitude'] : '',
					'lng' => isset($l['longitude']) ? $l['longitude'] : '',
				);
			}
		}
		if (!empty($data['location']['coordinates'])) {
			$coords = $data['location']['coordinates'];
			$location_rows[] = array(
				'type' => 'Start',
				'address' => isset($data['location']['address']) ? $data['location']['address'] : '',
				'lat' => isset($coords[0]) ? $coords[0] : '',
				'lng' => isset($coords[1]) ? $coords[1] : '',
			);
		}
		return $location_rows;
	}
}
