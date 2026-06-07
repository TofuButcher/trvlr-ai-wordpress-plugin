<?php

if (!defined('ABSPATH')) exit;

function trvlr_filter_toggle_svgs()
{
	return '<svg class="open-icon" width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
		<path d="M16.5007 2.54979C16.5005 2.13571 16.1651 1.80008 15.751 1.80008H2.55068C2.1367 1.80024 1.80113 2.13581 1.80098 2.54979V5.85008C1.80098 6.2642 2.1366 6.59963 2.55068 6.59979H15.751C16.1652 6.59979 16.5007 6.2643 16.5007 5.85008V2.54979ZM18.3007 5.85008C18.3007 7.25841 17.1593 8.39979 15.751 8.39979H2.55068C1.14249 8.39963 0.000976563 7.25831 0.000976563 5.85008V2.54979C0.00113489 1.1417 1.14259 0.000240857 2.55068 8.25295e-05H15.751C17.1592 8.25295e-05 18.3005 1.1416 18.3007 2.54979V5.85008Z" fill="white"/>
		<path d="M16.5007 12.4498C16.5005 12.0357 16.1651 11.7001 15.751 11.7001H2.55068C2.1367 11.7002 1.80113 12.0358 1.80098 12.4498V15.7501C1.80098 16.1642 2.1366 16.4996 2.55068 16.4998H15.751C16.1652 16.4998 16.5007 16.1643 16.5007 15.7501V12.4498ZM18.3007 15.7501C18.3007 17.1584 17.1593 18.2998 15.751 18.2998H2.55068C1.14249 18.2996 0.000976563 17.1583 0.000976563 15.7501V12.4498C0.00113489 11.0417 1.14259 9.90024 2.55068 9.90008H15.751C17.1592 9.90008 18.3005 11.0416 18.3007 12.4498V15.7501Z" fill="white"/>
		<path d="M4.20957 3.30007C4.70663 3.30007 5.10957 3.70302 5.10957 4.20007C5.10957 4.69713 4.70663 5.10007 4.20957 5.10007H4.20078C3.70372 5.10007 3.30078 4.69713 3.30078 4.20007C3.30078 3.70302 3.70372 3.30007 4.20078 3.30007H4.20957Z" fill="white"/>
		<path d="M4.20957 13.2001C4.70663 13.2001 5.10957 13.603 5.10957 14.1001C5.10957 14.5971 4.70663 15.0001 4.20957 15.0001H4.20078C3.70372 15.0001 3.30078 14.5971 3.30078 14.1001C3.30078 13.603 3.70372 13.2001 4.20078 13.2001H4.20957Z" fill="white"/>
	</svg>
	<svg class="close-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
		<path fill-rule="evenodd" clip-rule="evenodd" d="M13.5177 0.425911C14.0858 -0.141852 15.0064 -0.142044 15.5744 0.425911C16.1423 0.99387 16.1421 1.91449 15.5744 2.48255L10.0568 8.00013L15.5744 13.5177C16.1421 14.0858 16.1423 15.0064 15.5744 15.5743C15.0064 16.1423 14.0858 16.1421 13.5177 15.5743L8.00015 10.0568L2.48257 15.5743C1.91451 16.1422 0.99391 16.1423 0.425929 15.5743C-0.14205 15.0064 -0.141903 14.0858 0.425929 13.5177L5.94351 8.00013L0.425929 2.48255C-0.141902 1.91448 -0.142049 0.993889 0.425929 0.425911C0.993909 -0.142033 1.91451 -0.141908 2.48257 0.425911L8.00015 5.94349L13.5177 0.425911Z" fill="white"/>
	</svg>';
}

function trvlr_attraction_filter($atts = array())
{
	$atts = shortcode_atts(array(
		'target'        => '',
		'taxonomy'      => 'trvlr_attraction_tag',
		'terms'         => '',
		'default_label' => __('Most Popular', 'trvlr'),
		'default_slug'  => 'popular',
		'orderby'       => 'menu_order',
		'order'         => 'ASC',
	), $atts, 'trvlr_attraction_filter');

	$target = sanitize_html_class(trim($atts['target']));
	if (empty($target)) {
		return '';
	}

	$taxonomy = sanitize_key($atts['taxonomy']);

	$param_map = array(
		'trvlr_attraction_tag' => 'trvlr_tag_slug',
		'post_tag'             => 'tag_slug',
		'category'             => 'category_slug',
	);
	$query_param = isset($param_map[$taxonomy]) ? $param_map[$taxonomy] : 'trvlr_tag_slug';

	$term_args = array(
		'taxonomy'   => $taxonomy,
		'hide_empty' => true,
		'orderby'    => sanitize_key($atts['orderby']),
		'order'      => strtoupper(sanitize_text_field($atts['order'])) === 'DESC' ? 'DESC' : 'ASC',
	);

	$explicit_slugs = array();
	if (!empty($atts['terms'])) {
		$explicit_slugs = array_filter(array_map('sanitize_text_field', array_map('trim', explode(',', $atts['terms']))));
		$term_args['slug'] = array_values($explicit_slugs);
	}

	$terms = get_terms($term_args);
	if (is_wp_error($terms)) {
		$terms = array();
	}

	if ($taxonomy === 'category') {
		$terms = array_values(array_filter($terms, function ($term) {
			return $term->slug !== 'uncategorized' && $term->slug !== 'uncategorised';
		}));
	}

	$default_slug  = sanitize_text_field($atts['default_slug']);
	$default_label = esc_html($atts['default_label']);

	if (!empty($default_slug)) {
		$terms = array_values(array_filter($terms, function ($term) use ($default_slug) {
			return $term->slug !== $default_slug;
		}));
	}

	if (!empty($explicit_slugs)) {
		$slug_order = array_flip(array_values($explicit_slugs));
		usort($terms, function ($a, $b) use ($slug_order) {
			$ai = isset($slug_order[$a->slug]) ? $slug_order[$a->slug] : PHP_INT_MAX;
			$bi = isset($slug_order[$b->slug]) ? $slug_order[$b->slug] : PHP_INT_MAX;
			return $ai - $bi;
		});
	}

	$default_query = wp_json_encode(array($query_param => $default_slug));
	$buttons = sprintf(
		'<button class="filter-btn active" data-trvlr-filter-target="%1$s" data-trvlr-query="%2$s">' .
			'<span class="filter-btn__label">%3$s</span></button>',
		esc_attr($target),
		esc_attr($default_query),
		$default_label
	);

	foreach ($terms as $term) {
		$term_query = wp_json_encode(array($query_param => $term->slug));
		$buttons .= sprintf(
			'<button class="filter-btn" data-trvlr-filter-target="%1$s" data-trvlr-query="%2$s">' .
				'<span class="filter-btn__label">%3$s</span></button>',
			esc_attr($target),
			esc_attr($term_query),
			esc_html($term->name)
		);
	}

	$html = sprintf(
		'<div class="trvlr-attraction-filter tour-filters" data-trvlr-filter-target="%1$s" role="navigation" aria-label="%2$s">
			<button class="filter-btn active open-filter-menu" aria-label="%3$s" aria-haspopup="true">
				<span class="filter-btn__label">%4$s</span>
			</button>
			<div class="filter-buttons__container" role="menu">
				<button class="filter-btns-dropdown__toggle icon-toggle" aria-label="%5$s" aria-expanded="false">%6$s</button>
				<div class="filter-buttons__container-inner">
					<div class="filter-buttons__container-content">%7$s</div>
				</div>
			</div>
		</div>',
		esc_attr($target),
		esc_attr__('Attraction filters', 'trvlr'),
		esc_attr__('Open attraction filters', 'trvlr'),
		$default_label,
		esc_attr__('Toggle filters', 'trvlr'),
		trvlr_filter_toggle_svgs(),
		$buttons
	);

	wp_enqueue_script('trvlr-attraction-filter');
	wp_enqueue_style('trvlr-attraction-filter');

	return apply_filters('trvlr_attraction_filter', $html, $atts, $target);
}

function trvlr_attraction_sort($atts = array())
{
	$atts = shortcode_atts(array(
		'target' => '',
	), $atts, 'trvlr_attraction_sort');

	$target = sanitize_html_class(trim($atts['target']));
	if (empty($target)) {
		return '';
	}

	$options = array(
		array(
			'label' => __('Sort by A-Z', 'trvlr'),
			'query' => array('trvlr_sort' => 'az'),
			'active' => true,
		),
		array(
			'label' => __('Sort by Most Popular', 'trvlr'),
			'query' => array('trvlr_sort' => 'popular'),
			'active' => false,
		),
		array(
			'label' => __('Sort by Price', 'trvlr'),
			'query' => array('trvlr_sort' => 'price'),
			'active' => false,
		),
	);

	$buttons = '';
	foreach ($options as $option) {
		$buttons .= sprintf(
			'<button class="filter-btn%4$s" data-trvlr-filter-target="%1$s" data-trvlr-query="%2$s">' .
				'<span class="filter-btn__label">%3$s</span></button>',
			esc_attr($target),
			esc_attr(wp_json_encode($option['query'])),
			esc_html($option['label']),
			$option['active'] ? ' active' : ''
		);
	}

	$html = sprintf(
		'<div class="trvlr-attraction-filter trvlr-attraction-sort tour-filters" data-trvlr-filter-target="%1$s" role="navigation" aria-label="%2$s">
			<button class="filter-btn active open-filter-menu" aria-label="%3$s" aria-haspopup="true">
				<span class="filter-btn__label">%4$s</span>
			</button>
			<div class="filter-buttons__container" role="menu">
				<button class="filter-btns-dropdown__toggle icon-toggle" aria-label="%5$s" aria-expanded="false">%6$s</button>
				<div class="filter-buttons__container-inner">
					<div class="filter-buttons__container-content">%7$s</div>
				</div>
			</div>
		</div>',
		esc_attr($target),
		esc_attr__('Attraction sorting', 'trvlr'),
		esc_attr__('Open attraction sorting', 'trvlr'),
		esc_html__('Sort by A-Z', 'trvlr'),
		esc_attr__('Toggle sorting', 'trvlr'),
		trvlr_filter_toggle_svgs(),
		$buttons
	);

	wp_enqueue_script('trvlr-attraction-filter');
	wp_enqueue_style('trvlr-attraction-filter');

	return apply_filters('trvlr_attraction_sort', $html, $atts, $target);
}
