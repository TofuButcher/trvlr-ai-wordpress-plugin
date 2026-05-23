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

	echo trvlr_single_attraction_markup();

endwhile;

if ($is_dev_environment) {
	include $dev_footer_file;
} else {
	get_footer();
}
