<?php

/**
 * Template functions for displaying attraction components
 * 
 * @package Trvlr
 */

if (!defined('ABSPATH')) exit;


function trvlr_render_booking_calendar($atts = array())
{
	$base_iframe_url = get_trvlr_base_domain();

	$atts = shortcode_atts(array(
		'width' => '450px',
		'height' => '600px',
		'attraction_id' => get_trvlr_attraction_id(),
	), $atts);

	if (empty($base_iframe_url)) {
		return '<p>Please configure the trvlr organisation ID in the plugin settings.</p>';
	}

	if (empty($atts['attraction_id'])) {
		return '<p>Sorry this attraction is not available for booking. No Trvlr AI ID found.</p>';
	}

	ob_start();
?>
	<iframe id='trvlr-booking-calendar-iframe'
		style="width: <?php echo esc_attr($atts['width']); ?>; height: <?php echo esc_attr($atts['height']); ?>;"
		frameborder="0"
		src="<?php echo esc_url($base_iframe_url); ?>/date-picker2/index.html?attr_id=<?php echo esc_attr($atts['attraction_id']); ?>"
		title="Booking Calendar"
		class="iframe-cont"></iframe>
<?php
	return ob_get_clean();
}

/**
 * Display a single attraction card
 * 
 * @param int|null $post_id Optional post ID
 * @return string HTML output
 */
function trvlr_get_attraction_card($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();

	if (!$post_id || get_post_type($post_id) !== 'trvlr_attraction') {
		return '';
	}

	$permalink = get_permalink($post_id);
	$title = get_the_title($post_id);
	$trvlr_id = get_trvlr_attraction_id($post_id);
	$duration = get_trvlr_attraction_duration($post_id);
	$start_time = get_trvlr_attraction_start_time($post_id);
	$is_on_sale = get_trvlr_attraction_is_on_sale($post_id);
	$pricing = get_trvlr_attraction_pricing($post_id);

	$time_string = '';
	if ($duration) {
		$time_string = $duration;
		if ($start_time) {
			$time_string .= ', starts ' . $start_time;
		}
	} elseif ($start_time) {
		$time_string = 'starts ' . $start_time;
	}

	$advertised_price_value = get_trvlr_attraction_advertised_price_value($post_id);
	$advertised_price_type = get_trvlr_attraction_advertised_price_type($post_id);

	ob_start();
?>
	<div class="attraction-card">
		<div class="attraction-card__image-wrap">
			<?php if (has_post_thumbnail($post_id)) : ?>
				<?php echo get_the_post_thumbnail($post_id, 'medium', array('class' => 'attraction-card__image')); ?>
			<?php endif; ?>
			<div class="attraction-card__popular-badge">
				<svg>
					<use href="#icon-star"></use>
				</svg>
				Popular
			</div>
		</div>
		<div class="attraction-card__content">
			<h3 class="attraction-card__title">
				<a href="<?php echo esc_url($permalink); ?>">
					<?php echo esc_html($title); ?>
				</a>
			</h3>
			<div class="attraction-card__text-wrap">
				<?php if ($time_string) : ?>
					<div class="attraction-card__text-item">
						<svg>
							<use href="#icon-clock"></use>
						</svg>
						<span><?php echo esc_html($time_string); ?></span>
					</div>
				<?php endif; ?>
			</div>
			<div class="attraction-card__footer">
				<div class="attraction-card__price">
					<?php if ($is_on_sale) : ?>
						<div class="attraction-card__sale-badge">
							<span>% Special Deal</span>
						</div>
					<?php endif; ?>
					<?php if ($advertised_price_value) : ?>
						<span class="attraction-card__price-value">
							from $<?php echo esc_html($advertised_price_value); ?>
						</span>
						<?php if ($advertised_price_type) : ?>
							<span class="attraction-card__price-type">
								<?php echo esc_html($advertised_price_type); ?>
							</span>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<button class="attraction-card__book-now trvlr-book-now" attraction-id="<?php echo esc_attr($trvlr_id); ?>">
					<span>Book Now</span>
					<svg>
						<use href="#icon-arrow-right"></use>
					</svg>
				</button>
			</div>
		</div>
	</div>
<?php
	$output = ob_get_clean();
	return apply_filters('trvlr_attraction_card', $output, $post_id);
}

/**
 * Echo attraction card
 * 
 * @param int|null $post_id Optional post ID
 */
function trvlr_attraction_card($post_id = null)
{
	echo trvlr_get_attraction_card($post_id);
}

/**
 * Get multiple attraction cards based on query
 * 
 * @param array $args WP_Query arguments
 * @return string HTML output
 */
function trvlr_get_attraction_cards($args = array())
{
	// Check if we're on an attraction archive and should use main query
	$use_main_query = false;

	if (is_post_type_archive('trvlr_attraction') && in_the_loop()) {
		$use_main_query = true;
	}

	ob_start();

	if ($use_main_query) {
		// Use main query on archive pages
		echo '<div class="trvlr-attraction-cards">';
		if (have_posts()) {
			while (have_posts()) {
				the_post();
				trvlr_attraction_card(get_the_ID());
			}
		} else {
			echo '<p>' . esc_html__('No attractions found.', 'trvlr') . '</p>';
		}
		echo '</div>';
	} else {
		// Custom query
		$defaults = array(
			'post_type' => 'trvlr_attraction',
			'posts_per_page' => -1,
			'post_status' => 'publish',
		);

		$query_args = wp_parse_args($args, $defaults);
		$query = new WP_Query($query_args);

		echo '<div class="trvlr-attraction-cards">';
		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				trvlr_attraction_card(get_the_ID());
			}
			wp_reset_postdata();
		} else {
			echo '<p>' . esc_html__('No attractions found.', 'trvlr') . '</p>';
		}
		echo '</div>';
	}

	$output = ob_get_clean();
	return apply_filters('trvlr_attraction_cards', $output, $args);
}

/**
 * Echo multiple attraction cards
 * 
 * @param array $args WP_Query arguments
 */
function trvlr_attraction_cards($args = array())
{
	echo trvlr_get_attraction_cards($args);
}

/**
 * Get attraction gallery HTML (Featured + Additional Images)
 * 
 * @param int|null $post_id Optional post ID
 * @return string HTML output
 */
function trvlr_get_attraction_gallery($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();

	if (!$post_id) {
		return '';
	}

	$featured_image_id = get_post_thumbnail_id($post_id);
	$media_ids = get_trvlr_attraction_media($post_id);
	if (!is_array($media_ids)) {
		$media_ids = array();
	}

	$gallery_ids = array();
	if ($featured_image_id) {
		$gallery_ids[] = $featured_image_id;
	}

	foreach ($media_ids as $id) {
		if ($id != $featured_image_id) {
			$gallery_ids[] = $id;
		}
	}

	$gallery_ids = array_unique($gallery_ids);

	if (empty($gallery_ids)) {
		return '';
	}

	if (count($gallery_ids) === 1) {
		return '<div class="trvlr-attraction__media-single">' . wp_get_attachment_image($gallery_ids[0], 'large') . '</div>';
	}

	ob_start();
	$main_id = 'trvlr-main-slider-' . $post_id;
	$nav_id = 'trvlr-nav-slider-' . $post_id;
?>
	<div class="attraction-slider__wrap trvlr-gallery-wrapper">
		<!-- Main Slider -->
		<div id="<?php echo esc_attr($main_id); ?>" class="attraction-slider splide" role="region" aria-label="Gallery">
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

		<!-- Nav/Thumbnail Slider -->
		<div id="<?php echo esc_attr($nav_id); ?>" class="attraction-slider__controls splide" role="region" aria-label="Thumbnails">
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
	$output = ob_get_clean();
	return apply_filters('trvlr_attraction_gallery', $output, $post_id, $gallery_ids);
}

/**
 * Echo attraction gallery
 * 
 * @param int|null $post_id Optional post ID
 */
function trvlr_attraction_gallery($post_id = null)
{
	echo trvlr_get_attraction_gallery($post_id);
}


/**
 * Get formatted attraction locations
 * 
 * @param int|null $post_id Optional post ID
 * @return string HTML output
 */

function trvlr_get_formatted_attraction_locations($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$locations = get_trvlr_attraction_locations($post_id);
	$start_time = get_trvlr_attraction_start_time($post_id);
	$end_time = get_trvlr_attraction_end_time($post_id);

	$locations_content = '';
	if (is_array($locations)) {
		foreach ($locations as $loc) {
			$addr = isset($loc['address']) ? $loc['address'] : '';
			$type = isset($loc['type']) ? $loc['type'] : '';

			if ($addr) {
				$time_suffix = '';
				// Check for Start/End and append time if available
				if (stripos($type, 'Start') !== false && $start_time) {
					$time_suffix = ' at ' . $start_time;
				} elseif (stripos($type, 'End') !== false && $end_time) {
					$time_suffix = ' at ' . $end_time;
				}

				$label = $type ? '<strong>' . esc_html($type) . ':</strong> ' : '';
				$locations_content .= '<div class="trvlr-location-item">' . $label . esc_html($addr) . esc_html($time_suffix) . '</div>';
			}
		}
	}
	return $locations_content;
}


/**
 * Get attraction accordion HTML (Inclusions, Locations, Additional Info)
 * 
 * @param int|null $post_id Optional post ID
 * @return string HTML output
 */
function trvlr_get_attraction_accordion($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$inclusions = get_trvlr_attraction_inclusions($post_id);
	$locations = trvlr_get_formatted_attraction_locations($post_id);
	$additional_info = get_trvlr_attraction_additional_info($post_id);

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

	$single_attraction_accordion = '';
	if (!empty($accordion_items)) {
		$single_attraction_accordion = '<div class="trvlr-attraction__accordion simple-accordion">';
		foreach ($accordion_items as $accordion_item) {
			$single_attraction_accordion .= <<<HTML
			<div class="accordion__item">
				<div class="accordion__trigger">
					<span class="accordion__title">{$accordion_item['title']}</span>
					<div class="accordion__icon icon-toggle">
						<svg class="accordion__icon open-icon">
							<use href="#icon-plus"></use>
						</svg>
						<svg class="accordion__icon close-icon">
							<use href="#icon-minus"></use>
						</svg>
					</div>
				</div>
				<div class="accordion__content">
					{$accordion_item['content']}
				</div>
			</div>
HTML;
		}
		$single_attraction_accordion .= '</div>';
	}

	return $single_attraction_accordion;
}
