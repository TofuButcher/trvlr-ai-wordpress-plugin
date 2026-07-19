<?php

if (!defined('ABSPATH')) exit;

/**
 * Build a WP_Query args array from the named shortcode/API parameters.
 *
 * Accepts the same parameter set as `trvlr_attraction_cards` shortcode plus a `query_args`
 * key for a full WP_Query override. Handles `ids` â†’ `post__in` and `exclude` â†’
 * `post__not_in` conversions, and builds `tax_query` from named taxonomy params.
 *
 * Pass `tax_query` or `meta_query` directly to override the built-in taxonomy/meta handling.
 * Pass `query_args` (array) to bypass named-param processing entirely.
 *
 * @param array $args Named params or `query_args` override.
 * @return array WP_Query-ready argument array.
 */
function trvlr_build_query_args($args = array())
{
	if (isset($args['query_args']) && is_array($args['query_args'])) {
		$query_args = $args['query_args'];
		if (!isset($query_args['post_type'])) {
			$query_args['post_type'] = 'trvlr_attraction';
		}
		if (isset($args['paged'])) {
			$query_args['paged'] = max(1, intval($args['paged']));
		}
		if (!empty($args['trvlr_sort'])) {
			$query_args = trvlr_apply_attraction_sort_query_args($query_args, sanitize_key($args['trvlr_sort']));
		}
		return $query_args;
	}

	$defaults = array(
		'post_type'      => 'trvlr_attraction',
		'posts_per_page' => 16,
		'post_status'    => 'publish',
	);

	$query_args = wp_parse_args($args, $defaults);

	if (!empty($args['ids']) && !isset($args['post__in'])) {
		$ids = is_array($args['ids']) ? $args['ids'] : array_map('intval', explode(',', $args['ids']));
		$ids = array_filter($ids);
		if (!empty($ids)) {
			$query_args['post__in'] = $ids;
			if (empty($args['orderby'])) {
				$query_args['orderby'] = 'post__in';
			}
		}
	}

	$tax_groups = array();

	if (!empty($args['tag']) || !empty($args['tag_id']) || !empty($args['tag_slug'])) {
		$clauses = array();
		if (!empty($args['tag'])) {
			$terms = is_array($args['tag']) ? $args['tag'] : explode(',', $args['tag']);
			$terms = array_filter(array_map('sanitize_text_field', array_map('trim', $terms)));
			if (!empty($terms)) {
				$clauses[] = array('taxonomy' => 'post_tag', 'field' => 'slug', 'terms' => $terms);
			}
		}
		if (!empty($args['tag_id'])) {
			$terms = is_array($args['tag_id']) ? $args['tag_id'] : array_map('intval', explode(',', $args['tag_id']));
			$terms = array_filter($terms);
			if (!empty($terms)) {
				$clauses[] = array('taxonomy' => 'post_tag', 'field' => 'term_id', 'terms' => $terms);
			}
		}
		if (!empty($args['tag_slug'])) {
			$terms = is_array($args['tag_slug']) ? $args['tag_slug'] : explode(',', $args['tag_slug']);
			$terms = array_filter(array_map('sanitize_text_field', array_map('trim', $terms)));
			if (!empty($terms)) {
				$clauses[] = array('taxonomy' => 'post_tag', 'field' => 'slug', 'terms' => $terms);
			}
		}
		if (!empty($clauses)) {
			$tax_groups[] = count($clauses) > 1
				? array_merge(array('relation' => !empty($args['tag_relation']) ? strtoupper(sanitize_text_field($args['tag_relation'])) : 'AND'), $clauses)
				: $clauses[0];
		}
	}

	if (!empty($args['category']) || !empty($args['category_id']) || !empty($args['category_slug'])) {
		$clauses = array();
		if (!empty($args['category'])) {
			$terms = is_array($args['category']) ? $args['category'] : explode(',', $args['category']);
			$terms = array_filter(array_map('sanitize_text_field', array_map('trim', $terms)));
			if (!empty($terms)) {
				$clauses[] = array('taxonomy' => 'category', 'field' => 'slug', 'terms' => $terms);
			}
		}
		if (!empty($args['category_id'])) {
			$terms = is_array($args['category_id']) ? $args['category_id'] : array_map('intval', explode(',', $args['category_id']));
			$terms = array_filter($terms);
			if (!empty($terms)) {
				$clauses[] = array('taxonomy' => 'category', 'field' => 'term_id', 'terms' => $terms);
			}
		}
		if (!empty($args['category_slug'])) {
			$terms = is_array($args['category_slug']) ? $args['category_slug'] : explode(',', $args['category_slug']);
			$terms = array_filter(array_map('sanitize_text_field', array_map('trim', $terms)));
			if (!empty($terms)) {
				$clauses[] = array('taxonomy' => 'category', 'field' => 'slug', 'terms' => $terms);
			}
		}
		if (!empty($clauses)) {
			$tax_groups[] = count($clauses) > 1
				? array_merge(array('relation' => !empty($args['category_relation']) ? strtoupper(sanitize_text_field($args['category_relation'])) : 'AND'), $clauses)
				: $clauses[0];
		}
	}

	if (!empty($args['trvlr_tag']) || !empty($args['trvlr_tag_id']) || !empty($args['trvlr_tag_slug'])) {
		$clauses = array();
		if (!empty($args['trvlr_tag'])) {
			$terms = is_array($args['trvlr_tag']) ? $args['trvlr_tag'] : explode(',', $args['trvlr_tag']);
			$terms = array_filter(array_map('sanitize_text_field', array_map('trim', $terms)));
			if (!empty($terms)) {
				$clauses[] = array('taxonomy' => 'trvlr_attraction_tag', 'field' => 'name', 'terms' => $terms);
			}
		}
		if (!empty($args['trvlr_tag_id'])) {
			$terms = is_array($args['trvlr_tag_id']) ? $args['trvlr_tag_id'] : array_map('intval', explode(',', $args['trvlr_tag_id']));
			$terms = array_filter($terms);
			if (!empty($terms)) {
				$clauses[] = array('taxonomy' => 'trvlr_attraction_tag', 'field' => 'term_id', 'terms' => $terms);
			}
		}
		if (!empty($args['trvlr_tag_slug'])) {
			$terms = is_array($args['trvlr_tag_slug']) ? $args['trvlr_tag_slug'] : explode(',', $args['trvlr_tag_slug']);
			$terms = array_filter(array_map('sanitize_text_field', array_map('trim', $terms)));
			if (!empty($terms)) {
				$clauses[] = array('taxonomy' => 'trvlr_attraction_tag', 'field' => 'slug', 'terms' => $terms);
			}
		}
		if (!empty($clauses)) {
			$tax_groups[] = count($clauses) > 1
				? array_merge(array('relation' => !empty($args['trvlr_tag_relation']) ? strtoupper(sanitize_text_field($args['trvlr_tag_relation'])) : 'AND'), $clauses)
				: $clauses[0];
		}
	}

	if (!empty($tax_groups)) {
		if (count($tax_groups) > 1) {
			$query_args['tax_query'] = array_merge(array('relation' => 'AND'), $tax_groups);
		} else {
			$single = $tax_groups[0];
			$query_args['tax_query'] = isset($single['relation']) ? $single : array($single);
		}
	}

	if (!empty($args['tax_query']) && is_array($args['tax_query'])) {
		$query_args['tax_query'] = $args['tax_query'];
	}

	if (!empty($args['meta_query']) && is_array($args['meta_query'])) {
		$query_args['meta_query'] = $args['meta_query'];
	}

	if (!empty($args['meta_key'])) {
		$query_args['meta_key'] = $args['meta_key'];
		if (!empty($args['meta_value'])) {
			$query_args['meta_value'] = $args['meta_value'];
		}
		if (!empty($args['meta_compare'])) {
			$query_args['meta_compare'] = $args['meta_compare'];
		}
	}

	if (!empty($args['exclude'])) {
		$query_args['post__not_in'] = is_array($args['exclude'])
			? $args['exclude']
			: array_map('intval', explode(',', $args['exclude']));
	}

	if (!empty($args['trvlr_sort'])) {
		$query_args = trvlr_apply_attraction_sort_query_args($query_args, sanitize_key($args['trvlr_sort']));
	}

	$custom_keys = array(
		'tag',
		'tag_id',
		'tag_slug',
		'tag_relation',
		'category',
		'category_id',
		'category_slug',
		'category_relation',
		'trvlr_tag',
		'trvlr_tag_id',
		'trvlr_tag_slug',
		'trvlr_tag_relation',
		'exclude',
		'query_args',
		'ids',
		'grid_id',
		'trvlr_sort',
	);
	foreach ($custom_keys as $key) {
		unset($query_args[$key]);
	}

	return $query_args;
}

function trvlr_apply_attraction_sort_query_args($query_args, $sort)
{
	if ($sort === 'az') {
		$query_args['orderby'] = 'title';
		$query_args['order'] = 'ASC';
		return $query_args;
	}

	if ($sort === 'popular') {
		return trvlr_sort_attraction_query_by_popular($query_args);
	}

	if ($sort !== 'price') {
		return $query_args;
	}

	$lookup_args = $query_args;
	$lookup_args['fields'] = 'ids';
	$lookup_args['posts_per_page'] = -1;
	$lookup_args['nopaging'] = true;
	$lookup_args['no_found_rows'] = true;
	unset($lookup_args['paged'], $lookup_args['orderby'], $lookup_args['order']);

	$ids = get_posts($lookup_args);
	if (empty($ids)) {
		$query_args['post__in'] = array(0);
		$query_args['orderby'] = 'post__in';
		unset($query_args['order']);
		return $query_args;
	}

	$positions = array_flip($ids);

	usort($ids, function ($a, $b) use ($positions) {
		$a_price = trvlr_get_sortable_lowest_price($a);
		$b_price = trvlr_get_sortable_lowest_price($b);

		if ($a_price === $b_price) {
			return $positions[$a] - $positions[$b];
		}

		if ($a_price === null) {
			return 1;
		}

		if ($b_price === null) {
			return -1;
		}

		return $a_price <=> $b_price;
	});

	$query_args['post__in'] = array_map('intval', $ids);
	$query_args['orderby'] = 'post__in';
	unset($query_args['order']);

	return $query_args;
}

function trvlr_sort_attraction_query_by_popular($query_args)
{
	$lookup_args = $query_args;
	$lookup_args['fields'] = 'ids';
	$lookup_args['posts_per_page'] = -1;
	$lookup_args['nopaging'] = true;
	$lookup_args['no_found_rows'] = true;
	unset($lookup_args['paged'], $lookup_args['orderby'], $lookup_args['order']);

	$ids = get_posts($lookup_args);
	if (empty($ids)) {
		$query_args['post__in'] = array(0);
		$query_args['orderby'] = 'post__in';
		unset($query_args['order']);
		return $query_args;
	}

	$positions = array_flip($ids);

	usort($ids, function ($a, $b) use ($positions) {
		$a_popular = has_term('popular', 'trvlr_attraction_tag', $a) ? 1 : 0;
		$b_popular = has_term('popular', 'trvlr_attraction_tag', $b) ? 1 : 0;

		if ($a_popular === $b_popular) {
			return $positions[$a] - $positions[$b];
		}

		return $b_popular - $a_popular;
	});

	$query_args['post__in'] = array_map('intval', $ids);
	$query_args['orderby'] = 'post__in';
	unset($query_args['order']);

	return $query_args;
}

function trvlr_get_sortable_lowest_price($post_id)
{
	$pricing = get_post_meta($post_id, 'trvlr_pricing', true);
	if (!is_array($pricing)) {
		return null;
	}

	$prices = array();
	foreach ($pricing as $price_option) {
		foreach (array('sale_price', 'price', 'min_price', 'max_price') as $price_key) {
			if (!empty($price_option[$price_key])) {
				$parsed_price = trvlr_parse_sortable_price($price_option[$price_key]);
				if ($parsed_price !== null) {
					$prices[] = $parsed_price;
				}
			}
		}
	}

	return !empty($prices) ? min($prices) : null;
}

function trvlr_parse_sortable_price($value)
{
	if (is_numeric($value)) {
		return (float) $value;
	}

	$value = preg_replace('/[^\d.]/', '', (string) $value);
	if ($value === '') {
		return null;
	}

	$parts = explode('.', $value);
	if (count($parts) > 2) {
		$value = array_shift($parts) . '.' . implode('', $parts);
	}

	return is_numeric($value) ? (float) $value : null;
}

/**
 * Run a WP_Query for attractions and render the cards HTML.
 *
 * Returns an array with the rendered inner cards markup plus pagination metadata.
 * The `html` value is a `<div class="trvlr-cards">` element containing individual
 * card items (or a no-results message). It does NOT include the outer container div.
 *
 * Used internally by `trvlr_cards()` and the REST API endpoint so both share the
 * same rendering logic.
 *
 * @param array $query_args WP_Query-ready argument array (e.g. from `trvlr_build_query_args()`).
 * @return array {
 *     @type string $html         Inner `<div class="trvlr-cards">` HTML.
 *     @type int    $found_posts  Total posts matching the query (ignoring pagination).
 *     @type int    $max_pages    Total number of pages.
 *     @type int    $current_page Current page number.
 * }
 */
function trvlr_build_cards_result($query_args = array(), $card_variant = 'default')
{
	$query = new WP_Query($query_args);

	ob_start();
	echo '<div class="trvlr-cards">';
	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			echo trvlr_card(get_the_ID(), $card_variant);
		}
		wp_reset_postdata();
	} else {
		echo '<p>' . esc_html__('No posts found.', 'trvlr') . '</p>';
	}
	echo '</div>';

	return array(
		'html'         => ob_get_clean(),
		'found_posts'  => (int) $query->found_posts,
		'max_pages'    => (int) $query->max_num_pages,
		'current_page' => (int) max(1, isset($query_args['paged']) ? $query_args['paged'] : 1),
	);
}

function trvlr_cards_should_use_primary_loop_for_attractions()
{
	$q = isset($GLOBALS['wp_the_query']) ? $GLOBALS['wp_the_query'] : null;

	if (!$q instanceof WP_Query) {
		return false;
	}

	if ($q->is_post_type_archive('trvlr_attraction')) {
		return true;
	}

	if (! ($q->is_archive() || $q->is_tax() || $q->is_category() || $q->is_tag())) {
		return false;
	}

	$pt = $q->get('post_type');

	if ($pt === '' || $pt === 'post') {
		return false;
	}

	if ($pt === 'trvlr_attraction') {
		return true;
	}

	if (is_array($pt)) {
		$pt = array_values(array_unique(array_map('sanitize_key', $pt)));

		return count($pt) === 1 && $pt[0] === 'trvlr_attraction';
	}

	return false;
}

function trvlr_cards_get_archive_term_query_args()
{
	if (!is_category() && !is_tag() && !is_tax()) {
		return null;
	}

	$term = get_queried_object();

	if (!($term instanceof WP_Term)) {
		return null;
	}

	$tax = get_taxonomy($term->taxonomy);

	if (!$tax || !is_array($tax->object_type)) {
		return null;
	}

	if (!in_array('trvlr_attraction', $tax->object_type, true)) {
		return null;
	}

	$q = isset($GLOBALS['wp_the_query']) ? $GLOBALS['wp_the_query'] : null;
	$paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));

	$posts_per_page = (int) get_option('posts_per_page');

	if ($q instanceof WP_Query) {
		$mq_ppp = (int) $q->get('posts_per_page');

		if ($mq_ppp > 0) {
			$posts_per_page = $mq_ppp;
		}
	}

	return array(
		'tax_query'      => array(
			array(
				'taxonomy' => $term->taxonomy,
				'field'    => 'term_id',
				'terms'    => (int) $term->term_id,
			),
		),
		'paged'          => $paged,
		'posts_per_page' => $posts_per_page,
	);
}

function trvlr_cards_has_explicit_archive_scope($args)
{
	$scope_keys = array(
		'ids',
		'post__in',
		'tax_query',
		'query_args',
		'tag',
		'tag_id',
		'tag_slug',
		'category',
		'category_id',
		'category_slug',
		'trvlr_tag',
		'trvlr_tag_id',
		'trvlr_tag_slug',
	);

	foreach ($scope_keys as $key) {
		if (!empty($args[$key])) {
			return true;
		}
	}

	return false;
}

function trvlr_cards_maybe_inherit_archive_query($args)
{
	if (empty($args) || !(is_post_type_archive() || is_tax() || is_category() || is_tag())) {
		return $args;
	}

	if (trvlr_cards_has_explicit_archive_scope($args)) {
		return $args;
	}

	$archive_args = trvlr_cards_get_archive_term_query_args();
	if ($archive_args === null) {
		return $args;
	}

	$grid_id = isset($args['grid_id']) ? $args['grid_id'] : '';
	$sort = isset($args['trvlr_sort']) ? sanitize_key($args['trvlr_sort']) : '';
	unset($args['grid_id']);
	unset($args['trvlr_sort']);

	unset($archive_args['paged']);
	$query_args = trvlr_build_query_args(wp_parse_args($args, $archive_args));

	$inherited = array('query_args' => $query_args);
	if ($grid_id !== '') {
		$inherited['grid_id'] = $grid_id;
	}
	if ($sort !== '') {
		$inherited['trvlr_sort'] = $sort;
	}

	return $inherited;
}

/**
 * Output a grid of attraction cards from a WP_Query over `trvlr_attraction`.
 *
 * Usage:
 * - With an empty `$args` on a supported archive: if the main query is already limited to
 *   `trvlr_attraction` (CPT archive or tax archive whose main query targets that type), the
 *   main loop is used. If the viewed term applies to attractions but the main query is still
 *   default `post` (e.g. `post_tag` URLs), a secondary query inherits the term and `post_type`
 *   `trvlr_attraction`. Otherwise runs a dedicated query as below.
 * - Otherwise runs a dedicated query. Pass `query_args` as a full `WP_Query` argument array
 *   for a complete override (defaults `post_type` to `trvlr_attraction` if omitted).
 * - Or pass the parameters below; they are merged with defaults (`posts_per_page` 16, etc.).
 *
 * Taxonomy filters (combined with AND across groups; within a group use the matching `*_relation`):
 * - **WordPress tags** (`post_tag`): `tag` (comma-separated slugs), `tag_id` (comma-separated term IDs),
 *   `tag_slug` (comma-separated slugs), `tag_relation` (`AND`/`OR` when multiple of these dimensions apply).
 *   Synced/API â€œattraction typeâ€ terms live on `trvlr_attraction_tag` â€” filter those with `trvlr_tag`, `trvlr_tag_slug`, or `trvlr_tag_id`, not `tag` / `tag_slug`.
 * - **Categories** (`category`): `category` (comma-separated slugs), `category_id` (comma-separated term IDs),
 *   `category_slug` (comma-separated slugs), `category_relation`.
 * - **TRVLR attraction tags** (`trvlr_attraction_tag`): `trvlr_tag` (comma-separated names),
 *   `trvlr_tag_id` (comma-separated term IDs), `trvlr_tag_slug` (comma-separated slugs), `trvlr_tag_relation`.
 *
 * Other common `$args`: `posts_per_page`, `orderby`, `order`, `post__in` (via shortcode `ids`), `exclude` (post IDs
 * â†’ `post__not_in`), `meta_key` / `meta_value` / `meta_compare`, `meta_query`, or a raw `tax_query` array (replaces
 * all built-in taxonomy arguments above).
 *
 * Shortcode: `[trvlr_attraction_cards]` accepts the same argument names as attributes.
 *
 * @param array $args Query options, `query_args` override, or empty for main-query mode.
 * @return string HTML for the cards container.
 */
function trvlr_cards($args = array())
{
	$use_main_query = false;

	if (empty($args)) {
		if (is_post_type_archive() || is_tax() || is_category() || is_tag()) {
			if (trvlr_cards_should_use_primary_loop_for_attractions()) {
				$use_main_query = true;
			} else {
				$archive_term_args = trvlr_cards_get_archive_term_query_args();
				if ($archive_term_args !== null) {
					$args = $archive_term_args;
				}
			}
		}
	}

	$args = trvlr_cards_maybe_inherit_archive_query($args);

	ob_start();

	if ($use_main_query) {
		echo '<div class="trvlr-cards-container"><div class="trvlr-cards">';
		if (have_posts()) {
			while (have_posts()) {
				the_post();
				echo trvlr_card(get_the_ID());
			}
		} else {
			echo '<p>' . esc_html__('No posts found.', 'trvlr') . '</p>';
		}
		echo '</div></div>';
	} else {
		static $grid_counter    = 0;
		static $script_enqueued = false;

		$grid_counter++;

		$grid_id = !empty($args['grid_id'])
			? sanitize_html_class($args['grid_id'])
			: 'trvlr-grid-' . $grid_counter;

		// Extract card_variant before building query args (not a WP_Query key).
		$card_variant = 'default';
		if (!empty($args['card_variant'])) {
			$card_variant = sanitize_key($args['card_variant']);
		}

		$initial_query = $args;
		unset($initial_query['grid_id']);

		// Remove card_variant from args passed to trvlr_build_query_args (not a WP_Query key),
		// but keep it in $initial_query so the JS query state includes it for AJAX fetches.
		$query_build_args = $args;
		unset($query_build_args['card_variant']);

		$query_args = trvlr_build_query_args($query_build_args);
		$result     = trvlr_build_cards_result($query_args, $card_variant);

		// Derive a category--{slug} class from the active tax_query for the initial render.
		$category_class = '';
		if (!empty($query_args['tax_query'])) {
			$tq = $query_args['tax_query'];
			// Normalise: single-clause arrays are stored as [[...]], multi-clause with 'relation' key.
			$clauses = is_array($tq) ? $tq : array();
			foreach ($clauses as $k => $clause) {
				if ($k === 'relation' || !is_array($clause) || empty($clause['taxonomy'])) continue;
				if ($clause['taxonomy'] === 'category' && !empty($clause['terms'])) {
					$slug = is_array($clause['terms']) ? $clause['terms'][0] : $clause['terms'];
					$category_class = ' category--' . sanitize_html_class($slug);
					break;
				}
			}
		}

		$container_atts = array(
			'class'                     => 'trvlr-cards-container' . $category_class,
			'data-trvlr-grid-id'        => $grid_id,
			'data-trvlr-initial-query'  => wp_json_encode($initial_query),
			'data-trvlr-found-posts'    => $result['found_posts'],
			'data-trvlr-max-pages'      => $result['max_pages'],
			'data-trvlr-current-page'   => $result['current_page'],
		);

		$container_atts = apply_filters('trvlr_cards_container_atts', $container_atts, $grid_id, $args, $result);

		$attr_html = '';
		foreach ($container_atts as $attr_key => $attr_value) {
			$attr_html .= ' ' . esc_attr($attr_key) . '="' . esc_attr($attr_value) . '"';
		}

		echo '<div' . $attr_html . '>' . $result['html'] . '</div>';

		if (!$script_enqueued) {
			wp_enqueue_script('trvlr-query-manager');
			$script_enqueued = true;
		}
	}

	return apply_filters('trvlr_cards', ob_get_clean(), $args);
}
