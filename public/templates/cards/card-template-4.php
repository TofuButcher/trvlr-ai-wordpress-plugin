<?php

if (!defined('ABSPATH')) {
	exit;
}

$trvlr_card_variant = (isset($trvlr_card_variant) && is_string($trvlr_card_variant) && $trvlr_card_variant !== '') ? $trvlr_card_variant : 'default';
$trvlr_card_supported_variants = array('default', 'expanded');
if (!in_array($trvlr_card_variant, $trvlr_card_supported_variants, true)) {
	$trvlr_card_variant = 'default';
}

$post_id = isset($post_id) ? $post_id : get_the_ID();
$post_id = absint($post_id);
$permalink = get_permalink($post_id);
$title = get_trvlr_title($post_id);
$price = get_trvlr_advertised_price_value($post_id);
$popular_markup = trvlr_popular_badge($post_id, array('icon' => false));

?>
<div class="trvlr-card trvlr-card--attraction<?php echo $trvlr_card_variant !== 'default' ? ' trvlr-card--variant-' . esc_attr($trvlr_card_variant) : ''; ?>">
	<div class="trvlr-card__main-content">
		<div class="trvlr-card__image-wrap">
			<?php if (has_post_thumbnail($post_id)) : ?>
				<?php echo get_the_post_thumbnail($post_id, 'image-480', array('class' => 'trvlr-card__image')); ?>
				<?php if ($popular_markup !== '' && $trvlr_card_variant !== 'expanded') : ?>
					<div class="trvlr-card__badge-on-image">
						<?php echo $popular_markup; ?>
					</div>
				<?php endif; ?>
				<?php if ($trvlr_card_variant === 'expanded') : ?>
					<div class="trvlr-card__badge-on-image">
						<div class="trvlr-popular-badge">
							<span class="trvlr-popular-badge__text">
								Private Bespoke Tour
							</span>
						</div>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<div class="trvlr-card__content">
			<h3 class="trvlr-card__title">
				<a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
			</h3>
			<div class="trvlr-card__meta">
				<?php echo trvlr_duration($post_id); ?>
				<?php echo trvlr_suitable_ages($post_id); ?>
			</div>
			<div class="trvlr-card__footer">
				<?php if ($price) : ?>
					<div class="trvlr-card__price">
						<?php echo esc_html__('starts at', 'trvlr') . ' <strong>A$' . esc_html($price) . '</strong>'; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php $trvlr_highlights_html = trvlr_highlights($post_id, 'trvlr-list-items-trim'); ?>
	<?php if ($trvlr_highlights_html) : ?>
		<div class="trvlr-card__extended-content trvlr-card__hover-content">
			<div class="trvlr-card__hover-content-inner">
				<?php echo wp_kses_post($trvlr_highlights_html); ?>
			</div>
			<?php echo trvlr_booking_button($post_id, array('label' => __('Check availability', 'trvlr'), 'class' => ' trvlr-card__cta')); ?>
		</div>
	<?php endif; ?>
</div>