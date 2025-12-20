<?php

$is_dev_environment = false;
$dev_header_file = TRVLR_PLUGIN_DIR . '~dev/partials/dev-header.php';
$dev_footer_file = TRVLR_PLUGIN_DIR . '~dev/partials/dev-footer.php';

if (file_exists($dev_header_file)) {
	$is_dev_environment = true;
}



/**
 * Single template for trvlr attraction
 */
if ($is_dev_environment) {
	include $dev_header_file;
} else {
	get_header();
}

while (have_posts()) : the_post();

	$post_id = get_the_ID();
	$attraction_id = get_trvlr_attraction_id($post_id);
	$post_classes = get_post_class('single-attraction');
	$post_class = implode(' ', $post_classes);

?>
	<article id="attraction-<?php echo $post_id; ?>" class="trvlr-single-attraction <?php echo esc_attr($post_class); ?>">
		<section class="trvlr-single-attraction__section">
			<div class="trvlr-single-attraction__inner">
				<div class="trvlr-single-attraction__content">
					<a class="trvlr-back-link" href="/">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
							<path d="M3.33991 10.3399C3.30941 10.3094 3.28177 10.277 3.25704 10.2432C3.24984 10.2334 3.2424 10.2237 3.23564 10.2135C3.23002 10.2051 3.22572 10.1959 3.22044 10.1873C3.21202 10.1736 3.20321 10.1601 3.19558 10.1459C3.16305 10.0851 3.14001 10.0208 3.12377 9.95528C3.10802 9.8917 3.09826 9.82554 3.09822 9.7571C3.09816 9.67529 3.11069 9.59363 3.13482 9.51472C3.1475 9.47333 3.16373 9.43359 3.18246 9.39526C3.22151 9.3152 3.27339 9.23941 3.33991 9.1729L9.17285 3.33996C9.49501 3.0178 10.0177 3.0178 10.3398 3.33996C10.6619 3.66213 10.662 4.18484 10.3398 4.50697L5.91491 8.93191H15.5893C16.0449 8.93191 16.4145 9.30149 16.4145 9.7571C16.4142 10.2123 16.0452 10.5814 15.59 10.5816H5.91422L10.3392 15.0065C10.6613 15.3286 10.6618 15.8507 10.3398 16.1728C10.0177 16.495 9.49501 16.495 9.17285 16.1728L3.33991 10.3399Z" fill="currentColor"></path>
						</svg>
						Back
					</a>
					<?php echo trvlr_title($post_id, 1); ?>
					<div class="trvlr-single-attraction__columns">
						<div class="trvlr-single-attraction__main">
							<div class="trvlr-single-attraction__meta">
								<?php echo trvlr_duration($post_id); ?>
								<?php echo trvlr_sale($post_id); ?>
								<?php echo trvlr_advertised_price($post_id); ?>
								<Button class="trvlr-check-availability" attraction-id="<?php echo $attraction_id; ?>">Check Availability</Button>
							</div>
							<?php echo trvlr_gallery($post_id); ?>
							<?php echo trvlr_short_description($post_id); ?>
							<?php echo trvlr_description($post_id); ?>
							<?php the_content(); ?>
							<?php echo trvlr_accordion($post_id); ?>
						</div>
						<div class="trvlr-single-attraction__sidebar">
							<div class="trvlr-single-attraction__sidebar-inner">
								<?php echo trvlr_advertised_price($post_id); ?>
								<?php echo trvlr_booking_calendar($post_id); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</article>
<?php
endwhile;

if ($is_dev_environment) {
	include $dev_footer_file;
} else {
	get_footer();
}
