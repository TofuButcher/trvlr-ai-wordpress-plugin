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

function trvlr_sale_badge($post_id = null, $always_show = false)
{
	$post_id = $post_id ?: get_the_ID();
	$is_on_sale = get_trvlr_is_on_sale($post_id);
	if ($always_show) {
		$is_on_sale = true;
	}

	if (!$is_on_sale) {
		return '';
	}

	$output = '<div class="trvlr-sale__badge"><span>% Special Deal</span></div>';
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

	$permalink = get_permalink($post_id);
	$title = get_trvlr_title($post_id);
	$trvlr_id = get_trvlr_id($post_id);
	$duration = get_trvlr_duration($post_id);
	$start_time = get_trvlr_start_time($post_id);
	$is_on_sale = get_trvlr_is_on_sale($post_id);
	$advertised_price_value = get_trvlr_advertised_price_value($post_id);
	$advertised_price_type = get_trvlr_advertised_price_type($post_id);

	$time_string = '';
	if ($duration) {
		$time_string = $duration;
		if ($start_time) {
			$time_string .= ', starts ' . $start_time;
		}
	} elseif ($start_time) {
		$time_string = 'starts ' . $start_time;
	}

	ob_start();
?>
	<div class="trvlr-card trvlr-card--attraction">
		<div class="trvlr-card__image-wrap">
			<?php if (has_post_thumbnail($post_id)) : ?>
				<?php echo get_the_post_thumbnail($post_id, 'medium', array('class' => 'trvlr-card__image')); ?>
				<?php echo trvlr_popular_badge($post_id); ?>
			<?php endif; ?>
		</div>
		<div class="trvlr-card__content">
			<h3 class="trvlr-title trvlr-card__title">
				<a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
			</h3>
			<?php if ($time_string) : ?>
				<div class="trvlr-card__meta">
					<?php echo trvlr_duration($post_id); ?>
				</div>
			<?php endif; ?>
			<div class="trvlr-card__footer">
				<div class="trvlr-card__price">
					<?php if ($is_on_sale) : ?>
						<?php echo trvlr_sale_badge($post_id); ?>
					<?php endif; ?>
					<?php echo trvlr_advertised_price($post_id); ?>
				</div>
				<button class="trvlr-card__button trvlr-book-now" attraction-id="<?php echo esc_attr($trvlr_id); ?>">
					<span>Book Now</span>
					<svg>
						<use href="#icon-arrow-right"></use>
					</svg>
				</button>
			</div>
		</div>
	</div>
<?php
	return apply_filters('trvlr_card', ob_get_clean(), $post_id);
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
		if (isset($args['query_args']) && is_array($args['query_args'])) {
			$query_args = $args['query_args'];
			if (!isset($query_args['post_type'])) {
				$query_args['post_type'] = 'trvlr_attraction';
			}
		} else {
			$defaults = array(
				'post_type' => 'trvlr_attraction',
				'posts_per_page' => 16,
				'post_status' => 'publish',
			);

			$query_args = wp_parse_args($args, $defaults);

			$tax_groups = array();

			if (!empty($args['tag']) || !empty($args['tag_id']) || !empty($args['tag_slug'])) {
				$clauses = array();
				if (!empty($args['tag'])) {
					$terms = is_array($args['tag']) ? $args['tag'] : explode(',', $args['tag']);
					$terms = array_map('sanitize_text_field', array_map('trim', $terms));
					$terms = array_filter($terms);
					if (!empty($terms)) {
						$clauses[] = array(
							'taxonomy' => 'post_tag',
							'field' => 'slug',
							'terms' => $terms,
						);
					}
				}
				if (!empty($args['tag_id'])) {
					$terms = is_array($args['tag_id']) ? $args['tag_id'] : array_map('intval', explode(',', $args['tag_id']));
					$terms = array_filter($terms);
					if (!empty($terms)) {
						$clauses[] = array(
							'taxonomy' => 'post_tag',
							'field' => 'term_id',
							'terms' => $terms,
						);
					}
				}
				if (!empty($args['tag_slug'])) {
					$terms = is_array($args['tag_slug']) ? $args['tag_slug'] : explode(',', $args['tag_slug']);
					$terms = array_map('sanitize_text_field', array_map('trim', $terms));
					$terms = array_filter($terms);
					if (!empty($terms)) {
						$clauses[] = array(
							'taxonomy' => 'post_tag',
							'field' => 'slug',
							'terms' => $terms,
						);
					}
				}
				if (!empty($clauses)) {
					if (count($clauses) > 1) {
						$tax_groups[] = array_merge(
							array(
								'relation' => !empty($args['tag_relation']) ? strtoupper(sanitize_text_field($args['tag_relation'])) : 'AND',
							),
							$clauses
						);
					} else {
						$tax_groups[] = $clauses[0];
					}
				}
			}

			if (!empty($args['category']) || !empty($args['category_id']) || !empty($args['category_slug'])) {
				$clauses = array();
				if (!empty($args['category'])) {
					$terms = is_array($args['category']) ? $args['category'] : explode(',', $args['category']);
					$terms = array_map('sanitize_text_field', array_map('trim', $terms));
					$terms = array_filter($terms);
					if (!empty($terms)) {
						$clauses[] = array(
							'taxonomy' => 'category',
							'field' => 'slug',
							'terms' => $terms,
						);
					}
				}
				if (!empty($args['category_id'])) {
					$terms = is_array($args['category_id']) ? $args['category_id'] : array_map('intval', explode(',', $args['category_id']));
					$terms = array_filter($terms);
					if (!empty($terms)) {
						$clauses[] = array(
							'taxonomy' => 'category',
							'field' => 'term_id',
							'terms' => $terms,
						);
					}
				}
				if (!empty($args['category_slug'])) {
					$terms = is_array($args['category_slug']) ? $args['category_slug'] : explode(',', $args['category_slug']);
					$terms = array_map('sanitize_text_field', array_map('trim', $terms));
					$terms = array_filter($terms);
					if (!empty($terms)) {
						$clauses[] = array(
							'taxonomy' => 'category',
							'field' => 'slug',
							'terms' => $terms,
						);
					}
				}
				if (!empty($clauses)) {
					if (count($clauses) > 1) {
						$tax_groups[] = array_merge(
							array(
								'relation' => !empty($args['category_relation']) ? strtoupper(sanitize_text_field($args['category_relation'])) : 'AND',
							),
							$clauses
						);
					} else {
						$tax_groups[] = $clauses[0];
					}
				}
			}

			if (!empty($args['trvlr_tag']) || !empty($args['trvlr_tag_id']) || !empty($args['trvlr_tag_slug'])) {
				$clauses = array();
				if (!empty($args['trvlr_tag'])) {
					$terms = is_array($args['trvlr_tag']) ? $args['trvlr_tag'] : explode(',', $args['trvlr_tag']);
					$terms = array_map('sanitize_text_field', array_map('trim', $terms));
					$terms = array_filter($terms);
					if (!empty($terms)) {
						$clauses[] = array(
							'taxonomy' => 'trvlr_attraction_tag',
							'field' => 'name',
							'terms' => $terms,
						);
					}
				}
				if (!empty($args['trvlr_tag_id'])) {
					$terms = is_array($args['trvlr_tag_id']) ? $args['trvlr_tag_id'] : array_map('intval', explode(',', $args['trvlr_tag_id']));
					$terms = array_filter($terms);
					if (!empty($terms)) {
						$clauses[] = array(
							'taxonomy' => 'trvlr_attraction_tag',
							'field' => 'term_id',
							'terms' => $terms,
						);
					}
				}
				if (!empty($args['trvlr_tag_slug'])) {
					$terms = is_array($args['trvlr_tag_slug']) ? $args['trvlr_tag_slug'] : explode(',', $args['trvlr_tag_slug']);
					$terms = array_map('sanitize_text_field', array_map('trim', $terms));
					$terms = array_filter($terms);
					if (!empty($terms)) {
						$clauses[] = array(
							'taxonomy' => 'trvlr_attraction_tag',
							'field' => 'slug',
							'terms' => $terms,
						);
					}
				}
				if (!empty($clauses)) {
					if (count($clauses) > 1) {
						$tax_groups[] = array_merge(
							array(
								'relation' => !empty($args['trvlr_tag_relation']) ? strtoupper(sanitize_text_field($args['trvlr_tag_relation'])) : 'AND',
							),
							$clauses
						);
					} else {
						$tax_groups[] = $clauses[0];
					}
				}
			}

			if (!empty($tax_groups)) {
				if (count($tax_groups) > 1) {
					$query_args['tax_query'] = array_merge(
						array('relation' => 'AND'),
						$tax_groups
					);
				} else {
					$single = $tax_groups[0];
					$query_args['tax_query'] = isset($single['relation'])
						? $single
						: array($single);
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
				$query_args['post__not_in'] = is_array($args['exclude']) ? $args['exclude'] : array_map('intval', explode(',', $args['exclude']));
			}

			$custom_args = array(
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
			);
			foreach ($custom_args as $custom_arg) {
				unset($query_args[$custom_arg]);
			}
		}

		$query = new WP_Query($query_args);

		echo '<div class="trvlr-cards-container"><div class="trvlr-cards">';
		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				echo trvlr_card(get_the_ID());
			}
			wp_reset_postdata();
		} else {
			echo '<p>' . esc_html__('No posts found.', 'trvlr') . '</p>';
		}
		echo '</div></div>';
	}

	return apply_filters('trvlr_cards', ob_get_clean(), $args);
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
