<?php

if (!defined('ABSPATH')) {
	exit;
}

$post_id = isset($post_id) ? absint($post_id) : get_the_ID();
$hero_image = wp_get_attachment_image(get_post_thumbnail_id($post_id), 'full');
$price_value = get_trvlr_advertised_price_value($post_id);
$duration_raw = get_trvlr_duration($post_id);
$duration_text = $duration_raw ? apply_filters('trvlr_duration', $duration_raw, $post_id) : '';
$sale_badge_out = trvlr_sale_badge($post_id, false, false);
$sale_description_out = get_trvlr_is_on_sale($post_id) ? trvlr_sale_description($post_id) : '';
$important_information_out = trvlr_important_information($post_id);
$description_out = trvlr_short_description($post_id)
	. trvlr_description($post_id)
	. apply_filters('the_content', get_post_field('post_content', $post_id));
$faqs_out = trvlr_section(array(
	'section' => 'faqs',
	'post_id' => $post_id,
	'content' => trvlr_faqs($post_id, array('layout' => 'list')),
));
$overview_out = $sale_description_out . $important_information_out . $description_out . $faqs_out;
$inclusions_out = trvlr_inclusions($post_id);
$highlights_out = trvlr_highlights($post_id);
$gallery_out = trvlr_gallery($post_id, array('type' => 'masonry'));
$additional_info_out = trvlr_locations($post_id) . trvlr_additional_info($post_id);
$attraction_id = get_trvlr_id($post_id);
$group_id = get_trvlr_group_id($post_id);

$tabs = array(
	array(
		'title' => __('Overview', 'trvlr'),
		'content' => $overview_out,
		'class' => 'overview',
	),
	array(
		'title' => __('Inclusions', 'trvlr'),
		'content' => $inclusions_out,
		'class' => 'inclusions',
	),
	array(
		'title' => __('Highlights', 'trvlr'),
		'content' => $highlights_out,
		'class' => 'highlights',
	),
	array(
		'title' => __('Gallery', 'trvlr'),
		'content' => $gallery_out,
		'class' => 'gallery',
	),
	array(
		'title' => __('Additional Info', 'trvlr'),
		'content' => $additional_info_out,
		'class' => 'additional',
	),
);

?>
<article
	id="attraction-<?php echo esc_attr((string) $post_id); ?>"
	class="trvlr-single-attraction trvlr-single-attraction--tabs-sidebar">
	<div class="trvlr-single-attraction__inner trvlr-sidebar-layout">
		<div class="trvlr-single-attraction__main trvlr-sidebar-main">
			<section class="trvlr-single-attraction__hero">
				<?php if ($hero_image) : ?>
					<div class="trvlr-single-attraction__hero-image">
						<?php echo $hero_image; ?>
					</div>
				<?php endif; ?>
				<div class="trvlr-single-attraction__hero-container">
					<?php echo trvlr_back_link(); ?>
					<div class="trvlr-single-attraction__hero-title-wrap">
						<?php echo trvlr_title($post_id, 1); ?>
					</div>
					<button
						type="button"
						class="trvlr-single-attraction__hero-cta trvlr-check-availability"
						attraction-id="<?php echo esc_attr($attraction_id); ?>"
						<?php if ($group_id) : ?>attraction-group-id="<?php echo esc_attr($group_id); ?>" <?php endif; ?>>
						<?php esc_html_e('Check availability', 'trvlr'); ?>
						<?php echo trvlr_icon('arrow-right', true, array('class' => 'trvlr-single-attraction__hero-cta-icon')); ?>
					</button>
				</div>
			</section>
			<div class="trvlr-single-attraction__content">
				<div class="trvlr-single-attraction__content-container">
					<div class="trvlr-single-attraction__intro">
						<?php if ($price_value) : ?>
							<div class="trvlr-single-attraction__price">
								<?php echo esc_html__('from', 'trvlr'); ?> A$<?php echo esc_html($price_value); ?>
							</div>
						<?php endif; ?>
						<?php if ($duration_text !== '') : ?>
							<div class="trvlr-single-attraction__duration"><?php echo esc_html($duration_text); ?></div>
						<?php endif; ?>
						<?php echo $sale_badge_out; ?>
					</div>
					<?php echo trvlr_generate_tabs($tabs, array('class' => 'trvlr-attraction-tabs')); ?>
				</div>
			</div>
		</div>
		<aside class="trvlr-single-attraction__sidebar trvlr-sidebar" aria-label="<?php esc_attr_e('Booking', 'trvlr'); ?>">
		<div class="trvlr-single-attraction__sidebar-inner trvlr-sidebar__container">
			<?php echo trvlr_booking_calendar($post_id); ?>
		</div>
		</aside>
	</div>
</article>
