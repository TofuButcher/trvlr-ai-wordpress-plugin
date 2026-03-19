<?php

/**
 * Static data transformation utilities for TRVLR attraction fields
 *
 * @package    Trvlr
 * @subpackage Trvlr/includes
 */

class Trvlr_Data_Transform
{
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
}
