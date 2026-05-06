<?php

if (!defined('ABSPATH')) exit;

function trvlr_title($post_id = null, $level = 1)
{
	$post_id = $post_id ?: get_the_ID();
	$title = get_trvlr_title($post_id);
	$tag = 'h' . absint($level);

	$output = "<{$tag} class=\"trvlr-title\">" . esc_html($title) . "</{$tag}>";
	return apply_filters('trvlr_title', $output, $post_id, $level);
}

function trvlr_duration($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$duration = get_trvlr_duration($post_id);

	if (!$duration || $duration === '0' || $duration === '0-0-0') {
		return '';
	}

	ob_start();
?>
	<div class="trvlr-duration">
		<svg class="trvlr-duration__icon">
			<use href="#icon-clock"></use>
		</svg>
		<span class="trvlr-duration__value"><?php echo esc_html($duration); ?></span>
	</div>
<?php
	return apply_filters('trvlr_duration', ob_get_clean(), $post_id);
}

function trvlr_sale($post_id = null, $description = null)
{
	$post_id = $post_id ?: get_the_ID();
	$is_on_sale = get_trvlr_is_on_sale($post_id);
	$always_show = false;


	$sale_description = get_trvlr_sale_description($post_id);

	if ($description) {
		$always_show = true;
		$sale_description = $description;
	}

	if (!$is_on_sale && !$always_show) {
		return '';
	}

	ob_start();
?>
	<div class="trvlr-sale">
		<?php echo trvlr_sale_badge($post_id, $always_show); ?>
		<?php if ($sale_description) : ?>
			<?php echo trvlr_sale_description($post_id, $sale_description); ?>
		<?php endif; ?>
	</div>
<?php
	return apply_filters('trvlr_sale', ob_get_clean(), $post_id);
}

function trvlr_popular_badge($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$attraction_tags = get_trvlr_attraction_tags($post_id);

	$is_popular = false;
	if (is_array($attraction_tags)) {
		foreach ($attraction_tags as $term) {
			if (is_object($term) && isset($term->slug) && $term->slug === 'popular') {
				$is_popular = true;
				break;
			}
		}
	}

	if ($is_popular) {
		return '<div class="trvlr-popular-badge">
			<svg class="trvlr-icon trvlr-popular-badge__icon">
				<use href="#icon-star"></use>
			</svg>
			<span class="trvlr-popular-badge__text">Popular</span>
		</div>';
	}
	return '';
}

function trvlr_sale_badge($post_id = null, $always_show = false, $show_icon = true)
{
	$post_id = $post_id ?: get_the_ID();
	$is_on_sale = get_trvlr_is_on_sale($post_id);
	if ($always_show) {
		$is_on_sale = true;
	}

	if (!$is_on_sale) {
		return '';
	}

	$output = '<div class="trvlr-sale__badge"><span>';

	if ($show_icon) {
		$output .= '% Special Deal';
	} else {
		$output .= 'Special Deal';
	}

	$output .= '</span></div>';

	return apply_filters('trvlr_sale_badge', $output, $post_id);
}

function trvlr_sale_description($post_id = null, $description = null)
{
	$post_id = $post_id ?: get_the_ID();
	$description = $description ?: get_trvlr_sale_description($post_id);

	if (!$description) {
		return '';
	}

	$output = '<span class="trvlr-sale__description">' . esc_html($description) . '</span>';
	return apply_filters('trvlr_sale_description', $output, $post_id);
}

function trvlr_gallery($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$media_ids = get_trvlr_media($post_id, true);
	$gallery_ids = array_unique(array_filter($media_ids));

	if (empty($gallery_ids)) {
		return '';
	}

	if (count($gallery_ids) === 1) {
		$output = '<div class="trvlr-gallery trvlr-gallery--single">' . wp_get_attachment_image($gallery_ids[0], 'large') . '</div>';
		return apply_filters('trvlr_gallery', $output, $post_id, $gallery_ids);
	}

	ob_start();
	$main_id = 'trvlr-main-slider-' . $post_id;
	$nav_id = 'trvlr-nav-slider-' . $post_id;
?>
	<div class="trvlr-gallery trvlr-gallery--slider">
		<div id="<?php echo esc_attr($main_id); ?>" class="trvlr-gallery__main splide">
			<div class="splide__track">
				<ul class="splide__list">
					<?php foreach ($gallery_ids as $image_id) : ?>
						<li class="splide__slide">
							<?php echo wp_get_attachment_image($image_id, 'large'); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<div id="<?php echo esc_attr($nav_id); ?>" class="trvlr-gallery__nav splide">
			<div class="splide__track">
				<ul class="splide__list">
					<?php foreach ($gallery_ids as $image_id) : ?>
						<li class="splide__slide">
							<?php echo wp_get_attachment_image($image_id, 'thumbnail'); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
<?php
	return apply_filters('trvlr_gallery', ob_get_clean(), $post_id, $gallery_ids);
}

function trvlr_short_description($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$content = get_trvlr_short_description($post_id);

	if (!$content) {
		return '';
	}

	$output = '<div class="trvlr-short-description">' . $content . '</div>';
	return apply_filters('trvlr_short_description', $output, $post_id);
}

function trvlr_description($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$content = get_trvlr_description($post_id);

	if (!$content) {
		return '';
	}

	$output = '<div class="trvlr-description">' . $content . '</div>';
	return apply_filters('trvlr_description', $output, $post_id);
}

function trvlr_inclusions($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$content = get_trvlr_inclusions($post_id);

	if (!$content) {
		return '';
	}

	$output = '<div class="trvlr-inclusions">' . $content . '</div>';
	return apply_filters('trvlr_inclusions', $output, $post_id);
}

function trvlr_locations($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$locations = get_trvlr_locations($post_id);
	$start_time = get_trvlr_start_time($post_id);
	$end_time = get_trvlr_end_time($post_id);

	if (empty($locations)) {
		return '';
	}

	ob_start();
?>
	<div class="trvlr-locations">
		<?php foreach ($locations as $loc) : ?>
			<?php
			$addr = isset($loc['address']) ? $loc['address'] : '';
			$type = isset($loc['type']) ? $loc['type'] : '';

			if (!$addr) continue;

			$time_suffix = '';
			if (stripos($type, 'Start') !== false && $start_time) {
				$time_suffix = ' at ' . $start_time;
			} elseif (stripos($type, 'End') !== false && $end_time) {
				$time_suffix = ' at ' . $end_time;
			}

			$label = $type ? '<strong>' . esc_html($type) . ':</strong> ' : '';
			?>
			<div class="trvlr-locations__item">
				<?php echo $label . esc_html($addr) . esc_html($time_suffix); ?>
			</div>
		<?php endforeach; ?>
	</div>
<?php
	return apply_filters('trvlr_locations', ob_get_clean(), $post_id);
}

function trvlr_additional_info($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$content = get_trvlr_additional_info($post_id);

	if (!$content) {
		return '';
	}

	$output = '<div class="trvlr-additional-info">' . $content . '</div>';
	return apply_filters('trvlr_additional_info', $output, $post_id);
}

function trvlr_accordion($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$inclusions = get_trvlr_inclusions($post_id);
	$locations = trvlr_locations($post_id);
	$additional_info = get_trvlr_additional_info($post_id);

	$accordion_items = [];

	if ($inclusions) {
		$accordion_items[] = [
			'title' => 'Inclusions',
			'content' => $inclusions,
		];
	}
	if ($locations) {
		$accordion_items[] = [
			'title' => 'Start & End Locations',
			'content' => $locations,
		];
	}
	if ($additional_info) {
		$accordion_items[] = [
			'title' => 'Additional Info',
			'content' => $additional_info,
		];
	}

	if (empty($accordion_items)) {
		return '';
	}

	ob_start();
?>
	<div class="trvlr-accordion">
		<?php foreach ($accordion_items as $item) : ?>
			<div class="trvlr-accordion__item">
				<button class="trvlr-accordion__trigger">
					<span class="trvlr-accordion__title"><?php echo esc_html($item['title']); ?></span>
					<div class="trvlr-accordion__icon">
						<svg class="trvlr-accordion__icon-open">
							<use href="#icon-plus"></use>
						</svg>
						<svg class="trvlr-accordion__icon-close">
							<use href="#icon-minus"></use>
						</svg>
					</div>
				</button>
				<div class="trvlr-accordion__content">
					<?php echo $item['content']; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php
	return apply_filters('trvlr_accordion', ob_get_clean(), $post_id);
}

function trvlr_advertised_price($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = get_trvlr_advertised_price_value($post_id);
	$type = get_trvlr_advertised_price_type($post_id);

	if (!$value) {
		return '';
	}

	ob_start();
?>
	<div class="trvlr-price">
		<span class="trvlr-price__value">from $<?php echo esc_html($value); ?></span>
		<?php if ($type) : ?>
			<span class="trvlr-price__type"><?php echo esc_html($type); ?></span>
		<?php endif; ?>
	</div>
<?php
	return apply_filters('trvlr_advertised_price', ob_get_clean(), $post_id);
}

function trvlr_booking_calendar($post_id = null, $args = array())
{
	$post_id = $post_id ?: get_the_ID();
	$base_iframe_url = get_trvlr_base_domain();

	$defaults = array(
		'width' => '450px',
		'height' => '600px',
		'attraction_id' => get_trvlr_id($post_id),
	);
	$args = wp_parse_args($args, $defaults);

	if (empty($base_iframe_url)) {
		return '<p>Please configure the trvlr organisation ID in the plugin settings.</p>';
	}

	if (empty($args['attraction_id'])) {
		return '<p>Sorry this attraction is not available for booking. No Trvlr AI ID found.</p>';
	}

	ob_start();
?>
	<div class="trvlr-booking-calendar">
		<iframe
			class="trvlr-booking-calendar__iframe"
			style="width: <?php echo esc_attr($args['width']); ?>; height: <?php echo esc_attr($args['height']); ?>;"
			frameborder="0"
			src="<?php echo esc_url($base_iframe_url); ?>/date-picker2/index.html?attr_id=<?php echo esc_attr($args['attraction_id']); ?>"
			title="Booking Calendar"></iframe>
	</div>
<?php
	return apply_filters('trvlr_booking_calendar', ob_get_clean(), $post_id, $args);
}

function trvlr_booking_button($post_id = null, $args = array())
{
	$post_id = $post_id ?: get_the_ID();

	$defaults = array(
		'attraction_id' => get_trvlr_attraction_id($post_id),
		'class' => '',
		'label' => 'Book Now',
	);
	$args = wp_parse_args($args, $defaults);

	if (is_trvlr_attraction($post_id)) {
		$args['attraction_id'] = get_trvlr_attraction_id($post_id);
	} else {
		return '<p>Sorry this attraction is not available for booking. No Trvlr AI ID found.</p>';
	}

	ob_start();
?>
	<button class="trvlr-book-now<?php echo esc_attr($args['class']); ?>" attraction-id="<?php echo esc_attr($args['attraction_id']); ?>">
		<span><?php echo esc_html($args['label']); ?></span>
	</button>
<?php
	return apply_filters('trvlr_booking_button', ob_get_clean(), $post_id, $args);
}

function trvlr_card($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();

	if (!$post_id || get_post_type($post_id) !== 'trvlr_attraction') {
		return '';
	}

	$trvlr_card_template_slug = Trvlr_Template_Registry::get_active_card_slug();
	$template_path = Trvlr_Template_Registry::get_card_template_path();

	ob_start();
	include $template_path;

	return apply_filters('trvlr_card', ob_get_clean(), $post_id);
}

/**
 * Build a WP_Query args array from the named shortcode/API parameters.
 *
 * Accepts the same parameter set as `trvlr_attraction_cards` shortcode plus a `query_args`
 * key for a full WP_Query override. Handles `ids` → `post__in` and `exclude` →
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
	);
	foreach ($custom_keys as $key) {
		unset($query_args[$key]);
	}

	return $query_args;
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
function trvlr_build_cards_result($query_args = array())
{
	$query = new WP_Query($query_args);

	ob_start();
	echo '<div class="trvlr-cards">';
	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			echo trvlr_card(get_the_ID());
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

/**
 * Output a grid of attraction cards from a WP_Query over `trvlr_attraction`.
 *
 * Usage:
 * - With an empty `$args` on a main archive/taxonomy screen, uses the main query (loop).
 * - Otherwise runs a dedicated query. Pass `query_args` as a full `WP_Query` argument array
 *   for a complete override (defaults `post_type` to `trvlr_attraction` if omitted).
 * - Or pass the parameters below; they are merged with defaults (`posts_per_page` 16, etc.).
 *
 * Taxonomy filters (combined with AND across groups; within a group use the matching `*_relation`):
 * - **WordPress tags** (`post_tag`): `tag` (comma-separated slugs), `tag_id` (comma-separated term IDs),
 *   `tag_slug` (comma-separated slugs), `tag_relation` (`AND`/`OR` when multiple of these dimensions apply).
 *   Synced/API “attraction type” terms live on `trvlr_attraction_tag` — filter those with `trvlr_tag`, `trvlr_tag_slug`, or `trvlr_tag_id`, not `tag` / `tag_slug`.
 * - **Categories** (`category`): `category` (comma-separated slugs), `category_id` (comma-separated term IDs),
 *   `category_slug` (comma-separated slugs), `category_relation`.
 * - **TRVLR attraction tags** (`trvlr_attraction_tag`): `trvlr_tag` (comma-separated names),
 *   `trvlr_tag_id` (comma-separated term IDs), `trvlr_tag_slug` (comma-separated slugs), `trvlr_tag_relation`.
 *
 * Other common `$args`: `posts_per_page`, `orderby`, `order`, `post__in` (via shortcode `ids`), `exclude` (post IDs
 * → `post__not_in`), `meta_key` / `meta_value` / `meta_compare`, `meta_query`, or a raw `tax_query` array (replaces
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

	if (empty($args) && (is_post_type_archive() || is_tax() || is_category() || is_tag())) {
		$use_main_query = true;
	}

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

		$initial_query = $args;
		unset($initial_query['grid_id']);

		$query_args = trvlr_build_query_args($args);
		$result     = trvlr_build_cards_result($query_args);

		$container_atts = array(
			'class'                     => 'trvlr-cards-container',
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
		'orderby'       => 'name',
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

function trvlr_render_booking_calendar($atts = array())
{
	return trvlr_booking_calendar(null, $atts);
}

function trvlr_get_attraction_card($post_id = null)
{
	return trvlr_card($post_id);
}

function trvlr_attraction_card($post_id = null)
{
	echo trvlr_card($post_id);
}

function trvlr_get_attraction_cards($args = array())
{
	return trvlr_cards($args);
}

function trvlr_attraction_cards($args = array())
{
	echo trvlr_cards($args);
}

function trvlr_get_attraction_gallery($post_id = null)
{
	return trvlr_gallery($post_id);
}

function trvlr_attraction_gallery($post_id = null)
{
	echo trvlr_gallery($post_id);
}

function trvlr_get_formatted_attraction_locations($post_id = null)
{
	return trvlr_locations($post_id);
}

function trvlr_get_attraction_accordion($post_id = null)
{
	return trvlr_accordion($post_id);
}

function trvlr_payment_confirmation_markup()
{
	$org_id = get_trvlr_organisation_id();

	if (empty($org_id)) {
		return '<p>' . esc_html__('Sorry. This site has not been connected to trvlr.ai properly...  Please contact support.', 'trvlr') . '</p>';
	}

	$base_domain = get_trvlr_base_domain($org_id);

	ob_start();
?>
	<div id="trvlr-payment-confirmation-container" class="trvlr-payment-wrapper">
		<iframe
			id="trvlr-payment-confirmation-iframe"
			src="<?php echo esc_url($base_domain . '/payment/confirmation.html'); ?>"
			title="<?php esc_attr_e('Payment Confirmation', 'trvlr'); ?>"
			frameborder="0"></iframe>
	</div>
	<script>
		(function() {
			console.log('Payment confirmation iframe loaded');
			window.addEventListener('message', function(event) {
				console.log('Payment confirmation message received:', event.data);

				if (event.data.type === 'REFRESH_PAGE') {
					console.log('Setting refresh page flag in localStorage');
					localStorage.setItem('isRefreshPage', 'true');

					setTimeout(function() {
						window.location.href = '<?php echo esc_url(home_url()); ?>';
					}, 2000);
				}
			});
		})();
	</script>
<?php
	return ob_get_clean();
}
