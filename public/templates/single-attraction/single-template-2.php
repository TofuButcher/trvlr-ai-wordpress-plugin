<?php

if (!defined('ABSPATH')) {
	exit;
}

$hero_image_id = get_post_thumbnail_id($post_id);
$hero_image = wp_get_attachment_image($hero_image_id, 'full');
$duration_raw = get_trvlr_duration($post_id);
$duration_text = $duration_raw ? apply_filters('trvlr_duration', $duration_raw, $post_id) : '';
$inclusions_out = trvlr_inclusions($post_id);
$has_inclusions = $inclusions_out !== '';
$gallery_out = trvlr_gallery($post_id, array('type' => 'masonry'));
$has_gallery = $gallery_out !== '';
$loc_out = trvlr_locations($post_id);
$add_info_out = trvlr_additional_info($post_id);
$has_additional = ($loc_out !== '' || $add_info_out !== '');
$attraction_id = get_trvlr_attraction_id($post_id);

?>
<article
	id="attraction-<?php echo esc_attr((string) $post_id); ?>"
	class="trvlr-single-attraction trvlr-sa2">
	<section>
		<div class="trvlr-sa2__inner"> <!-- grid columns -->
			<div class="trvlr-sa2__main">
				<!-- Hero -->
				<div class="trvlr-sa2__hero">
					<?php if ($hero_image) : ?>
						<div class="trvlr-sa2__hero-image">
							<?php echo $hero_image; ?>
						</div>
					<?php endif; ?>
					<div class="trvlr-sa2__hero-container">
						<a class="trvlr-sa2__back trvlr-back-link" href="<?php echo esc_url(home_url('/')); ?>">
							<svg class="trvlr-sa2__back-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
								<path d="M3.33991 10.3399C3.30941 10.3094 3.28177 10.277 3.25704 10.2432C3.24984 10.2334 3.2424 10.2237 3.23564 10.2135C3.23002 10.2051 3.22572 10.1959 3.22044 10.1873C3.21202 10.1736 3.20321 10.1601 3.19558 10.1459C3.16305 10.0851 3.14001 10.0208 3.12377 9.95528C3.10802 9.8917 3.09826 9.82554 3.09822 9.7571C3.09816 9.67529 3.11069 9.59363 3.13482 9.51472C3.1475 9.47333 3.16373 9.43359 3.18246 9.39526C3.22151 9.3152 3.27339 9.23941 3.33991 9.1729L9.17285 3.33996C9.49501 3.0178 10.0177 3.0178 10.3398 3.33996C10.6619 3.66213 10.662 4.18484 10.3398 4.50697L5.91491 8.93191H15.5893C16.0449 8.93191 16.4145 9.30149 16.4145 9.7571C16.4142 10.2123 16.0452 10.5814 15.59 10.5816H5.91422L10.3392 15.0065C10.6613 15.3286 10.6618 15.8507 10.3398 16.1728C10.0177 16.495 9.49501 16.495 9.17285 16.1728L3.33991 10.3399Z" fill="currentColor" />
							</svg>
							<?php esc_html_e('Back', 'trvlr'); ?>
						</a>
						<div class="trvlr-sa2__hero-content">
							<div class="trvlr-sa2__hero-title-wrap">
								<?php echo trvlr_title($post_id, 1); ?>
							</div>
							<button type="button" class="trvlr-sa2__hero-cta trvlr-check-availability" attraction-id="<?php echo esc_attr($attraction_id); ?>">
								<?php esc_html_e('Check availability', 'trvlr'); ?>
								<svg class="trvlr-sa2__hero-cta-icon" width="21" height="21" viewBox="0 0 21 21" aria-hidden="true">
									<use href="#icon-arrow-right" />
								</svg>
							</button>
						</div>
					</div>
				</div>
				<div class="trvlr-sa2__content">
					<div class="trvlr-sa2__content-container">
						<div class="trvlr-sa2__intro">
							<?php
							$price_value = get_trvlr_advertised_price_value($post_id);
							if ($price_value) {
							?>
								<div class="trvlr-sa2__price">
									<?php
									$type = get_trvlr_advertised_price_type($post_id);
									echo 'from A$' . esc_html($price_value);
									if ($type) {
										echo ' <span class="trvlr-sa2__price-type">' . esc_html($type) . '</span>';
									}
									?>
								</div>
							<?php
							} elseif (function_exists('trvlr_advertised_price')) {
								$ap = trvlr_advertised_price($post_id);
								if ($ap !== '') {
									echo '<div class="trvlr-sa2__price trvlr-sa2__price--from-helper">' . $ap . '</div>';
								}
							}
							?>
							<?php if ($duration_text) : ?>
								<p class="trvlr-sa2__duration"><?php echo esc_html($duration_text); ?></p>
							<?php endif; ?>
							<?php
							$sale_block = trvlr_sale($post_id);
							if ($sale_block !== '') {
								echo '<div class="trvlr-sa2__sale">' . $sale_block . '</div>';
							}
							?>
						</div>

						<div class="trvlr-sa2-tabs" data-trvlr-sa2-tabs>
							<input class="trvlr-sa2-tabs__state" type="radio" name="trvlr_sa1_tab" id="trvlr_sa1_tab_overview" checked>
							<input class="trvlr-sa2-tabs__state" type="radio" name="trvlr_sa1_tab" id="trvlr_sa1_tab_inclusions">
							<input class="trvlr-sa2-tabs__state" type="radio" name="trvlr_sa1_tab" id="trvlr_sa1_tab_gallery">
							<input class="trvlr-sa2-tabs__state" type="radio" name="trvlr_sa1_tab" id="trvlr_sa1_tab_additional">
							<div class="trvlr-sa2-tabs__nav" role="tablist">
								<label class="trvlr-sa2-tabs__label" for="trvlr_sa1_tab_overview" role="tab"><?php esc_html_e('Overview', 'trvlr'); ?></label>
								<label class="trvlr-sa2-tabs__label" for="trvlr_sa1_tab_inclusions" role="tab"><?php esc_html_e('Inclusions', 'trvlr'); ?></label>
								<label class="trvlr-sa2-tabs__label" for="trvlr_sa1_tab_gallery" role="tab"><?php esc_html_e('Gallery', 'trvlr'); ?></label>
								<label class="trvlr-sa2-tabs__label" for="trvlr_sa1_tab_additional" role="tab"><?php esc_html_e('Additional Info', 'trvlr'); ?></label>
							</div>
							<div class="trvlr-sa2-tabs__panels">
								<div class="trvlr-sa2-tabs__panel trvlr-sa2-tabs__panel--overview" id="trvlr-sa2-panel-overview" role="tabpanel" aria-labelledby="trvlr_sa1_tab_overview" tabindex="0">
									<div class="trvlr-sa2__prose trvlr-sa2__overview">
										<?php echo trvlr_short_description($post_id); ?>
										<?php echo trvlr_description($post_id); ?>
										<?php the_content(); ?>
									</div>
								</div>
								<div class="trvlr-sa2-tabs__panel trvlr-sa2-tabs__panel--inclusions" id="trvlr-sa2-panel-inclusions" role="tabpanel" aria-labelledby="trvlr_sa1_tab_inclusions" tabindex="0">
									<?php
									if ($has_inclusions) {
										echo $inclusions_out;
									} else {
										printf('<p class="trvlr-sa2__empty">%s</p>', esc_html__('No inclusions have been provided for this attraction.', 'trvlr'));
									}
									?>
								</div>
								<div class="trvlr-sa2-tabs__panel trvlr-sa2-tabs__panel--gallery" id="trvlr-sa2-panel-gallery" role="tabpanel" aria-labelledby="trvlr_sa1_tab_gallery" tabindex="0">
									<?php
									if ($has_gallery) {
										echo $gallery_out;
									} else {
										printf('<p class="trvlr-sa2__empty">%s</p>', esc_html__('No images are available in the gallery yet.', 'trvlr'));
									}
									?>
								</div>
								<div class="trvlr-sa2-tabs__panel trvlr-sa2-tabs__panel--additional" id="trvlr-sa2-panel-additional" role="tabpanel" aria-labelledby="trvlr_sa1_tab_additional" tabindex="0">
									<?php
									if ($has_additional) {
										echo $loc_out;
										echo $add_info_out;
									} else {
										printf('<p class="trvlr-sa2__empty">%s</p>', esc_html__('No additional information is available for this attraction.', 'trvlr'));
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<aside class="trvlr-sa2__sidebar" aria-label="<?php esc_attr_e('Booking', 'trvlr'); ?>">
				<div class="trvlr-sa2__sidebar-inner">
					<?php echo trvlr_booking_calendar($post_id); ?>
				</div>
			</aside>
	</section>
</article>