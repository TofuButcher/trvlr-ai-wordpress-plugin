<?php

if (!defined('ABSPATH')) {
	exit;
}

$post_id = isset($post_id) ? absint($post_id) : get_the_ID();
$hero_image_id = get_post_thumbnail_id($post_id);
$hero_image = wp_get_attachment_image($hero_image_id, 'full');
$gallery_out = trvlr_gallery($post_id, array('type' => 'slider', 'layout' => 'nav-right-2col'));
$has_gallery = $gallery_out !== '';
$additional_info_content = apply_filters('the_content', get_post_field('post_content', $post_id))
	. trvlr_locations($post_id)
	. trvlr_additional_info($post_id);
$price_value = get_trvlr_advertised_price_value($post_id);
$attraction_id = get_trvlr_id($post_id);
$group_id = get_trvlr_group_id($post_id);

?>
<article
	id="attraction-<?php echo esc_attr((string) $post_id); ?>"
	class="trvlr-single-attraction">
	<div class="trvlr-single-attraction__inner trvlr-sidebar-layout">
		<div class="trvlr-single-attraction__main trvlr-sidebar-main">
			<section class="trvlr-single-attraction__hero trvlr-hero trvlr-hero--cover">
				<?php if ($hero_image) : ?>
					<div class="trvlr-single-attraction__hero-image trvlr-hero__image">
						<?php echo $hero_image; ?>
					</div>
				<?php endif; ?>
				<div class="trvlr-single-attraction__hero-inner-wrap">
					<div class="trvlr-single-attraction__hero-inner trvlr-hero__inner trvlr-container">
						<?php echo trvlr_back_link(); ?>
						<h1 class="trvlr-title"><?php echo esc_html(get_trvlr_title($post_id)); ?></h1>
					</div>
				</div>
				<div class="trvlr-single-attraction__summary-outer">
					<div class="trvlr-single-attraction__summary-container trvlr-summary-wrap trvlr-container">
						<div class="trvlr-single-attraction__summary trvlr-summary">
							<?php if ($price_value) : ?>
								<div class="trvlr-single-attraction__summary-item trvlr-summary-item trvlr-icon-text">
									<?php echo trvlr_icon('dollar-sign', true, array('class' => 'trvlr-single-attraction__summary-icon trvlr-summary-icon')); ?>
									<span><?php echo esc_html__('from', 'trvlr'); ?> A$<?php echo esc_html($price_value); ?></span>
								</div>
							<?php endif; ?>
							<?php echo trvlr_duration($post_id); ?>
							<?php echo trvlr_simple_location($post_id); ?>
							<?php echo trvlr_suitable_ages($post_id); ?>
							<?php echo trvlr_cancellation_policy($post_id); ?>
						</div>
					</div>
				</div>
			</section>
			<div class="trvlr-single-attraction__intro">
				<?php
				echo trvlr_section(array(
					'section' => 'important_information',
					'post_id' => $post_id,
					'title' => '',
				));
				echo trvlr_section(array(
					'section' => 'short_description',
					'post_id' => $post_id,
					'title' => '',
				));
				?>
			</div>
			<div class="trvlr-single-attraction__content">
				<?php if ($has_gallery) : ?>
					<section class="trvlr-single-attraction__gallery trvlr-section">
						<div class="trvlr-container">
							<?php echo $gallery_out; ?>
						</div>
					</section>
				<?php endif; ?>
				<?php
				echo trvlr_section(array(
					'section' => 'highlights',
					'post_id' => $post_id,
				));
				echo trvlr_section(array(
					'section' => 'inclusions',
					'post_id' => $post_id,
				));
				echo trvlr_section(array(
					'section' => 'faqs',
					'post_id' => $post_id,
				));
				echo trvlr_section(array(
					'section' => 'description',
					'post_id' => $post_id,
				));
				echo trvlr_section(array(
					'section' => 'additional_info',
					'post_id' => $post_id,
					'content' => $additional_info_content,
				));
				?>
			</div>
		</div>
		<aside class="trvlr-single-attraction__sidebar trvlr-sidebar" aria-label="<?php esc_attr_e('Booking', 'trvlr'); ?>">
			<div class="trvlr-single-attraction__sidebar-inner trvlr-sidebar__container">
				<?php echo trvlr_booking_calendar($post_id); ?>
			</div>
		</aside>
	</div>
	<button
		type="button"
		class="trvlr-single-attraction__mobile-availability trvlr-mobile-cta trvlr-check-availability"
		attraction-id="<?php echo esc_attr($attraction_id); ?>"
		<?php if ($group_id) : ?>attraction-group-id="<?php echo esc_attr($group_id); ?>" <?php endif; ?>>
		<span><?php esc_html_e('Check availability', 'trvlr'); ?></span>
		<?php echo trvlr_icon('arrow-right', true); ?>
	</button>
</article>
