<?php

if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/trvlr-cards-functions.php';
require_once __DIR__ . '/trvlr-filter-functions.php';

function trvlr_title($post_id = null, $level = 1)
{
	$post_id = $post_id ?: get_the_ID();
	$title = get_trvlr_title($post_id);
	$tag = 'h' . absint($level);

	$output = "<{$tag} class=\"trvlr-title trvlr-heading\">" . esc_html($title) . "</{$tag}>";
	return apply_filters('trvlr_title', $output, $post_id, $level);
}

function trvlr_icon_allowed_html()
{
	return array(
		'svg' => array(
			'aria-hidden' => true,
			'class' => true,
			'fill' => true,
			'focusable' => true,
			'height' => true,
			'role' => true,
			'stroke' => true,
			'stroke-linecap' => true,
			'stroke-linejoin' => true,
			'stroke-width' => true,
			'viewBox' => true,
			'viewbox' => true,
			'width' => true,
			'xmlns' => true,
		),
		'path' => array(
			'd' => true,
			'fill' => true,
			'fill-rule' => true,
			'stroke' => true,
			'stroke-linecap' => true,
			'stroke-linejoin' => true,
			'stroke-width' => true,
		),
		'circle' => array(
			'cx' => true,
			'cy' => true,
			'r' => true,
			'fill' => true,
			'stroke' => true,
			'stroke-width' => true,
		),
		'line' => array(
			'x1' => true,
			'x2' => true,
			'y1' => true,
			'y2' => true,
			'stroke' => true,
			'stroke-linecap' => true,
			'stroke-width' => true,
		),
		'g' => array(
			'clip-path' => true,
			'fill' => true,
			'stroke' => true,
		),
		'use' => array(
			'href' => true,
			'xlink:href' => true,
		),
		'span' => array(
			'aria-hidden' => true,
			'class' => true,
		),
	);
}

function trvlr_icon_element_kses($html)
{
	return wp_kses($html, trvlr_icon_allowed_html());
}

function trvlr_kses_post_with_icons($html)
{
	$allowed = wp_kses_allowed_html('post');
	foreach (trvlr_icon_allowed_html() as $tag => $attrs) {
		$allowed[$tag] = isset($allowed[$tag])
			? array_merge($allowed[$tag], $attrs)
			: $attrs;
	}
	return wp_kses($html, $allowed);
}

function trvlr_back_link()
{
	ob_start();
	?>
	<a class="trvlr-back-link" href="<?php echo esc_url(home_url('/')); ?>">
		<?php echo trvlr_icon('arrow-left', true, array('class' => 'trvlr-back-link__icon')); ?>
		<?php esc_html_e('Back', 'trvlr'); ?>
	</a>
<?php
	return apply_filters('trvlr_back_link', ob_get_clean());
}

function trvlr_duration($post_id = null, $args = array())
{
	$post_id = $post_id ?: get_the_ID();
	$args = wp_parse_args(
		$args,
		array(
			'icon' => true,
			'icon_element' => '',
		)
	);
	$duration = get_trvlr_duration($post_id);

	if (!$duration || $duration === '0' || $duration === '0-0-0') {
		return '';
	}

	$icon_html = '';
	if ($args['icon_element'] !== '') {
		$icon_html = trvlr_icon_element_kses($args['icon_element']);
	} else {
		$icon_html = trvlr_component_icon($args['icon'], 'clock', 'trvlr-duration__icon');
	}

	ob_start();
?>
	<div class="trvlr-duration <?php echo $icon_html ? 'trvlr-icon-text' : ''; ?>">
		<?php echo $icon_html; ?>
		<span class="trvlr-duration__value"><?php echo esc_html($duration); ?></span>
	</div>
<?php
	return apply_filters('trvlr_duration', ob_get_clean(), $post_id, $args);
}

function trvlr_suitable_ages($post_id = null, $args = array())
{
	$post_id = $post_id ?: get_the_ID();
	$args = wp_parse_args(
		$args,
		array(
			'icon' => true,
			'icon_element' => '',
		)
	);

	$label = get_post_meta($post_id, 'trvlr_suitable_ages', true);
	if (!$label || $label === '') {
		return '';
	}

	$icon_html = '';
	if ($args['icon_element'] !== '') {
		$icon_html = trvlr_icon_element_kses($args['icon_element']);
	} else {
		$icon_html = trvlr_component_icon($args['icon'], 'user', 'trvlr-suitable-ages__icon');
	}

	ob_start();
?>
	<div class="trvlr-suitable-ages <?php echo $icon_html ? 'trvlr-icon-text' : ''; ?>">
		<?php echo $icon_html; ?>
		<span class="trvlr-suitable-ages__value"><?php echo esc_html($label); ?></span>
	</div>
<?php
	return apply_filters('trvlr_suitable_ages', ob_get_clean(), $post_id, $args);
}

function trvlr_simple_location($post_id = null, $args = array())
{
	$post_id = $post_id ?: get_the_ID();
	$args = wp_parse_args(
		$args,
		array(
			'icon' => true,
			'icon_element' => '',
		)
	);

	$label = get_post_meta($post_id, 'trvlr_simple_location', true);
	if (!$label || $label === '') {
		return '';
	}

	$icon_html = '';
	if ($args['icon_element'] !== '') {
		$icon_html = trvlr_icon_element_kses($args['icon_element']);
	} else {
		$icon_html = trvlr_component_icon($args['icon'], 'map-pin', 'trvlr-simple-location__icon');
	}

	ob_start();
?>
	<div class="trvlr-simple-location <?php echo $icon_html ? 'trvlr-icon-text' : ''; ?>">
		<?php echo $icon_html; ?>
		<span class="trvlr-simple-location__value"><?php echo esc_html($label); ?></span>
	</div>
<?php
	return apply_filters('trvlr_simple_location', ob_get_clean(), $post_id, $args);
}

function trvlr_cancellation_policy($post_id = null, $args = array())
{
	$post_id = $post_id ?: get_the_ID();
	$args = wp_parse_args(
		$args,
		array(
			'icon' => true,
			'icon_element' => '',
		)
	);

	$label = get_post_meta($post_id, 'trvlr_cancellation_policy', true);
	if (!$label || $label === '') {
		return '';
	}

	$icon_html = '';
	if ($args['icon_element'] !== '') {
		$icon_html = trvlr_icon_element_kses($args['icon_element']);
	} else {
		$icon_html = trvlr_component_icon($args['icon'], 'circle-x', 'trvlr-cancellation-policy__icon');
	}

	ob_start();
?>
	<div class="trvlr-cancellation-policy <?php echo $icon_html ? 'trvlr-icon-text' : ''; ?>">
		<?php echo $icon_html; ?>
		<span class="trvlr-cancellation-policy__value"><?php echo esc_html($label); ?></span>
	</div>
<?php
	return apply_filters('trvlr_cancellation_policy', ob_get_clean(), $post_id, $args);
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

function trvlr_term_has_popular($terms)
{
	$popular_term = apply_filters('trvlr_popular_term', 'popular');
	if (is_array($terms)) {
		foreach ($terms as $term) {
			if (is_object($term) && isset($term->slug) && $term->slug === $popular_term) {
				return true;
			} else if (is_string($term) && $term === $popular_term || $term === ucfirst($popular_term)) {
				return true;
			}
		}
	}
	return false;
}

function trvlr_popular_badge($post_id = null, $args = array())
{
	$post_id = $post_id ?: get_the_ID();
	$args = wp_parse_args(
		$args,
		array(
			'icon' => true,
		)
	);
	$attraction_tags = get_trvlr_attraction_tags($post_id);
	$categories = get_the_terms($post_id, 'category');
	$post_tags = get_the_terms($post_id, 'post_tag');

	$is_popular = trvlr_term_has_popular($attraction_tags) || trvlr_term_has_popular($categories) || trvlr_term_has_popular($post_tags);

	$badge_text = apply_filters('trvlr_badge_text', 'Popular', $post_id);

	if ($is_popular) {
		$icon = $args['icon'] ? trvlr_icon('star', true, array('class' => 'trvlr-popular-badge__icon trvlr-badge__icon')) : '';

		return '<div class="trvlr-popular-badge trvlr-badge">' . $icon . '<span class="trvlr-popular-badge__text">' . $badge_text . '</span></div>';
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

	$output = '<div class="trvlr-sale__badge trvlr-badge"><span>';

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

function trvlr_enqueue_gallery_slider_assets()
{
	wp_enqueue_style('trvlr-gallery-slider');
	trvlr_enqueue_front_script('trvlr-gallery-slider');
}

function trvlr_enqueue_gallery_masonry_assets()
{
	wp_enqueue_style('trvlr-gallery-masonry');
	trvlr_enqueue_front_script('trvlr-gallery-masonry');
}

function trvlr_gallery_attachment_lightbox_attrs($attachment_id)
{
	$full = wp_get_attachment_image_src($attachment_id, 'full');
	if (!$full) {
		return array(
			'href'   => '',
			'width'  => 0,
			'height' => 0,
		);
	}

	$width  = (int) $full[1];
	$height = (int) $full[2];

	if ($width <= 0 || $height <= 0) {
		$meta = wp_get_attachment_metadata($attachment_id);
		if (is_array($meta)) {
			$width  = isset($meta['width']) ? (int) $meta['width'] : 0;
			$height = isset($meta['height']) ? (int) $meta['height'] : 0;
		}
	}

	return array(
		'href'   => $full[0],
		'width'  => $width,
		'height' => $height,
	);
}

function trvlr_gallery($post_id = null, $args = array())
{
	$post_id = $post_id ?: get_the_ID();
	$args = wp_parse_args(
		$args,
		array(
			'type' => 'slider',
			'layout' => 'nav-bottom',
		)
	);
	$type = $args['type'] === 'masonry' ? 'masonry' : 'slider';

	$layouts = array(
		'nav-bottom' => 'nav-bottom',
		'bottom' => 'nav-bottom',
		'nav-right' => 'nav-right',
		'right' => 'nav-right',
		'nav-right-2col' => 'nav-right-2col',
		'right-2col' => 'nav-right-2col',
		'right-2-col' => 'nav-right-2col',
	);
	$layout_key = sanitize_key((string) $args['layout']);
	$layout = isset($layouts[$layout_key]) ? $layouts[$layout_key] : 'nav-bottom';

	$media_ids = get_trvlr_media($post_id, true);
	$gallery_ids = array_unique(array_filter($media_ids));

	if (empty($gallery_ids)) {
		return '';
	}

	if (count($gallery_ids) === 1) {
		$output = '<div class="trvlr-gallery trvlr-gallery--single">' . wp_get_attachment_image($gallery_ids[0], 'large') . '</div>';
		return apply_filters('trvlr_gallery', $output, $post_id, $gallery_ids, $type, $layout);
	}

	if ($type === 'masonry') {
		trvlr_enqueue_gallery_masonry_assets();

		ob_start();
		$gallery_id = 'trvlr-gallery-masonry-' . $post_id . '-' . wp_unique_id();
	?>
		<div id="<?php echo esc_attr($gallery_id); ?>" class="trvlr-gallery trvlr-gallery--masonry">
			<div class="trvlr-gallery__grid">
				<div class="trvlr-gallery__sizer" aria-hidden="true"></div>
				<?php foreach ($gallery_ids as $image_id) :
					$lightbox = trvlr_gallery_attachment_lightbox_attrs($image_id);
					if ($lightbox['href'] === '') {
						continue;
					}
				?>
					<div class="trvlr-gallery__item">
						<a
							class="trvlr-gallery__link"
							href="<?php echo esc_url($lightbox['href']); ?>"
							data-pswp-width="<?php echo esc_attr((string) $lightbox['width']); ?>"
							data-pswp-height="<?php echo esc_attr((string) $lightbox['height']); ?>">
							<?php echo wp_get_attachment_image($image_id, 'large', false, array('loading' => 'eager')); ?>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php
		return apply_filters('trvlr_gallery', ob_get_clean(), $post_id, $gallery_ids, $type, $layout);
	}

	trvlr_enqueue_gallery_slider_assets();

	ob_start();
	$uid = wp_unique_id('trvlr-gallery-');
	$main_id = $uid . '-main';
	$nav_id = $uid . '-nav';
	$layout_class = 'trvlr-gallery--' . $layout;
	?>
	<div class="trvlr-container-sizer">
		<div
			class="trvlr-gallery trvlr-gallery--slider <?php echo esc_attr($layout_class); ?>"
			data-trvlr-gallery-layout="<?php echo esc_attr($layout); ?>">
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
	</div>
<?php
	return apply_filters('trvlr_gallery', ob_get_clean(), $post_id, $gallery_ids, $type, $layout);
}

function trvlr_short_description($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$content = get_trvlr_short_description($post_id);

	if (!$content) {
		return '';
	}

	if ($content !== '' && $content !== null) {
		$has_html = preg_match('/<(?:p|br|div|span|ul|ol|li|h[1-6]|strong|em|a|img|table|blockquote)\b/i', $content);
		if (!$has_html) {
			$content = wpautop($content);
		}
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

	if ($content !== '' && $content !== null) {
		$has_html = preg_match('/<(?:p|br|div|span|ul|ol|li|h[1-6]|strong|em|a|img|table|blockquote)\b/i', $content);
		if (!$has_html) {
			$content = wpautop($content);
		}
	}
			
	$output = '<div class="trvlr-description">' . $content . '</div>';
	return apply_filters('trvlr_description', $output, $post_id);
}

function trvlr_important_information($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$content = get_trvlr_important_information($post_id);

	if (!$content) {
		return '';
	}

	$output = '<div class="trvlr-important-information">' . esc_html($content) . '</div>';
	return apply_filters('trvlr_important_information', $output, $post_id);
}

function trvlr_highlights($post_id = null, $class = '') {
	$post_id = $post_id ?: get_the_ID();
	$content = get_post_meta($post_id, 'trvlr_highlights', true);

	if (!$content) {
		return '';
	}

	$output = '<div class="trvlr-highlights ' . esc_attr($class) . '">' . $content . '</div>';
	return apply_filters('trvlr_highlights', $output, $post_id, $class);
}

function trvlr_inclusions($post_id = null, $class = '')
{
	$post_id = $post_id ?: get_the_ID();
	$content = get_trvlr_inclusions($post_id);

	if (!$content || empty($content) || $content === '[]' || $content === '<ul></ul>') {
		return '';
	}

	$output = '<div class="trvlr-inclusions ' . esc_attr($class) . '">' . $content . '</div>';
	return apply_filters('trvlr_inclusions', $output, $post_id, $class);
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

/**
 * @param array<int, array{title?: string, content?: string}> $items
 * @param array{class?: string} $args
 */
function trvlr_generate_accordion($items, $args = array())
{
	if (empty($items) || !is_array($items)) {
		return '';
	}

	$args = wp_parse_args($args, array(
		'class' => 'trvlr-accordion',
	));

	$normalized = array();
	foreach ($items as $item) {
		if (!is_array($item)) {
			continue;
		}
		$title = isset($item['title']) ? trim((string) $item['title']) : '';
		$content = isset($item['content']) ? (string) $item['content'] : '';
		if ($title === '' || $content === '') {
			continue;
		}
		$normalized[] = array(
			'title' => $title,
			'content' => $content,
		);
	}

	if (empty($normalized)) {
		return '';
	}

	ob_start();
	?>
	<div class="<?php echo esc_attr($args['class']); ?>">
		<?php foreach ($normalized as $item) : ?>
			<div class="trvlr-accordion__item">
				<button type="button" class="trvlr-accordion__trigger">
					<span class="trvlr-accordion__title"><?php echo esc_html($item['title']); ?></span>
					<div class="trvlr-accordion__icon">
						<?php echo trvlr_icon('plus', true, array('class' => 'trvlr-accordion__icon-open')); ?>
						<?php echo trvlr_icon('minus', true, array('class' => 'trvlr-accordion__icon-close')); ?>
					</div>
				</button>
				<div class="trvlr-accordion__content">
					<div class="trvlr-accordion__content-inner">
						<?php echo $item['content']; ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
	return apply_filters('trvlr_generate_accordion', ob_get_clean(), $normalized, $args);
}

function trvlr_generate_tabs($items, $args = array())
{
	if (empty($items) || !is_array($items)) {
		return '';
	}

	$args = wp_parse_args($args, array(
		'class' => '',
		'active_index' => 0,
	));

	$normalized = array();
	foreach ($items as $item) {
		if (!is_array($item)) {
			continue;
		}

		$title = isset($item['title']) ? trim((string) $item['title']) : '';
		$content = isset($item['content']) ? (string) $item['content'] : '';
		if ($title === '' || trim($content) === '') {
			continue;
		}

		$normalized[] = array(
			'title' => $title,
			'content' => $content,
			'class' => isset($item['class']) ? sanitize_html_class((string) $item['class']) : '',
		);
	}

	if (empty($normalized)) {
		return '';
	}

	$active_index = max(0, min((int) $args['active_index'], count($normalized) - 1));
	$base_id = wp_unique_id('trvlr-tabs-');
	$classes = trim('trvlr-tabs ' . (string) $args['class']);

	ob_start();
	?>
	<div class="<?php echo esc_attr($classes); ?>" data-trvlr-tabs>
		<div class="trvlr-tabs__nav" role="tablist">
			<?php foreach ($normalized as $index => $item) :
				$tab_id = $base_id . '-tab-' . $index;
				$panel_id = $base_id . '-panel-' . $index;
				$is_active = $index === $active_index;
			?>
				<button
					type="button"
					id="<?php echo esc_attr($tab_id); ?>"
					class="trvlr-tabs__tab<?php echo $is_active ? ' is-active' : ''; ?>"
					role="tab"
					aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
					aria-controls="<?php echo esc_attr($panel_id); ?>"
					tabindex="<?php echo $is_active ? '0' : '-1'; ?>">
					<?php echo esc_html($item['title']); ?>
				</button>
			<?php endforeach; ?>
		</div>
		<div class="trvlr-tabs__panels">
			<?php foreach ($normalized as $index => $item) :
				$tab_id = $base_id . '-tab-' . $index;
				$panel_id = $base_id . '-panel-' . $index;
				$is_active = $index === $active_index;
				$panel_classes = trim('trvlr-tabs__panel ' . ($item['class'] !== '' ? 'trvlr-tabs__panel--' . $item['class'] : ''));
			?>
				<div
					id="<?php echo esc_attr($panel_id); ?>"
					class="<?php echo esc_attr($panel_classes); ?>"
					role="tabpanel"
					aria-labelledby="<?php echo esc_attr($tab_id); ?>"
					tabindex="0"
					<?php echo $is_active ? '' : 'hidden'; ?>>
					<?php echo $item['content']; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php

	return apply_filters('trvlr_generate_tabs', ob_get_clean(), $normalized, $args);
}

function trvlr_accordion($post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$inclusions = trvlr_inclusions($post_id);
	$locations = trvlr_locations($post_id);
	$additional_info = trvlr_additional_info($post_id);

	$accordion_items = array();

	if ($inclusions) {
		$accordion_items[] = array(
			'title' => __('Inclusions', 'trvlr'),
			'content' => $inclusions,
		);
	}
	if ($locations) {
		$accordion_items[] = array(
			'title' => __('Start & End Locations', 'trvlr'),
			'content' => $locations,
		);
	}
	if ($additional_info) {
		$accordion_items[] = array(
			'title' => __('Additional Info', 'trvlr'),
			'content' => $additional_info,
		);
	}

	return apply_filters('trvlr_accordion', trvlr_generate_accordion($accordion_items), $post_id);
}

function trvlr_faqs($post_id = null, $args = array())
{
	$post_id = $post_id ?: get_the_ID();
	$args = wp_parse_args($args, array(
		'layout' => 'accordion',
	));
	$faqs = get_trvlr_faqs($post_id);

	if (empty($faqs) || !is_array($faqs)) {
		return '';
	}

	$items = array();
	foreach ($faqs as $faq) {
		if (!is_array($faq)) {
			continue;
		}
		$question = isset($faq['question']) ? trim((string) $faq['question']) : '';
		$answer = isset($faq['answer']) ? (string) $faq['answer'] : '';
		if ($question === '' || trim($answer) === '') {
			continue;
		}
		$items[] = array(
			'title' => $question,
			'content' => $answer,
		);
	}

	if (empty($items)) {
		return '';
	}

	$layout = sanitize_key((string) $args['layout']);
	if ($layout === 'list' || $layout === 'expanded') {
		ob_start();
		?>
		<div class="trvlr-faqs trvlr-faqs--list">
			<?php foreach ($items as $item) : ?>
				<div class="trvlr-faqs__item">
					<div class="trvlr-faqs__question"><?php echo esc_html($item['title']); ?></div>
					<div class="trvlr-faqs__answer"><?php echo wp_kses_post($item['content']); ?></div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
		return apply_filters('trvlr_faqs_markup', ob_get_clean(), $post_id, $args, $items);
	}

	return apply_filters(
		'trvlr_faqs_markup',
		trvlr_generate_accordion($items),
		$post_id,
		$args,
		$items
	);
}

function trvlr_section($args = array())
{
	$args = wp_parse_args($args, array(
		'section' => '',
		'content' => null,
		'post_id' => null,
		'class' => '',
		'id' => '',
		'title' => null,
		'title_tag' => 'h2',
	));

	$post_id = $args['post_id'] ?: get_the_ID();
	$section = (string) $args['section'];

	$resolvers = array(
		'short_description' => 'trvlr_short_description',
		'important_information' => 'trvlr_important_information',
		'description' => 'trvlr_description',
		'inclusions' => 'trvlr_inclusions',
		'highlights' => 'trvlr_highlights',
		'additional_info' => 'trvlr_additional_info',
		'faqs' => 'trvlr_faqs',
	);

	$content = $args['content'];
	if ($content === null) {
		if ($section === '' || !isset($resolvers[$section])) {
			return '';
		}
		$content = call_user_func($resolvers[$section], $post_id);
	}

	if (trim((string) $content) === '') {
		return '';
	}

	if ($args['title'] === null) {
		$title = $section !== '' ? get_trvlr_section_heading($section, $post_id) : '';
	} else {
		$title = (string) $args['title'];
	}

	$title_tag = in_array($args['title_tag'], array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'), true)
		? $args['title_tag']
		: 'h2';

	$classes = array_filter(array(
		'trvlr-section',
		$section !== '' ? 'trvlr-section--' . sanitize_html_class($section) : '',
		$args['class'],
	));

	$id_attr = $args['id'] !== '' ? ' id="' . esc_attr($args['id']) . '"' : '';

	$before_action = 'trvlr_before_section_' . $section;
	$after_action = 'trvlr_after_section_' . $section;

	ob_start();
	do_action($before_action, $args['post_id']);
	?>
	<section class="<?php echo esc_attr(implode(' ', $classes)); ?>"<?php echo $id_attr; ?>>
		<?php if ($title !== '') : ?>
			<<?php echo esc_attr($title_tag); ?> class="trvlr-heading trvlr-section__title"><?php echo esc_html($title); ?></<?php echo esc_attr($title_tag); ?>>
		<?php endif; ?>
		<?php echo $content; ?>
	</section>
	<?php
	do_action($after_action, $args['post_id']);
	
	return apply_filters('trvlr_section', ob_get_clean(), $args);
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
		'height' => '520px',
		'attraction_id' => get_trvlr_id($post_id),
		'group_id' => get_trvlr_group_id($post_id),
	);
	$args = wp_parse_args($args, $defaults);

	if (empty($base_iframe_url)) {
		return '<p>Please configure the trvlr organisation ID in the plugin settings.</p>';
	}

	if (empty($args['attraction_id'])) {
		return '<p>Sorry this attraction is not available for booking. No Traveloris ID found.</p>';
	}
	$calendar_query = !empty($args['group_id'])
		? 'group_id=' . esc_attr($args['group_id'])
		: 'attr_id=' . esc_attr($args['attraction_id']);

	ob_start();
?>
	<div class="trvlr-booking-calendar">
		<iframe
			class="trvlr-booking-calendar__iframe"
			style="width: <?php echo esc_attr($args['width']); ?>; height: <?php echo esc_attr($args['height']); ?>;"
			frameborder="0"
			src="<?php echo esc_url($base_iframe_url); ?>/date-picker2/index.html?<?php echo $calendar_query; ?>"
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
		'icon' => '',
		'icon_position' => 'right',
		'icon_class' => '',
	);
	$args = wp_parse_args($args, $defaults);

	if (is_trvlr_attraction($post_id)) {
		$args['attraction_id'] = get_trvlr_attraction_id($post_id);
	} else {
		return '<p>Sorry this attraction is not available for booking.</p>';
	}

	$group_id = get_post_meta($post_id, 'trvlr_group_id', true);

	$icon = $args['icon'] !== '' ? trvlr_icon($args['icon'], true, array('class' => 'trvlr-booking-button__icon ' . $args['icon_class'])) : '';
	$icon_left = $args['icon_position'] === 'left' ? $icon : '';
	$icon_right = $args['icon_position'] !== 'left' ? $icon : '';
	ob_start();
?>
	<button
		class="trvlr-book-now trvlr-booking-button<?php echo esc_attr($args['class']); ?>"
		attraction-id="<?php echo esc_attr($args['attraction_id']); ?>"
		<?php if ($group_id) : ?>attraction-group-id="<?php echo esc_attr($group_id); ?>"<?php endif; ?>
	>
	<?php $icon_left; ?>
		<span><?php echo esc_html($args['label']); ?></span>
	<?php echo $icon_right; ?>
	</button>
<?php
	return apply_filters('trvlr_booking_button', ob_get_clean(), $post_id, $args);
}

function trvlr_card($post_id = null, $variant = 'default')
{
	$post_id = $post_id ?: get_the_ID();

	if (!$post_id || get_post_type($post_id) !== 'trvlr_attraction') {
		return '';
	}

	$trvlr_card_template_slug = Trvlr_Template_Registry::get_active_card_slug();
	$template_path = Trvlr_Template_Registry::get_card_template_path();
	$trvlr_card_variant = (is_string($variant) && $variant !== '') ? $variant : 'default';

	ob_start();
	include $template_path;

	return apply_filters('trvlr_card', ob_get_clean(), $post_id);
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

function trvlr_single_attraction_markup($post_id = null, $template_slug = null)
{
	if (!class_exists('Trvlr_Template_Registry')) {
		return '';
	}

	$post_id = $post_id ? absint($post_id) : get_the_ID();
	if (!$post_id) {
		return '';
	}

	$post = get_post($post_id);
	if (!$post || $post->post_type !== 'trvlr_attraction') {
		return '';
	}

	$registry_slug = null;
	if ($template_slug !== null && $template_slug !== '') {
		$try = sanitize_key((string) $template_slug);
		$singles = Trvlr_Template_Registry::get_single_templates();
		if ($try !== '' && isset($singles[$try])) {
			$registry_slug = $try;
		}
	}

	$trvlr_single_template_slug = $registry_slug !== null
		? $registry_slug
		: Trvlr_Template_Registry::get_active_single_slug();

	$single_template_path = Trvlr_Template_Registry::get_single_template_path($registry_slug);
	if ($single_template_path === '' || !is_readable($single_template_path)) {
		return '';
	}

	global $post;
	$original_post = $post;
	$post = get_post($post_id);
	setup_postdata($post);

	$attraction_id = get_trvlr_attraction_id($post_id);
	$post_classes = get_post_class('single-attraction', $post_id);
	$post_class = implode(' ', $post_classes);

	ob_start();
	include $single_template_path;
	$html = ob_get_clean();

	$post = $original_post;
	if ($post) {
		setup_postdata($post);
	} else {
		wp_reset_postdata();
	}

	return apply_filters('trvlr_single_attraction_markup', $html, $post_id, $trvlr_single_template_slug);
}

function trvlr_payment_confirmation_markup()
{
	$org_id = get_trvlr_organisation_id();

	if (empty($org_id)) {
		return '<p>' . esc_html__('Sorry. This site has not been connected to Traveloris properly...  Please contact support.', 'trvlr') . '</p>';
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
