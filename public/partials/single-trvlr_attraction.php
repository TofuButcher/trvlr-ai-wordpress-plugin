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
	$trvlr_single_template_slug = Trvlr_Template_Registry::get_active_single_slug();
	$single_template_path = Trvlr_Template_Registry::get_single_template_path();

	include $single_template_path;

endwhile;

if ($is_dev_environment) {
	include $dev_footer_file;
} else {
	get_footer();
}
