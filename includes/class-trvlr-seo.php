<?php

/**
 * Attraction SEO schema (JSON-LD) from synced seo_metadata.
 *
 * @package Trvlr
 */

if (! defined('ABSPATH')) {
	exit;
}

class Trvlr_SEO
{
	const OPTION_MODE = 'trvlr_seo_schema_mode';

	/**
	 * @return void
	 */
	public function register_hooks()
	{
		add_action('wp_head', array($this, 'output_json_ld_script'), 5);
		add_filter('rank_math/json_ld', array($this, 'inject_rank_math'), 99, 2);
		add_filter('wpseo_schema_graph', array($this, 'inject_yoast'), 11, 1);
	}

	/**
	 * @return string auto|always|never
	 */
	public static function get_mode()
	{
		if (function_exists('trvlr_is_attraction_seo_schema_disabled')
			&& trvlr_is_attraction_seo_schema_disabled()
		) {
			return apply_filters('trvlr_seo_schema_mode', 'never');
		}

		$mode = (string) get_option(self::OPTION_MODE, 'auto');
		if (! in_array($mode, array('auto', 'always', 'never'), true)) {
			$mode = 'auto';
		}

		if ($mode === 'never') {
			$mode = 'auto';
		}

		return apply_filters('trvlr_seo_schema_mode', $mode);
	}

	/**
	 * @return bool
	 */
	public static function is_rank_math_active()
	{
		return defined('RANK_MATH_VERSION') || class_exists('RankMath');
	}

	/**
	 * @return bool
	 */
	public static function is_yoast_active()
	{
		return defined('WPSEO_VERSION');
	}

	/**
	 * @return void
	 */
	public function output_json_ld_script()
	{
		if (! $this->should_print_script()) {
			return;
		}

		$post_id = get_queried_object_id();
		$payload = $this->build_json_ld_payload($post_id);
		if ($payload === null) {
			return;
		}

		$json = wp_json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		if (! is_string($json) || $json === '') {
			return;
		}

		$json = str_replace('</', '<\/', $json);
		echo '<script type="application/ld+json">' . $json . '</script>' . "\n";
	}

	/**
	 * @param array $data
	 * @param mixed $jsonld
	 * @return array
	 */
	public function inject_rank_math($data, $jsonld = null)
	{
		if (! is_array($data) || ! $this->should_inject_into_seo_plugin('rank_math')) {
			return $data;
		}

		$post_id = get_queried_object_id();
		$pieces = $this->build_schema_pieces($post_id);
		if (empty($pieces)) {
			return $data;
		}

		if (! empty($pieces['faq'])) {
			foreach (array_keys($data) as $key) {
				$piece = $data[$key];
				if (! is_array($piece)) {
					continue;
				}
				$type = isset($piece['@type']) ? $piece['@type'] : '';
				if ($type === 'FAQPage' || (is_array($type) && in_array('FAQPage', $type, true))) {
					unset($data[$key]);
				}
			}
		}

		if (! empty($pieces['attraction'])) {
			$data['trvlr_tourist_attraction'] = $pieces['attraction'];
		}
		if (! empty($pieces['faq'])) {
			$data['trvlr_faq_page'] = $pieces['faq'];
		}

		return $data;
	}

	/**
	 * @param array $graph
	 * @return array
	 */
	public function inject_yoast($graph)
	{
		if (! is_array($graph) || ! $this->should_inject_into_seo_plugin('yoast')) {
			return $graph;
		}

		$post_id = get_queried_object_id();
		$pieces = $this->build_schema_pieces($post_id);
		if (empty($pieces)) {
			return $graph;
		}

		if (! empty($pieces['faq'])) {
			$filtered = array();
			foreach ($graph as $piece) {
				if (! is_array($piece)) {
					$filtered[] = $piece;
					continue;
				}
				$type = isset($piece['@type']) ? $piece['@type'] : '';
				if ($type === 'FAQPage' || (is_array($type) && in_array('FAQPage', $type, true))) {
					continue;
				}
				$filtered[] = $piece;
			}
			$graph = $filtered;
		}

		if (! empty($pieces['attraction'])) {
			$graph[] = $pieces['attraction'];
		}
		if (! empty($pieces['faq'])) {
			$graph[] = $pieces['faq'];
		}

		return $graph;
	}

	/**
	 * @return bool
	 */
	private function should_print_script()
	{
		$mode = self::get_mode();
		if ($mode === 'never') {
			return false;
		}

		if (! is_singular('trvlr_attraction')) {
			return false;
		}

		if ($mode === 'auto' && (self::is_rank_math_active() || self::is_yoast_active())) {
			return false;
		}

		$post_id = get_queried_object_id();
		$should = $this->build_json_ld_payload($post_id) !== null;

		return (bool) apply_filters('trvlr_should_output_seo_schema', $should, $post_id, 'script');
	}

	/**
	 * @param string $plugin rank_math|yoast
	 * @return bool
	 */
	private function should_inject_into_seo_plugin($plugin)
	{
		$mode = self::get_mode();
		if ($mode !== 'auto') {
			return false;
		}

		if (! is_singular('trvlr_attraction')) {
			return false;
		}

		if ($plugin === 'rank_math' && ! self::is_rank_math_active()) {
			return false;
		}
		if ($plugin === 'yoast' && ! self::is_yoast_active()) {
			return false;
		}
		if ($plugin === 'yoast' && self::is_rank_math_active()) {
			return false;
		}

		$post_id = get_queried_object_id();
		$should = ! empty($this->build_schema_pieces($post_id));

		return (bool) apply_filters('trvlr_should_output_seo_schema', $should, $post_id, $plugin);
	}

	/**
	 * @param int $post_id
	 * @return array|null @graph payload or null
	 */
	public function build_json_ld_payload($post_id)
	{
		$pieces = $this->build_schema_pieces($post_id);
		if (empty($pieces)) {
			return null;
		}

		$graph = array();
		if (! empty($pieces['attraction'])) {
			$graph[] = $pieces['attraction'];
		}
		if (! empty($pieces['faq'])) {
			$graph[] = $pieces['faq'];
		}

		if (empty($graph)) {
			return null;
		}

		$payload = array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		);

		return apply_filters('trvlr_seo_json_ld', $payload, $post_id);
	}

	/**
	 * @param int $post_id
	 * @return array{attraction?: array, faq?: array}
	 */
	public function build_schema_pieces($post_id)
	{
		$post_id = (int) $post_id;
		if ($post_id <= 0 || get_post_type($post_id) !== 'trvlr_attraction') {
			return array();
		}

		$metadata = get_trvlr_seo_metadata($post_id);
		if (! is_array($metadata)) {
			return array();
		}

		$permalink = get_permalink($post_id);
		if (! is_string($permalink) || $permalink === '') {
			$permalink = home_url('/');
		}

		$pieces = array();

		$attraction = $this->build_tourist_attraction($metadata, $permalink);
		if (! empty($attraction)) {
			$pieces['attraction'] = $attraction;
		}

		$faq = $this->build_faq_page($metadata, $permalink);
		if (! empty($faq)) {
			$pieces['faq'] = $faq;
		}

		return $pieces;
	}

	/**
	 * @param array  $metadata
	 * @param string $permalink
	 * @return array|null
	 */
	private function build_tourist_attraction(array $metadata, $permalink)
	{
		if (empty($metadata['schema']) || ! is_array($metadata['schema'])) {
			return null;
		}

		$schema = $metadata['schema'];
		unset($schema['@context']);

		if (isset($schema['mainEntity']) && $this->main_entity_looks_like_faq($schema['mainEntity'])) {
			unset($schema['mainEntity']);
		}

		if (empty($schema['@type'])) {
			$schema['@type'] = 'TouristAttraction';
		}

		if (empty($schema['@id'])) {
			$schema['@id'] = trailingslashit($permalink) . '#/schema/tourist-attraction';
		}

		if (empty($schema['url'])) {
			$schema['url'] = $permalink;
		}

		return $this->sanitize_schema_node($schema);
	}

	/**
	 * @param array  $metadata
	 * @param string $permalink
	 * @return array|null
	 */
	private function build_faq_page(array $metadata, $permalink)
	{
		if (empty($metadata['faq_schema']) || ! is_array($metadata['faq_schema'])) {
			return null;
		}

		$questions = array();
		foreach ($metadata['faq_schema'] as $item) {
			if (! is_array($item)) {
				continue;
			}

			$name = isset($item['name']) ? sanitize_text_field((string) $item['name']) : '';
			$answer_text = '';
			if (! empty($item['acceptedAnswer']) && is_array($item['acceptedAnswer'])) {
				$answer_text = isset($item['acceptedAnswer']['text'])
					? sanitize_textarea_field((string) $item['acceptedAnswer']['text'])
					: '';
			}

			if ($name === '' || $answer_text === '') {
				continue;
			}

			$questions[] = array(
				'@type'          => 'Question',
				'name'           => $name,
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $answer_text,
				),
			);
		}

		if (empty($questions)) {
			return null;
		}

		return array(
			'@type'      => 'FAQPage',
			'@id'        => trailingslashit($permalink) . '#/schema/faq',
			'url'        => $permalink,
			'mainEntity' => $questions,
		);
	}

	/**
	 * @param mixed $main_entity
	 * @return bool
	 */
	private function main_entity_looks_like_faq($main_entity)
	{
		if (! is_array($main_entity) || empty($main_entity)) {
			return false;
		}

		$first = isset($main_entity[0]) ? $main_entity[0] : reset($main_entity);
		if (! is_array($first)) {
			return false;
		}

		$type = isset($first['@type']) ? $first['@type'] : '';
		if ($type === 'Question' || (is_array($type) && in_array('Question', $type, true))) {
			return true;
		}

		return isset($first['acceptedAnswer']);
	}

	/**
	 * Recursively sanitize string leaves in a schema node.
	 *
	 * @param array $node
	 * @return array
	 */
	private function sanitize_schema_node(array $node)
	{
		$out = array();
		foreach ($node as $key => $value) {
			$key = (string) $key;
			if (is_array($value)) {
				$out[$key] = $this->sanitize_schema_node($value);
			} elseif (is_string($value)) {
				if ($key === 'description' || $key === 'text') {
					$out[$key] = sanitize_textarea_field($value);
				} else {
					$out[$key] = sanitize_text_field($value);
				}
			} elseif (is_int($value) || is_float($value) || is_bool($value) || $value === null) {
				$out[$key] = $value;
			}
		}

		return $out;
	}
}
